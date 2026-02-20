import * as fs from 'fs';
import * as path from 'path';
import { Page } from '@playwright/test';
import {
  OnFail,
  RunContext,
  Scenario,
  ScenarioMode,
  ScenarioReport,
  Step,
  StepReport,
  Variable,
} from './types.js';
import { executeStep, interpolate } from './executors.js';
import { buildStepReport } from './reporter.js';

const SCENARIOS_DIR = path.join(process.cwd(), 'tests', 'e2e', 'scenarios');

export function loadScenariosForMode(mode: ScenarioMode): Scenario[] {
  if (!fs.existsSync(SCENARIOS_DIR)) return [];
  return collectScenarios(SCENARIOS_DIR).filter(s => s.mode.includes(mode));
}

function collectScenarios(dir: string): Scenario[] {
  const results: Scenario[] = [];
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory() && entry.name !== 'examples') {
      results.push(...collectScenarios(fullPath));
    } else if (entry.isFile() && entry.name.endsWith('.json')) {
      try {
        const raw = JSON.parse(fs.readFileSync(fullPath, 'utf-8'));
        validateScenario(raw, fullPath);
        results.push(raw as Scenario);
      } catch (err) {
        console.warn(`[scenario-runner] Skipping ${fullPath}: ${(err as Error).message}`);
      }
    }
  }
  return results;
}

function validateScenario(raw: unknown, filePath: string): void {
  if (typeof raw !== 'object' || raw === null) {
    throw new Error(`Not an object in ${filePath}`);
  }
  const s = raw as Record<string, unknown>;
  if (typeof s['scenarioId'] !== 'string' || s['scenarioId'].length === 0) {
    throw new Error(`Missing or empty scenarioId in ${filePath}`);
  }
  if (s['version'] !== 2) {
    throw new Error(`Expected version 2, got ${s['version']} in ${filePath}`);
  }
  if (!Array.isArray(s['mode']) || s['mode'].length === 0) {
    throw new Error(`Missing or empty mode in ${filePath}`);
  }
  if (!Array.isArray(s['steps']) || s['steps'].length === 0) {
    throw new Error(`Missing or empty steps in ${filePath}`);
  }
}

function resolveVariables(scenario: Scenario): Record<string, string> {
  const vars: Record<string, string> = {};
  if (!scenario.variables) return vars;

  for (const [name, def] of Object.entries(scenario.variables)) {
    vars[name] = resolveVariable(name, def);
  }
  return vars;
}

function resolveVariable(name: string, def: Variable): string {
  switch (def.source) {
    case 'env': {
      const val = process.env[def.key];
      if (val !== undefined) return val;
      if (def.default !== undefined) return def.default;
      console.warn(`[scenario-runner] Env var ${def.key} not set and no default for variable "${name}"`);
      return '';
    }
    case 'literal':
      return def.value;
    case 'generated':
      return generateValue(def.type, def.length);
  }
}

function generateValue(type: 'uuid' | 'timestamp' | 'random_string', length?: number): string {
  switch (type) {
    case 'uuid':
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
        const r = (Math.random() * 16) | 0;
        const v = c === 'x' ? r : (r & 0x3) | 0x8;
        return v.toString(16);
      });
    case 'timestamp':
      return Date.now().toString();
    case 'random_string': {
      const len = length ?? 8;
      const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
      return Array.from({ length: len }, () => chars[Math.floor(Math.random() * chars.length)]).join('');
    }
  }
}

function topologicalSort(steps: Step[]): Step[] {
  const stepMap = new Map<string, Step>();
  const inDegree = new Map<string, number>();
  const dependents = new Map<string, string[]>();

  for (const step of steps) {
    stepMap.set(step.id, step);
    inDegree.set(step.id, step.dependsOn?.length ?? 0);
    if (!dependents.has(step.id)) dependents.set(step.id, []);
    for (const dep of step.dependsOn ?? []) {
      if (!dependents.has(dep)) dependents.set(dep, []);
      dependents.get(dep)!.push(step.id);
    }
  }

  const queue = steps.filter(s => (s.dependsOn?.length ?? 0) === 0);
  const sorted: Step[] = [];

  while (queue.length > 0) {
    const step = queue.shift()!;
    sorted.push(step);
    for (const depId of dependents.get(step.id) ?? []) {
      const newDegree = (inDegree.get(depId) ?? 1) - 1;
      inDegree.set(depId, newDegree);
      if (newDegree === 0) queue.push(stepMap.get(depId)!);
    }
  }

  if (sorted.length !== steps.length) {
    const cyclic = steps.filter(s => !sorted.includes(s)).map(s => s.id);
    throw new Error(`Circular dependency detected in steps: ${cyclic.join(', ')}`);
  }

  return sorted;
}

async function runStepWithRetry(
  page: Page,
  step: Step,
  ctx: RunContext,
  retryCount: number,
): Promise<{ error?: unknown; retries: number }> {
  let lastError: unknown;
  for (let attempt = 0; attempt <= retryCount; attempt++) {
    try {
      await executeStep(page, step, ctx);
      return { retries: attempt };
    } catch (err) {
      lastError = err;
      if (attempt < retryCount) {
        await new Promise(r => setTimeout(r, 1000));
      }
    }
  }
  return { error: lastError, retries: retryCount };
}

export async function runScenario(page: Page, scenario: Scenario): Promise<ScenarioReport> {
  const startTime = Date.now();
  const vars = resolveVariables(scenario);
  const ctx: RunContext = { vars, stepResults: {} };
  const stepReports: StepReport[] = [];

  const defaults = scenario.defaults ?? {};
  const defaultTimeout = defaults.timeout ?? 30_000;
  const defaultRetry = defaults.retry ?? 0;
  const defaultOnFail: OnFail = defaults.onFail ?? 'stop';

  let sortedSteps: Step[];
  try {
    sortedSteps = topologicalSort(scenario.steps);
  } catch (err) {
    throw new Error(`[${scenario.scenarioId}] ${(err as Error).message}`);
  }

  const skippedIds = new Set<string>();

  const runSteps = async (steps: Step[]): Promise<void> => {
    for (const step of steps) {
      if (skippedIds.has(step.id)) {
        stepReports.push(buildStepReport(step.id, step.description, 'skipped', 0, 0));
        ctx.stepResults[step.id] = 'skipped';
        continue;
      }

      const dependsFailed = (step.dependsOn ?? []).some(dep => ctx.stepResults[dep] === 'failed');
      if (dependsFailed) {
        skippedIds.add(step.id);
        stepReports.push(buildStepReport(step.id, step.description, 'skipped', 0, 0));
        ctx.stepResults[step.id] = 'skipped';
        continue;
      }

      const retry = step.retry ?? defaultRetry;
      const onFail: OnFail = step.onFail ?? defaultOnFail;

      page.setDefaultTimeout(step.timeout ?? defaultTimeout);

      const stepStart = Date.now();
      const { error, retries } = await runStepWithRetry(page, step, ctx, retry);
      const duration = Date.now() - stepStart;

      if (error) {
        ctx.stepResults[step.id] = 'failed';
        stepReports.push(buildStepReport(step.id, step.description, 'failed', duration, retries, error));

        if (onFail === 'stop') {
          throw error;
        }

        if (onFail === 'skip_dependents') {
          markDependentsSkipped(step.id, steps, skippedIds);
        }
      } else {
        ctx.stepResults[step.id] = 'passed';
        stepReports.push(buildStepReport(step.id, step.description, 'passed', duration, retries));
      }
    }
  };

  if (scenario.beforeAll && scenario.beforeAll.length > 0) {
    const sortedBefore = topologicalSort(scenario.beforeAll);
    await runSteps(sortedBefore);
  }

  let scenarioError: unknown;
  try {
    await runSteps(sortedSteps);
  } catch (err) {
    scenarioError = err;
  }

  if (scenario.afterAll && scenario.afterAll.length > 0) {
    const sortedAfter = topologicalSort(scenario.afterAll);
    try {
      await runSteps(sortedAfter);
    } catch (err) {
      console.warn(`[${scenario.scenarioId}] afterAll failed: ${(err as Error).message}`);
    }
  }

  const hasFailed = stepReports.some(r => r.status === 'failed');
  const status = hasFailed ? 'failed' : 'passed';

  const report: ScenarioReport = {
    scenarioId: scenario.scenarioId,
    status,
    duration: Date.now() - startTime,
    mode: scenario.mode,
    tags: scenario.tags ?? [],
    runAt: new Date().toISOString(),
    steps: stepReports,
  };

  if (scenarioError) throw scenarioError;

  return report;
}

function markDependentsSkipped(failedId: string, steps: Step[], skippedIds: Set<string>): void {
  for (const step of steps) {
    if (step.dependsOn?.includes(failedId) && !skippedIds.has(step.id)) {
      skippedIds.add(step.id);
      markDependentsSkipped(step.id, steps, skippedIds);
    }
  }
}

export function interpolateScenarioId(scenarioId: string): string {
  return scenarioId.replace(/[^a-zA-Z0-9._-]/g, '_');
}

import * as fs from 'fs';
import * as path from 'path';
import { ScenarioReport, StepReport } from './types.js';

const ARTIFACTS_DIR = path.join(process.cwd(), 'artifacts', 'browser-playwright');

function ensureDir(dir: string): void {
  if (!fs.existsSync(dir)) {
    fs.mkdirSync(dir, { recursive: true });
  }
}

export function writeJsonReport(report: ScenarioReport): string {
  ensureDir(ARTIFACTS_DIR);
  const filename = `${report.scenarioId}.${Date.now()}.json`;
  const filepath = path.join(ARTIFACTS_DIR, filename);
  fs.writeFileSync(filepath, JSON.stringify(report, null, 2), 'utf-8');
  return filepath;
}

export function writeMdReport(report: ScenarioReport): string {
  ensureDir(ARTIFACTS_DIR);
  const filename = `${report.scenarioId}.${Date.now()}.md`;
  const filepath = path.join(ARTIFACTS_DIR, filename);
  fs.writeFileSync(filepath, buildMdContent(report), 'utf-8');
  return filepath;
}

export function writeSummaryReports(reports: ScenarioReport[]): void {
  ensureDir(ARTIFACTS_DIR);

  const allPassed = reports.every(r => r.status === 'passed');
  const summary = {
    status: allPassed ? 'passed' : 'failed',
    total: reports.length,
    passed: reports.filter(r => r.status === 'passed').length,
    failed: reports.filter(r => r.status === 'failed').length,
    skipped: reports.filter(r => r.status === 'skipped').length,
    duration: reports.reduce((sum, r) => sum + r.duration, 0),
    runAt: new Date().toISOString(),
    scenarios: reports,
  };

  fs.writeFileSync(
    path.join(ARTIFACTS_DIR, 'browser-deterministic.json'),
    JSON.stringify(summary, null, 2),
    'utf-8',
  );

  fs.writeFileSync(
    path.join(ARTIFACTS_DIR, 'browser-deterministic.md'),
    buildSummaryMd(reports),
    'utf-8',
  );
}

function buildMdContent(report: ScenarioReport): string {
  const statusIcon = report.status === 'passed' ? '✓' : report.status === 'failed' ? '✗' : '⊘';
  const lines: string[] = [
    `# E2E Scenario: ${report.scenarioId}`,
    '',
    `**Status:** ${statusIcon} ${report.status}`,
    `**Duration:** ${report.duration}ms`,
    `**Mode:** ${report.mode.join(', ')}`,
    `**Tags:** ${report.tags.length > 0 ? report.tags.join(', ') : '—'}`,
    `**Run at:** ${report.runAt}`,
    '',
    '## Steps',
    '',
    '| Step | Description | Status | Duration | Retries |',
    '|------|-------------|--------|----------|---------|',
  ];

  for (const step of report.steps) {
    const icon = step.status === 'passed' ? '✓' : step.status === 'failed' ? '✗' : '⊘';
    lines.push(
      `| \`${step.stepId}\` | ${step.description ?? '—'} | ${icon} ${step.status} | ${step.duration}ms | ${step.retries} |`,
    );
  }

  const failedSteps = report.steps.filter(s => s.status === 'failed');
  if (failedSteps.length > 0) {
    lines.push('', '## Failures', '');
    for (const step of failedSteps) {
      lines.push(`### Step: \`${step.stepId}\``, '', '```', step.error ?? 'Unknown error', '```', '');
    }
  }

  if (report.artifacts) {
    lines.push('', '## Artifacts', '');
    if (report.artifacts.trace) lines.push(`- Trace: \`${report.artifacts.trace}\``);
    if (report.artifacts.screenshot) lines.push(`- Screenshot: \`${report.artifacts.screenshot}\``);
  }

  return lines.join('\n');
}

function buildSummaryMd(reports: ScenarioReport[]): string {
  const passed = reports.filter(r => r.status === 'passed').length;
  const failed = reports.filter(r => r.status === 'failed').length;
  const skipped = reports.filter(r => r.status === 'skipped').length;
  const totalDuration = reports.reduce((sum, r) => sum + r.duration, 0);
  const overallStatus = failed > 0 ? '✗ FAILED' : '✓ PASSED';

  const lines: string[] = [
    '# E2E Test Run Summary',
    '',
    `**Overall:** ${overallStatus}`,
    `**Total:** ${reports.length} | ✓ ${passed} passed | ✗ ${failed} failed | ⊘ ${skipped} skipped`,
    `**Total duration:** ${totalDuration}ms`,
    `**Run at:** ${new Date().toISOString()}`,
    '',
    '## Scenarios',
    '',
    '| Scenario | Status | Duration | Mode |',
    '|----------|--------|----------|------|',
  ];

  for (const r of reports) {
    const icon = r.status === 'passed' ? '✓' : r.status === 'failed' ? '✗' : '⊘';
    lines.push(`| \`${r.scenarioId}\` | ${icon} ${r.status} | ${r.duration}ms | ${r.mode.join(', ')} |`);
  }

  const failedScenarios = reports.filter(r => r.status === 'failed');
  if (failedScenarios.length > 0) {
    lines.push('', '## Failed scenarios', '');
    for (const r of failedScenarios) {
      const failedStep = r.steps.find(s => s.status === 'failed');
      lines.push(
        `- **${r.scenarioId}** — failed at step \`${failedStep?.stepId ?? '?'}\`: ${failedStep?.error ?? 'Unknown error'}`,
      );
    }
  }

  return lines.join('\n');
}

export function buildStepReport(
  stepId: string,
  description: string | undefined,
  status: StepReport['status'],
  duration: number,
  retries: number,
  error?: unknown,
): StepReport {
  return {
    stepId,
    description,
    status,
    duration,
    retries,
    error: error instanceof Error ? error.message : error != null ? String(error) : undefined,
  };
}

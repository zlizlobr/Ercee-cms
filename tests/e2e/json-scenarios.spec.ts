import { test } from '@playwright/test';
import { ScenarioMode, ScenarioReport } from './runner/types.js';
import { loadScenariosForMode, runScenario } from './runner/scenario-runner.js';
import { writeJsonReport, writeMdReport, writeSummaryReports } from './runner/reporter.js';

const mode = (process.env['E2E_MODE'] ?? 'pr') as ScenarioMode;
const scenarios = loadScenariosForMode(mode);

if (scenarios.length === 0) {
  test(`no JSON scenarios found for mode="${mode}"`, () => {
    console.warn(`No scenarios in tests/e2e/scenarios/ match mode="${mode}"`);
  });
}

const allReports: ScenarioReport[] = [];

for (const scenario of scenarios) {
  test(`[json] ${scenario.scenarioId}`, async ({ page }) => {
    const report = await runScenario(page, scenario);

    writeJsonReport(report);
    writeMdReport(report);
    allReports.push(report);
  });
}

test.afterAll(() => {
  if (allReports.length > 0) {
    writeSummaryReports(allReports);
  }
});

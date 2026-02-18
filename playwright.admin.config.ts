import { defineConfig, devices } from '@playwright/test';

const PORT = Number(process.env.PLAYWRIGHT_CMS_PORT ?? 8000);
const HOST = process.env.PLAYWRIGHT_CMS_HOST ?? '127.0.0.1';
const baseURL = process.env.PLAYWRIGHT_CMS_BASE_URL ?? `http://${HOST}:${PORT}`;

export default defineConfig({
  testDir: './tests/e2e',
  fullyParallel: false,
  timeout: 45_000,
  expect: {
    timeout: 10_000,
  },
  reporter: [['list']],
  use: {
    baseURL,
    trace: 'retain-on-failure',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
  webServer: process.env.PLAYWRIGHT_CMS_BASE_URL
    ? undefined
    : {
        command: `php artisan serve --host ${HOST} --port ${PORT}`,
        url: `${baseURL}/admin/login`,
        reuseExistingServer: true,
        timeout: 120_000,
      },
});

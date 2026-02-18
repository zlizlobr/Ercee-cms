import { expect, test } from '@playwright/test';

test.describe('cms admin login (P1)', () => {
  test('loads /admin/login and signs in with seeded user', async ({ page, baseURL }) => {
    const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'admin@example.com';
    const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'password';

    const response = await page.goto('/admin/login');

    expect(response).not.toBeNull();
    expect(response?.ok()).toBeTruthy();

    await page.fill('input[id="data.email"]', adminEmail);
    await page.fill('input[id="data.password"]', adminPassword);
    await page.locator('button[type="submit"]').click();

    await page.waitForURL(/\/admin(\/.*)?$/);
    await expect(page).not.toHaveURL(/\/admin\/login$/);

    if (baseURL) {
      expect(page.url().startsWith(baseURL)).toBeTruthy();
    }
  });
});

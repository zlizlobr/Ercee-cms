import { expect, Page } from '@playwright/test';
import {
  Assertion,
  Save,
  Selector,
  Step,
  Wait,
  RunContext,
} from './types.js';

export function resolveSelector(page: Page, selector: Selector) {
  switch (selector.type) {
    case 'css':
      return page.locator(selector.value);
    case 'text':
      return page.getByText(selector.value);
    case 'role':
      return page.getByRole(selector.value as Parameters<typeof page.getByRole>[0], {
        name: selector.name,
      });
    case 'testId':
      return page.getByTestId(selector.value);
    case 'xpath':
      return page.locator(`xpath=${selector.value}`);
  }
}

export async function executeWait(page: Page, wait: Wait, ctx: RunContext): Promise<void> {
  if (wait.type === 'url') {
    const url = interpolate(wait.value, ctx.vars);
    await page.waitForURL(new RegExp(url.startsWith('/') ? url.replace(/\//g, '\\/') : url));
    return;
  }

  if (wait.type === 'loadstate') {
    await page.waitForLoadState(wait.value);
    return;
  }

  const locator = resolveSelector(page, wait.selector);
  const timeout = wait.timeout;

  switch (wait.type) {
    case 'visible':
      await locator.waitFor({ state: 'visible', timeout });
      break;
    case 'hidden':
      await locator.waitFor({ state: 'hidden', timeout });
      break;
    case 'attached':
      await locator.waitFor({ state: 'attached', timeout });
      break;
    case 'detached':
      await locator.waitFor({ state: 'detached', timeout });
      break;
  }
}

export async function executeAssertions(
  page: Page,
  assertions: Assertion[],
  ctx: RunContext,
): Promise<void> {
  for (const assertion of assertions) {
    switch (assertion.type) {
      case 'text': {
        const el = resolveSelector(page, assertion.selector);
        await expect(el).toContainText(interpolate(assertion.value, ctx.vars));
        break;
      }
      case 'visible': {
        const el = resolveSelector(page, assertion.selector);
        await expect(el).toBeVisible();
        break;
      }
      case 'hidden': {
        const el = resolveSelector(page, assertion.selector);
        await expect(el).toBeHidden();
        break;
      }
      case 'count': {
        const el = resolveSelector(page, assertion.selector);
        await expect(el).toHaveCount(assertion.value);
        break;
      }
      case 'url': {
        const pattern = interpolate(assertion.value, ctx.vars);
        await expect(page).toHaveURL(new RegExp(pattern));
        break;
      }
      case 'attribute': {
        const el = resolveSelector(page, assertion.selector);
        await expect(el).toHaveAttribute(
          assertion.name,
          interpolate(assertion.value, ctx.vars),
        );
        break;
      }
    }
  }
}

export async function executeSave(page: Page, save: Save, ctx: RunContext): Promise<void> {
  let value: string;

  switch (save.source) {
    case 'url':
      value = page.url();
      break;
    case 'text': {
      if (!save.selector) throw new Error('save.selector is required for source=text');
      value = (await resolveSelector(page, save.selector).textContent()) ?? '';
      break;
    }
    case 'value': {
      if (!save.selector) throw new Error('save.selector is required for source=value');
      value = (await resolveSelector(page, save.selector).inputValue()) ?? '';
      break;
    }
    case 'attribute': {
      if (!save.selector) throw new Error('save.selector is required for source=attribute');
      if (!save.attribute) throw new Error('save.attribute is required for source=attribute');
      value = (await resolveSelector(page, save.selector).getAttribute(save.attribute)) ?? '';
      break;
    }
  }

  ctx.vars[save.targetVar] = value;
}

export async function executeStep(page: Page, step: Step, ctx: RunContext): Promise<void> {
  switch (step.action) {
    case 'goto': {
      const url = interpolate(step.url, ctx.vars);
      await page.goto(url);
      break;
    }
    case 'fill': {
      const value = interpolate(step.value, ctx.vars);
      const locator = resolveSelector(page, step.selector);
      await locator.fill(value);
      break;
    }
    case 'click': {
      const locator = resolveSelector(page, step.selector);
      await locator.click();
      break;
    }
    case 'select': {
      const value = interpolate(step.value, ctx.vars);
      const locator = resolveSelector(page, step.selector);
      await locator.selectOption(value);
      break;
    }
    case 'waitFor': {
      const wait = step.wait;
      await executeWait(page, wait, ctx);
      break;
    }
    case 'assert': {
      await executeAssertions(page, step.assertions, ctx);
      break;
    }
    case 'save': {
      await executeSave(page, step.save, ctx);
      break;
    }
    case 'loginAdmin': {
      const email = step.email
        ? interpolate(step.email, ctx.vars)
        : (ctx.vars['adminEmail'] ?? process.env['E2E_ADMIN_EMAIL'] ?? 'admin@example.com');
      const password = step.password
        ? interpolate(step.password, ctx.vars)
        : (ctx.vars['adminPassword'] ?? process.env['E2E_ADMIN_PASSWORD'] ?? 'password');

      await page.goto('/admin/login');
      await page.locator('input[id="data.email"]').fill(email);
      await page.locator('input[id="data.password"]').fill(password);
      await page.locator('button[type="submit"]').click();
      await page.waitForURL(/\/admin(\/.*)?$/);
      await expect(page).not.toHaveURL(/\/admin\/login$/);
      break;
    }
    case 'openResource': {
      const id = step.id !== undefined ? `/${step.id}` : '';
      await page.goto(`/admin/${step.resource}${id}`);
      await page.waitForLoadState('networkidle');
      break;
    }
    case 'deleteRecordByTitle': {
      await page.goto(`/admin/${step.resource}`);
      await page.waitForLoadState('networkidle');

      const title = interpolate(step.title, ctx.vars);
      const row = page.locator('tr', { hasText: title });
      await expect(row).toBeVisible();

      const deleteBtn = row.locator('[data-action="delete"], button[wire\\:click*="delete"]').first();
      if (await deleteBtn.isVisible()) {
        await deleteBtn.click();
      } else {
        const actionsBtn = row.locator('.fi-dropdown-trigger, button.fi-btn').last();
        await actionsBtn.click();
        await page.getByRole('menuitem', { name: /delete/i }).click();
      }

      const modal = page.locator('.fi-modal, [role="dialog"]');
      if (await modal.isVisible()) {
        await modal.getByRole('button', { name: /delete|confirm/i }).click();
      }

      await expect(row).not.toBeVisible();
      break;
    }
  }

  if (step.wait && step.action !== 'waitFor') {
    await executeWait(page, step.wait, ctx);
  }

  if (step.assert && step.assert.length > 0) {
    await executeAssertions(page, step.assert, ctx);
  }

  if (step.save) {
    await executeSave(page, step.save, ctx);
  }
}

export function interpolate(template: string, vars: Record<string, string>): string {
  return template.replace(/\{\{(\w+)\}\}/g, (_, key: string) => vars[key] ?? `{{${key}}}`);
}

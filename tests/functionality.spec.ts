import { test, expect } from '@playwright/test';

// ───────────────────────────────────────────────────────────
// FUNCTIONALITY TESTS – verify user flows and interactions
// ───────────────────────────────────────────────────────────

test.describe('Functionality Tests', () => {

  // ── 1  Login success ───────────────────────────────────
  test('1 – Login with valid LDCU email navigates to home', async ({ page }) => {
    page.on('dialog', dialog => dialog.dismiss());

    await page.goto('/login');
    await page.fill('input[type="email"]', 'test@liceo.edu.ph');
    await page.fill('input[type="password"]', 'Password1');
    await page.click('button.primary-button');

    await expect(page).toHaveURL(/\/home$/);
  });

  // ── 2  Login validation ───────────────────────────────
  test('2 – Login shows alert for empty fields and wrong email domain', async ({ page }) => {
    // Empty fields
    let alertMessage = '';
    page.on('dialog', async dialog => {
      alertMessage = dialog.message();
      await dialog.dismiss();
    });

    await page.goto('/login');
    await page.click('button.primary-button');
    expect(alertMessage).toContain('Please enter your LDCU email and password');

    // Wrong domain
    await page.fill('input[type="email"]', 'user@gmail.com');
    await page.fill('input[type="password"]', 'Password1');
    await page.click('button.primary-button');
    expect(alertMessage).toContain('official LDCU email');
  });

  // ── 3  Password visibility toggle ─────────────────────
  test('3 – Login password toggle switches field visibility', async ({ page }) => {
    await page.goto('/login');

    const passwordInput = page.locator('input[type="password"]');
    const toggle = page.locator('.password-toggle');

    await expect(passwordInput).toBeVisible();
    await expect(toggle).toHaveText('◌');

    await toggle.click();
    await expect(page.locator('input[type="text"]').last()).toBeVisible();
    await expect(toggle).toHaveText('◉');

    await toggle.click();
    await expect(page.locator('input[type="password"]')).toBeVisible();
    await expect(toggle).toHaveText('◌');
  });

  // ── 4  Register success ───────────────────────────────
  test('4 – Register with valid data redirects to login', async ({ page }) => {
    let alertMessage = '';
    page.on('dialog', async dialog => {
      alertMessage = dialog.message();
      await dialog.accept();
    });

    await page.goto('/register');
    await page.fill('input[placeholder="Juan Dela Cruz"]', 'Test User');
    await page.fill('input[placeholder="2021-00001"]', '2021-00001');
    await page.fill('input[placeholder="yourname@liceo.edu.ph"]', 'test@liceo.edu.ph');
    await page.fill('input[placeholder="Create a strong password"]', 'StrongPass1');
    await page.fill('input[placeholder="Re-enter your password"]', 'StrongPass1');
    await page.check('.checkbox-row input[type="checkbox"]');
    await page.click('button.primary-button');

    expect(alertMessage).toContain('Account created successfully');
    await expect(page).toHaveURL(/\/login$/);
  });

  // ── 5  Register validation ────────────────────────────
  test('5 – Register shows alerts for empty fields, wrong email, short password, mismatch', async ({ page }) => {
    let alertMessage = '';
    page.on('dialog', async dialog => {
      alertMessage = dialog.message();
      await dialog.dismiss();
    });

    await page.goto('/register');

    // Empty fields
    await page.click('button.primary-button');
    expect(alertMessage).toContain('Please complete all fields');

    // Fill all fields first, then test wrong email
    await page.fill('input[placeholder="Juan Dela Cruz"]', 'Test User');
    await page.fill('input[placeholder="2021-00001"]', '2021-00001');
    await page.fill('input[placeholder="yourname@liceo.edu.ph"]', 'user@gmail.com');
    await page.fill('input[placeholder="Create a strong password"]', 'StrongPass1');
    await page.fill('input[placeholder="Re-enter your password"]', 'StrongPass1');
    await page.click('button.primary-button');
    expect(alertMessage).toContain('official LDCU email address');

    // Short password
    await page.fill('input[placeholder="yourname@liceo.edu.ph"]', 'test@liceo.edu.ph');
    await page.fill('input[placeholder="Create a strong password"]', 'abc');
    await page.fill('input[placeholder="Re-enter your password"]', 'abc');
    await page.click('button.primary-button');
    expect(alertMessage).toContain('Password must have at least 8 characters');

    // Password mismatch
    await page.fill('input[placeholder="Create a strong password"]', 'StrongPass1');
    await page.fill('input[placeholder="Re-enter your password"]', 'Different1');
    await page.click('button.primary-button');
    expect(alertMessage).toContain('Passwords do not match');
  });

  // ── 6  Search text filter ─────────────────────────────
  test('6 – Search input filters locations by text', async ({ page }) => {
    await page.goto('/search');

    // All 6 visible
    await expect(page.locator('.search-location-card')).toHaveCount(6);

    // Type "West"
    await page.fill('input[type="search"]', 'West');
    await page.locator('.search-box').evaluate(form => (form as HTMLFormElement).requestSubmit());

    const results = page.locator('.search-location-card');
    await expect(results).toHaveCount(1);
    await expect(results.first()).toContainText('West Academic Cluster');

    // Unmatched query → no results
    await page.fill('input[type="search"]', 'xyznonexistent');
    await page.locator('.search-box').evaluate(form => (form as HTMLFormElement).requestSubmit());
    await expect(page.locator('.no-results')).toHaveText('No locations found.');
  });

  // ── 7  Search category filter ─────────────────────────
  test('7 – Search category buttons filter locations by category', async ({ page }) => {
    await page.goto('/search');

    // All
    await expect(page.locator('.search-location-card')).toHaveCount(6);

    // Academic → 4
    await page.locator('.category-button').filter({ hasText: 'Academic' }).click();
    await expect(page.locator('.category-button.active')).toHaveText('Academic');
    await expect(page.locator('.search-location-card')).toHaveCount(4);

    // Facilities → 2
    await page.locator('.category-button').filter({ hasText: 'Facilities' }).click();
    await expect(page.locator('.category-button.active')).toHaveText('Facilities');
    await expect(page.locator('.search-location-card')).toHaveCount(2);

    // Back to All → 6
    await page.locator('.category-button').filter({ hasText: /^All$/ }).click();
    await expect(page.locator('.category-button.active')).toHaveText('All');
    await expect(page.locator('.search-location-card')).toHaveCount(6);
  });

  // ── 8  Saved locations removal ────────────────────────
  test('8 – Saved page allows removing locations and shows empty state', async ({ page }) => {
    await page.goto('/saved');

    await expect(page.locator('.saved-location-card')).toHaveCount(2);

    // Remove first
    await page.locator('.saved-heart').first().click();
    await expect(page.locator('.saved-location-card')).toHaveCount(1);

    // Remove last
    await page.locator('.saved-heart').first().click();
    await expect(page.locator('.empty-saved')).toBeVisible();
    await expect(page.locator('.empty-saved h2')).toHaveText('No saved locations');
  });

  // ── 9  Forgot password flow ───────────────────────────
  test('9 – Forgot password shows error for invalid email and success for valid', async ({ page }) => {
    await page.goto('/forgot-password');

    // Empty email → error
    await page.click('.forgot-submit');
    await expect(page.locator('.forgot-error')).toContainText('Please enter your LDCU email address');

    // Wrong domain → error
    await page.fill('#forgot-email', 'user@gmail.com');
    await page.click('.forgot-submit');
    await expect(page.locator('.forgot-error')).toContainText('official LDCU email');

    // Valid → success state
    await page.fill('#forgot-email', 'test@liceo.edu.ph');
    await page.click('.forgot-submit');
    await expect(page.locator('h1')).toHaveText('Check Your Email');
    await expect(page.locator('.reset-email')).toHaveText('test@liceo.edu.ph');
  });

  // ── 10  Navigation and page routing ───────────────────
  test('10 – Bottom nav links and back buttons navigate correctly', async ({ page }) => {
    // Bottom nav routes
    await page.goto('/home');

    await page.locator('a.nav-item', { hasText: 'Map' }).click();
    await expect(page).toHaveURL(/\/map$/);

    await page.locator('a.nav-item', { hasText: 'Search' }).click();
    await expect(page).toHaveURL(/\/search$/);

    await page.locator('a.nav-item', { hasText: 'Saved' }).click();
    await expect(page).toHaveURL(/\/saved$/);

    await page.locator('a.nav-item', { hasText: 'Profile' }).click();
    await expect(page).toHaveURL(/\/profile$/);

    // Account Settings back → Profile
    await page.goto('/account-settings');
    await page.click('.settings-back');
    await expect(page).toHaveURL(/\/profile$/);

    // Map search button → Search
    await page.goto('/map');
    await page.click('.map-search-button');
    await expect(page).toHaveURL(/\/search$/);

    // Map location query param
    await page.goto('/map?location=Rodolsa%20Hall');
    await expect(page.locator('.selected-location-row strong')).toHaveText('Rodolsa Hall');
  });

});

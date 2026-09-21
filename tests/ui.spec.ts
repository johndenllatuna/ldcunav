import { test, expect } from '@playwright/test';

// ───────────────────────────────────────────────────────────
// UI TESTS – verify elements, layout, text, and visibility
// ───────────────────────────────────────────────────────────

test.describe('UI Tests', () => {

  // ── 1  Login page UI ────────────────────────────────────
  test('1 – Login page displays all expected UI elements', async ({ page }) => {
    await page.goto('/login');

    // Brand
    await expect(page.locator('.brand-small strong')).toHaveText('LDCUNav');

    // Heading
    await expect(page.locator('.auth-content h1')).toHaveText('Welcome Back');

    // Subtitle
    await expect(page.locator('.auth-subtitle')).toContainText('Log in using your LDCU account');

    // Email field
    const emailInput = page.locator('input[type="email"]');
    await expect(emailInput).toBeVisible();
    await expect(emailInput).toHaveAttribute('placeholder', 'yourname@liceo.edu.ph');

    // Password field
    const passwordInput = page.locator('input[type="password"]');
    await expect(passwordInput).toBeVisible();
    await expect(passwordInput).toHaveAttribute('placeholder', 'Enter your password');

    // Submit button
    await expect(page.locator('button.primary-button')).toHaveText('Log In');

    // Forgot-password link
    await expect(page.locator('a.forgot')).toHaveText('Forgot Password?');

    // Sign-up link
    await expect(page.locator('.auth-footer')).toContainText("Don't have an account?");
    await expect(page.locator('.auth-footer a')).toHaveText('Sign Up');
  });

  // ── 2  Navigation menu UI ───────────────────────────────
  test('2 – Bottom navigation shows all items on dashboard', async ({ page }) => {
    await page.goto('/home');

    const nav = page.locator('nav.bottom-nav');
    await expect(nav).toBeVisible();

    // All 5 nav links
    const items = ['Home', 'Map', 'Search', 'Saved', 'Profile'];
    for (const label of items) {
      const link = nav.locator('.nav-item', { hasText: label });
      await expect(link).toBeVisible();
      await expect(link).toContainText(label);
    }

    // Each is a <RouterLink> (renders <a>)
    const links = nav.locator('.nav-item');
    await expect(links).toHaveCount(5);
  });

  // ── 3  Navigation hidden on auth pages ─────────────────
  test('3 – Bottom navigation is hidden on login page', async ({ page }) => {
    await page.goto('/login');
    await expect(page.locator('nav.bottom-nav')).not.toBeVisible();

    await page.goto('/register');
    await expect(page.locator('nav.bottom-nav')).not.toBeVisible();

    await page.goto('/forgot-password');
    await expect(page.locator('nav.bottom-nav')).not.toBeVisible();
  });

  // ── 4  Register page form UI ───────────────────────────
  test('4 – Register page displays form with all fields', async ({ page }) => {
    await page.goto('/register');

    await expect(page.locator('.auth-content h1')).toHaveText('Create Your LDCUNav Account');

    // Info box
    await expect(page.locator('.info-box')).toContainText('official LDCU email');

    // Labels
    await expect(page.locator('label', { hasText: 'FULL NAME' })).toBeVisible();
    await expect(page.locator('label', { hasText: 'STUDENT ID NUMBER' })).toBeVisible();
    await expect(page.locator('label', { hasText: 'LDCU EMAIL' })).toBeVisible();
    await expect(page.getByText('PASSWORD', { exact: true }).first()).toBeVisible();
    await expect(page.locator('label', { hasText: 'CONFIRM PASSWORD' })).toBeVisible();

    // Input placeholders
    await expect(page.locator('input[placeholder="Juan Dela Cruz"]')).toBeVisible();
    await expect(page.locator('input[placeholder="2021-00001"]')).toBeVisible();
    await expect(page.locator('input[placeholder="yourname@liceo.edu.ph"]')).toBeVisible();
    await expect(page.locator('input[placeholder="Create a strong password"]')).toBeVisible();
    await expect(page.locator('input[placeholder="Re-enter your password"]')).toBeVisible();

    // Password rules
    await expect(page.locator('.password-rules')).toContainText('At least 8 characters');
    await expect(page.locator('.password-rules')).toContainText('One uppercase letter');
    await expect(page.locator('.password-rules')).toContainText('One number');

    // Terms checkbox
    await expect(page.locator('.checkbox-row')).toContainText('Terms of Service');

    // Submit button
    await expect(page.locator('button.primary-button')).toHaveText('Create Account');
  });

  // ── 5  Search page UI ──────────────────────────────────
  test('5 – Search page shows search bar and category filters', async ({ page }) => {
    await page.goto('/search');

    // Header
    await expect(page.locator('.search-header h1')).toHaveText('Search');
    await expect(page.locator('.search-header span')).toHaveText('Find your destination');

    // Search input
    const searchInput = page.locator('input[type="search"]');
    await expect(searchInput).toBeVisible();
    await expect(searchInput).toHaveAttribute('placeholder', 'Search buildings, offices...');

    // Category buttons
    const cats = ['All', 'Academic', 'Facilities', 'Offices', 'Services'];
    for (const cat of cats) {
      await expect(
        page.locator('.search-categories .category-button', { hasText: cat })
      ).toBeVisible();
    }

    // "All" is active by default
    await expect(
      page.locator('.search-categories .category-button.active')
    ).toHaveText('All');

    // Results heading
    await expect(page.locator('.search-results h2')).toHaveText('Locations');
  });

  // ── 6  Home page search box UI ─────────────────────────
  test('6 – Home page search box and location cards are visible', async ({ page }) => {
    await page.goto('/home');

    // Greeting
    await expect(page.locator('.greeting h1')).toContainText('Student');

    // Search box
    const searchInput = page.locator('.search-box input[type="search"]');
    await expect(searchInput).toBeVisible();
    await expect(searchInput).toHaveAttribute('placeholder', 'Where do you want to go?');

    // Small text under search
    await expect(page.locator('.search-box small')).toContainText('Search buildings, offices');

    // Recent searches
    await expect(page.locator('.section-label')).toHaveText('RECENT');
    const chips = page.locator('.recent-chip');
    await expect(chips).toHaveCount(4);
    await expect(chips.first()).toHaveText('Rodolsa Hall');

    // Explore section heading
    await expect(page.locator('.section-heading h2')).toHaveText('Explore Campus');

    // Location cards
    const cards = page.locator('.location-card');
    await expect(cards).toHaveCount(6);

    // Map promo
    await expect(page.locator('.map-promo strong')).toHaveText('View Full Campus Map');
  });

  // ── 7  Map page UI ─────────────────────────────────────
  test('7 – Map page displays map container and controls', async ({ page }) => {
    await page.goto('/map');

    // Header
    await expect(page.locator('.map-header h1')).toHaveText('Map');
    await expect(page.locator('.map-header span')).toHaveText('Campus navigation');

    // Back button
    await expect(page.locator('.map-back')).toBeVisible();

    // Map container with building labels
    const mapContainer = page.locator('.map-container');
    await expect(mapContainer).toBeVisible();
    await expect(mapContainer).toContainText('North Academic');
    await expect(mapContainer).toContainText('West Academic');
    await expect(mapContainer).toContainText('Rodolsa Hall');
    await expect(mapContainer).toContainText('Civic Center');

    // Selected location card
    await expect(page.locator('.selected-location')).toBeVisible();
    await expect(page.locator('.selected-location-row strong')).toHaveText('Campus');

    // Search button
    await expect(page.locator('.map-search-button')).toHaveText('Search another location');
  });

  // ── 8  Profile page UI ─────────────────────────────────
  test('8 – Profile page displays card, settings, and logout', async ({ page }) => {
    await page.goto('/profile');

    // Header
    await expect(page.locator('.page-header h1')).toHaveText('Profile');
    await expect(page.locator('.page-header span')).toHaveText('Your account');

    // Profile card
    await expect(page.locator('.profile-card h2')).toHaveText('Student');
    await expect(page.locator('.profile-card p')).toHaveText('student@liceo.edu.ph');
    await expect(page.locator('.profile-avatar')).toHaveText('S');

    // Settings buttons
    await expect(page.locator('.settings button', { hasText: 'Account Settings' })).toBeVisible();
    await expect(page.locator('.settings button', { hasText: 'Privacy' })).toBeVisible();
    await expect(page.locator('.settings button', { hasText: 'Terms of Service' })).toBeVisible();

    // Logout button
    await expect(page.locator('.logout-button')).toHaveText('Log Out');
  });

  // ── 9  Saved page UI ──────────────────────────────────
  test('9 – Saved locations page shows saved items or empty state', async ({ page }) => {
    await page.goto('/saved');

    // Header
    await expect(page.locator('.saved-header h1')).toHaveText('Saved Locations');
    await expect(page.locator('.saved-header span')).toHaveText('Your places');

    // Default: two saved locations
    const cards = page.locator('.saved-location-card');
    await expect(cards).toHaveCount(2);

    // First card details
    await expect(cards.first().locator('strong')).toHaveText('Rodolsa Hall');
    await expect(cards.first().locator('span').first()).toBeVisible();

    // Second card
    await expect(cards.nth(1).locator('strong')).toHaveText('West Academic Cluster');

    // Heart/remove buttons
    const hearts = page.locator('.saved-heart');
    await expect(hearts).toHaveCount(2);
  });

  // ── 10  Account settings page UI ──────────────────────
  test('10 – Account Settings page displays profile fields', async ({ page }) => {
    await page.goto('/account-settings');

    // Header
    await expect(page.locator('.settings-header h1')).toHaveText('Account Settings');
    await expect(page.locator('.settings-header span')).toHaveText('Profile');

    // Back button
    await expect(page.locator('.settings-back')).toBeVisible();

    // Personal information section
    await expect(page.locator('.settings-section h2', { hasText: 'Personal Information' })).toBeVisible();

    // Full name (editable)
    const nameInput = page.locator('#full-name');
    await expect(nameInput).toBeVisible();
    await expect(nameInput).toHaveValue('Student');

    // Student ID (readonly)
    const idInput = page.locator('#student-id');
    await expect(idInput).toBeVisible();
    await expect(idInput).toHaveValue('2021-00001');
    await expect(idInput).toHaveAttribute('readonly', '');

    // Email (readonly)
    const emailInput = page.locator('#student-email');
    await expect(emailInput).toBeVisible();
    await expect(emailInput).toHaveValue('student@liceo.edu.ph');
    await expect(emailInput).toHaveAttribute('readonly', '');

    // Password section
    await expect(page.locator('.settings-section h2', { hasText: 'Password' })).toBeVisible();
    await expect(page.locator('.settings-option', { hasText: 'Change Password' })).toBeVisible();

    // Save button
    await expect(page.locator('.settings-save')).toHaveText('Save Changes');
  });

});

# LdcuNav Test Suite & Verification Guide

This document details the test suites, verification procedures, automated scripts, and test results for the **LdcuNav** project.

---

## 1. Test Architecture Overview

The LdcuNav verification strategy encompasses three complementary layers:

```
      ▲
     / \     End-to-End Browser Tests (Playwright)
    /───\    - UI rendering, navigation flows, auth dialogs
   /     \
  /───────\  API Integration Tests (PowerShell & PHPUnit Feature)
 /         \ - HTTP routing, headers, status codes, CORS, Prisma persistence
/───────────\
───────────── Unit Tests (PHPUnit)
              - Request validation rules, JSON response envelopes, Query compilation
```

---

## 2. Backend PHPUnit Test Suite

The backend test suite is located under `ldcunav-be/tests/` and contains **44 tests with 290 assertions**, fully verified against the local PostgreSQL database.

### Test Files & Responsibilities

| Test File | Type | Focus Areas |
|---|---|---|
| `tests/Feature/LocationApiTest.php` | Feature | Public location listing, category filtering, search queries, map building endpoints, slug lookup, 404 responses. |
| `tests/Feature/AuthApiTest.php` | Feature | User registration, `@liceo.edu.ph` domain validation, password complexity, duplicate email & studentId prevention, login credential matching, token issuance, logout token revocation, forgot password flow. |
| `tests/Feature/ProfileApiTest.php` | Feature | `/api/me` identity endpoint, unauthenticated 401 guard, `/api/profile` retrieval, full name updates, validation. |
| `tests/Feature/SavedLocationApiTest.php` | Feature | Auth guard on saved endpoints, listing bookmarks, saving destinations, duplicate save idempotency, referential integrity, deletion, 404 on unsaved removals. |
| `tests/Unit/ValidationRulesTest.php` | Unit | Isolated validation checks for `RegisterRequest`, `LoginRequest`, `UpdateProfileRequest`, `StoreSavedLocationRequest`, and `ForgotPasswordRequest`. |
| `tests/Unit/RespondsWithApiTest.php` | Unit | API envelope consistency (`success`, `message`, `data`, `errors`), status code propagation. |
| `tests/Unit/ModelQueryTest.php` | Unit | Query builder field whitelisting, injection prevention, unknown column rejection, count aggregations. |

### Running the Backend Tests

```powershell
# From the ldcunav-be directory:
cd ldcunav-be

# Run the complete PHPUnit test suite:
php vendor/phpunit/phpunit/phpunit

# Or run a specific test class:
php vendor/phpunit/phpunit/phpunit --filter=LocationApiTest
php vendor/phpunit/phpunit/phpunit --filter=AuthApiTest
php vendor/phpunit/phpunit/phpunit --filter=SavedLocationApiTest

# Run only Unit or Feature suites:
php vendor/phpunit/phpunit/phpunit --testsuite=Unit
php vendor/phpunit/phpunit/phpunit --testsuite=Feature
```

*Note on Windows / XAMPP environments: Ensure `pdo_pgsql` is loaded. If using XAMPP PHP with PostgreSQL 18:*
```powershell
$env:Path = "C:\Program Files\PostgreSQL\18\bin;$env:Path"
& "C:\xampp\php\php.exe" -d extension=pdo_pgsql vendor/phpunit/phpunit/phpunit
```

### Verified Test Results
```text
PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: ldcunav-be\phpunit.xml

............................................                      44 / 44 (100%)

Time: 00:02.014, Memory: 34.00 MB

OK (44 tests, 290 assertions)
```

---

## 3. Automated API Integration Suite (`backend-e2e.ps1`)

The repository includes a comprehensive standalone integration script: `tools/backend-e2e.ps1`. It executes **42 end-to-end HTTP checks** against a live running Laravel server (`http://127.0.0.1:8000/api`), validating:

1. Valid student registration (status 201, token presence, password hash exclusion).
2. Duplicate email & studentId rejection (status 422).
3. Non-Liceo domain rejection (`@gmail.com` -> 422).
4. Weak password rejection (status 422).
5. Missing input field validation (status 422).
6. Invalid password login rejection (status 401).
7. Unknown user login rejection (status 401).
8. Valid login and token receipt (status 200).
9. Authenticated profile `/me` and `/profile` retrieval.
10. Profile name modification and database persistence.
11. Catalog listing, filtering, search, map placements, and slug retrieval.
12. Bookmark addition, idempotency, foreign key enforcement, and deletion.
13. Token revocation upon logout.
14. CORS preflight `OPTIONS` and simple request origin validation.

### Running the E2E Integration Suite

```powershell
# 1. In terminal 1, start the backend:
cd ldcunav-be
php artisan serve

# 2. In terminal 2, execute the verification script:
.\tools\backend-e2e.ps1
```

---

## 4. Frontend Browser Tests (Playwright)

Playwright tests are configured at the repository root and run headless browser automation against the Vue 3 application:

- `tests/ui.spec.ts`: Visual checks, header titles, campus logo, back navigation buttons, input placeholder rendering, and CSS map layout.
- `tests/functionality.spec.ts`: User flows including login, client-side email domain alerts, password reveal toggle, navigation to explore/map views, and search input interactions.

### Running Playwright Tests

```bash
# Ensure frontend dev server or build is running:
npx playwright test

# Or run with UI test runner:
npx playwright test --ui

# View generated test report:
npx playwright show-report
```

---

## 5. Build and Schema Validation Checks

### Prisma Schema Validation
```bash
cd ldcunav-be
npx prisma validate
# Output: The schema at prisma\schema.prisma is valid 🚀
```

### Prisma Migration Status
```bash
cd ldcunav-be
npx prisma migrate status
# Output: Database schema is up to date! (1 migration found in prisma/migrations)
```

### Frontend Production Build
```bash
cd ldcunav-fe
npx vite build
# Output: 54 modules transformed, dist/ built successfully in 246ms
```

---

## 6. Environment & Troubleshooting Notes

| Issue | Cause | Resolution |
|---|---|---|
| `PDOException: could not find driver` | PHP CLI missing `pdo_pgsql` extension | Enable `extension=pdo_pgsql` in `php.ini` and ensure PostgreSQL `bin/` (containing `libpq.dll`) is in system `PATH`. |
| `FATAL: database ":memory:" does not exist` | Leftover SQLite config in `phpunit.xml` | Updated `phpunit.xml` to use `<env name="DB_CONNECTION" value="pgsql"/>` and `<env name="DB_DATABASE" value="ldcunav"/>`. |
| `bind message supplies 0 parameters` | PHP closure by-value copy in `ModelQuery.php` | Replaced `array_map` with explicit `foreach` by-reference parameter binding in query compiler. |
| `vue-tsc: Package subpath './lib/tsc' not defined` | Compatibility between TypeScript 7 and `vue-tsc` | Build using `npx vite build` directly. |

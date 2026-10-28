# LdcuNav — Campus Navigation System for Liceo de Cagayan University

![LdcuNav](https://img.shields.io/badge/Project-LdcuNav-blue.svg)
![Status](https://img.shields.io/badge/Status-Complete%20%2F%20Deliverable%20Ready-success.svg)
![Frontend](https://img.shields.io/badge/Frontend-Vue%203%20%7C%20Vite%20%7C%20TypeScript-42b883.svg)
![Backend](https://img.shields.io/badge/Backend-Laravel%2012%20%7C%20PHP%208.2%2B-ff2d20.svg)
![Database](https://img.shields.io/badge/Database-PostgreSQL%2018%20%7C%20Prisma%20ORM-336791.svg)
![Tests](https://img.shields.io/badge/Tests-44%20Passing%20(290%20Assertions)-brightgreen.svg)

**LdcuNav** is a modern, web-based campus navigation and student resource portal tailored for **Liceo de Cagayan University** (Cagayan de Oro City, Philippines). It provides an interactive platform for students, faculty, administrative staff, and university visitors to locate academic clusters, facilities, student service offices, and administrative departments across the university campus.

---

## 👥 Project Information & Authors

* **Project**: LdcuNav — Campus Navigation System
* **Institution**: Liceo de Cagayan University
* **Team Members**:
  * **Ashton Lactuan** — Team Leader
  * **Johnden Llatuna** — Full-Stack Developer
  * **Nikki Pacatang** — Systems Analyst & Documentation
  * **Christian Cabalde** — Backend & Database Engineer

---

## 📌 Problem Statement & Objectives

### The Problem
University campuses can be disorienting for incoming freshmen, transferees, and campus visitors. Inquiring at multiple desks for office locations, classroom numbers, or student service centers causes lost time, administrative congestion, and unnecessary confusion during peak enrollment and examination periods.

### System Objectives
1. **Centralized Directory**: Consolidate university buildings, colleges, laboratories, student offices, and clinic locations into a single searchable catalog.
2. **Interactive Map Visualization**: Render key campus landmarks (North Academic, West Academic, Rodolsa Hall, and Liceo Civic Center) on an interactive campus map.
3. **Personalized Bookmarks**: Enable registered students to save and quickly access frequently visited destinations.
4. **Institutional Security**: Restrict registration strictly to verified Liceo institutional emails (`@liceo.edu.ph`) with robust bearer token authentication.

### Target Users
- **Students (Undergraduate & Senior High)**: Quick discovery of lecture rooms, laboratories, library facilities, and cashier/registrar offices.
- **Faculty & Academic Staff**: Easy navigation between academic clusters and administrative divisions.
- **University Guests & Parents**: Locating event venues (Liceo Civic Center, Rodolsa Hall) and admissions desks.

---

## 🛠️ Technology Stack & Architecture

LdcuNav is designed as a decoupled SPA (Single Page Application) and RESTful API architecture:

```
┌─────────────────────────────────────────────────────────────┐
│                       Vue 3 Frontend                        │
│         (Vue 3, TypeScript, Vite, Vue Router, Pinia)        │
└──────────────────────────────┬──────────────────────────────┘
                               │
                HTTP REST API  │ Authorization: Bearer <token>
                JSON Envelopes │ CORS: http://localhost:5173
                               ▼
┌─────────────────────────────────────────────────────────────┐
│                     Laravel 12 Backend                      │
│     (Routing, Form Requests, Sanctum/Bearer Token Guard)    │
└──────────────────────────────┬──────────────────────────────┘
                               │
             Injected Singleton│ App\Prisma\PrismaClient
          PDO Prepared Stmts   │ Whitelisted Column Compilation
                               ▼
┌─────────────────────────────────────────────────────────────┐
│                 PostgreSQL 18 Relational DB                 │
│         (Schema & Migrations Managed via Prisma ORM)        │
└─────────────────────────────────────────────────────────────┘
```

| Layer | Technology | Version | Purpose |
|---|---|---|---|
| **Frontend** | Vue.js | `3.5.43` | Single-page application UI framework |
| **Tooling & Build** | Vite | `8.3.0` | Ultra-fast development server & production bundler |
| **Language** | TypeScript | `7.0.2` | Strong type checking and developer experience |
| **Icons** | Lucide Vue Next | `1.0.0` | Modern, clean vector iconography |
| **Routing** | Vue Router | `4.6.4` | Client-side routing and navigation guards |
| **Backend** | Laravel | `12.x` | Robust REST API routing, validation, and middleware |
| **Language** | PHP | `8.2+` (tested on 8.2 / 8.4) | Server-side execution engine |
| **Database** | PostgreSQL | `18.6` | Production ACID relational database |
| **Schema & Migrations**| Prisma CLI | `6.19.3` | Single source of truth schema definition & migrations |
| **Data Access Layer** | Custom Prisma PDO Client | `App\Prisma` | Injection-safe, prepared-statement PostgreSQL client |
| **E2E Testing** | Playwright | `1.63.0` | Automated headless browser test suite |

---

## 📂 Repository Directory Structure

```text
ldcunav/
├── docs/                               # Comprehensive project documentation
│   ├── API.md                          # REST API specification & curl examples
│   ├── DATABASE.md                     # Schema reference, ER diagrams, seeders
│   ├── DEPLOYMENT.md                   # Production deployment & Nginx guide
│   └── TESTING.md                      # Test suite documentation & execution guide
├── ldcunav-be/                         # Laravel 12 Backend API
│   ├── app/
│   │   ├── Auth/                       # AuthenticatedUser & PrismaUserProvider
│   │   ├── Console/Commands/           # SeedLocations artisan command
│   │   ├── Http/Controllers/           # Auth, Location, Profile, SavedLocation
│   │   ├── Http/Requests/              # Validation requests (ends_with:@liceo.edu.ph)
│   │   ├── Models/                     # User model
│   │   ├── Prisma/                     # PrismaClient & ModelQuery PDO bridge
│   │   ├── Providers/                  # AppServiceProvider (bearer guard binding)
│   │   ├── Services/                   # AuthService, LocationService, SavedLocationService
│   │   └── Support/                    # RespondsWithApi JSON envelope trait
│   ├── config/                         # App, database, auth, cors configuration
│   ├── database/
│   │   └── seeders/                    # DatabaseSeeder, LocationSeeder, UserSeeder, SavedLocationSeeder
│   ├── prisma/
│   │   ├── migrations/                 # PostgreSQL baseline migration
│   │   └── schema.prisma               # Master Prisma schema definition
│   ├── routes/
│   │   └── api.php                     # API route declarations
│   ├── tests/
│   │   ├── Feature/                    # AuthApiTest, LocationApiTest, ProfileApiTest, SavedLocationApiTest
│   │   └── Unit/                       # RespondsWithApiTest, ValidationRulesTest, ModelQueryTest
│   ├── .env.example                    # Backend environment template
│   ├── composer.json                   # PHP dependencies
│   ├── package.json                    # Node dependencies (Prisma CLI)
│   └── phpunit.xml                     # PHPUnit testing configuration
├── ldcunav-fe/                         # Vue 3 Frontend SPA
│   ├── public/                         # Static assets and favicons
│   ├── src/
│   │   ├── assets/                     # Styles, logo, images
│   │   ├── components/                 # Reusable Vue components
│   │   ├── router/index.ts             # Client-side route declarations
│   │   ├── stores/                     # State management stores
│   │   ├── views/                      # Login, Register, Explore, Map, Saved, Profile
│   │   ├── App.vue                     # Root Vue component
│   │   ├── main.ts                     # Vue application bootstrap
│   │   └── style.css                   # Global responsive CSS styling
│   ├── .env.example                    # Frontend environment template
│   ├── package.json                    # Frontend dependencies & scripts
│   ├── tsconfig.json                   # TypeScript configuration
│   └── vite.config.ts                  # Vite configuration
├── tests/                              # Root Playwright End-to-End browser tests
│   ├── functionality.spec.ts           # Flow & validation tests
│   └── ui.spec.ts                      # Visual & structural component tests
├── tools/                              # Developer and testing utilities
│   ├── backend.ps1                     # Environment setup helper for PHP / XAMPP
│   └── backend-e2e.ps1                 # 42-check automated PowerShell API test suite
├── .gitignore                          # Git ignore definitions
├── package.json                        # Root package definition (Playwright)
├── playwright.config.ts                # Playwright configuration
└── README.md                           # Master project documentation (this file)
```

---

## ⚡ Quickstart & Local Setup Guide

### 1. Prerequisites
Ensure the following tools are installed on your machine:
- **PHP 8.2 or higher** with `pdo`, `pdo_pgsql`, and `mbstring` enabled.
- **Composer 2.5+**
- **Node.js 18+ & npm**
- **PostgreSQL 14+** (running locally on port `5432` with a database named `ldcunav`)

---

### 2. Database Initialization
Create the local PostgreSQL database:
```sql
-- In psql or pgAdmin:
CREATE DATABASE ldcunav;
```

---

### 3. Backend Setup (`ldcunav-be`)

```powershell
# Navigate to the backend directory
cd ldcunav-be

# 1. Copy environment template
copy .env.example .env

# 2. Configure credentials in .env:
#    DB_USERNAME=postgres
#    DB_PASSWORD=your_password
#    DATABASE_URL="postgresql://postgres:your_password@127.0.0.1:5432/ldcunav?schema=public"

# 3. Install PHP dependencies
composer install

# 4. Generate application encryption key
php artisan key:generate

# 5. Install Node dependencies (for Prisma CLI)
npm install

# 6. Apply database schema and migrations
npx prisma migrate deploy

# 7. Seed the database with campus locations, demo users, and bookmarks
php artisan db:seed

# 8. Start the Laravel development server
php artisan serve
```
The API is now running at `http://127.0.0.1:8000/api`.

---

### 4. Frontend Setup (`ldcunav-fe`)

In a new terminal window:
```powershell
# Navigate to the frontend directory
cd ldcunav-fe

# 1. Copy environment template
copy .env.example .env

# 2. Install dependencies
npm install

# 3. Start the Vite development server
npm run dev
```
The application will be accessible in your browser at `http://localhost:5173`.

---

## 🧪 Testing and Verification

LdcuNav includes comprehensive testing across unit, integration, and browser levels.

### A. Run Backend PHPUnit Suite (44 Tests, 290 Assertions)
```powershell
cd ldcunav-be
php vendor/phpunit/phpunit/phpunit
```
*Result: 44 tests, 290 assertions, 100% passing.*

### B. Run Automated API Integration Suite (42 Checks)
```powershell
# While the backend server is running:
.\tools\backend-e2e.ps1
```
*Result: 42 checks passed (Registration, Auth, Profile, Locations, Saved, CORS).*

### C. Run Playwright Browser Tests
```powershell
npx playwright test
```

### D. Verify Frontend Production Build
```powershell
cd ldcunav-fe
npx vite build
```
*Result: Built in ~250ms with 0 errors.*

---

## 🔑 Demonstration Accounts

For evaluation and testing, the database is populated with realistic demonstration accounts. All accounts share the same password:

> **Default Password for All Demo Accounts**: `LdcuNav2026!`

| Name | Role | Email | Student / Employee ID | Pre-Configured Bookmarks |
|---|---|---|---|---|
| **Maria Clara Santos** | Senior Student | `maria.santos@liceo.edu.ph` | `2024-00123` | North Academic, Library, IT Services, Cashier |
| **Juan Dela Cruz** | Sophomore Student | `juan.delacruz@liceo.edu.ph` | `2024-00456` | West Academic, Rodolsa Hall, Gym, Cafeteria |
| **Prof. Clara Reyes** | Faculty Member | `clara.reyes@liceo.edu.ph` | `2020-00789` | North Academic, Rodolsa Hall, HRMO, Research |
| **Demo Campus Navigator** | Evaluator Account | `demo.navigator@liceo.edu.ph` | `2025-00999` | Civic Center, Clinic, Registrar, Guidance |

---

## 🌐 API Endpoints Reference

All endpoints return standard envelopes: `{ success, message, data }` or `{ success, message, errors }`.

| Method | Endpoint | Access | Summary |
|---|---|---|---|
| `POST` | `/api/register` | Public | Register with official `@liceo.edu.ph` email |
| `POST` | `/api/login` | Public | Authenticate and obtain Bearer token |
| `POST` | `/api/logout` | Bearer | Revoke current Bearer token |
| `POST` | `/api/forgot-password` | Public | Password recovery notification |
| `GET` | `/api/me` | Bearer | Retrieve active user identity |
| `GET` | `/api/profile` | Bearer | Detailed user profile |
| `PUT` | `/api/profile` | Bearer | Update user profile full name |
| `GET` | `/api/locations` | Public | List campus locations (optional `?category=`) |
| `GET` | `/api/locations/search` | Public | Search locations by keyword (`?q=`) |
| `GET` | `/api/locations/map` | Public | Retrieve 4 map building placements |
| `GET` | `/api/locations/{slug}` | Public | Location details by slug |
| `GET` | `/api/saved` | Bearer | List student's saved bookmarks |
| `POST` | `/api/saved` | Bearer | Bookmark a location (`locationId`) |
| `DELETE` | `/api/saved/{locationId}` | Bearer | Remove bookmark |

*For complete payload examples and curl commands, see [`docs/API.md`](docs/API.md).*

---

## 🔒 Security & Data Integrity

1. **Email Domain Enforcement**: Server-side validation strictly enforces the `@liceo.edu.ph` institutional domain on registration.
2. **Password Security**: Passwords must contain uppercase, numbers, and at least 8 characters. Stored exclusively as bcrypt hashes (`Hash::make()`). Plaintext passwords are never stored or logged.
3. **Bearer Token Protection**: API tokens are generated as 64-character random strings and stored as SHA-256 hashes (`tokenHash`).
4. **SQL Injection Prevention**: Column identifiers are strictly whitelisted; all query operands are bound through PDO prepared statement parameters.
5. **CORS Isolation**: Access is restricted strictly to the verified frontend origin.
6. **Referential Cascade**: Bookmarks and access tokens automatically cascade on user deletion.

---

## 📚 Detailed Documentation Links

- 📖 [**Database Specification & Schema Reference** (`docs/DATABASE.md`)](docs/DATABASE.md)
- 🔌 [**REST API Documentation & Samples** (`docs/API.md`)](docs/API.md)
- 🧪 [**Test Suite & Verification Guide** (`docs/TESTING.md`)](docs/TESTING.md)
- 🚀 [**Production Deployment & Nginx Guide** (`docs/DEPLOYMENT.md`)](docs/DEPLOYMENT.md)

---

## ⚖️ Academic Disclaimer & License

This project was developed as an academic software engineering capstone / deliverable for **Liceo de Cagayan University**. All building names, department descriptions, and demo accounts are synthetic representations designed for functional navigation demonstration and educational evaluation.

Licensed under the [MIT License](LICENSE).

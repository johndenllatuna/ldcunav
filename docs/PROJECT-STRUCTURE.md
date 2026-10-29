# Codebase and component structure

## Repository layout

```text
ldcunav/
├── docs/                       # API, database, deployment, tests, UI documentation
├── lducnav-fe/                 # Vue 3 + TypeScript + Vite SPA
│   ├── public/                 # Static public assets
│   └── src/
│       ├── assets/             # Images and starter assets
│       ├── components/         # Shared Vue components and icons
│       ├── router/index.ts     # SPA route table
│       ├── stores/             # Pinia store modules (where used)
│       ├── views/              # Route-level page components
│       ├── App.vue             # Root layout
│       ├── main.ts             # Vue application entry point
│       └── style.css           # Global styles
├── ldcunav-be/                 # Laravel REST API and PostgreSQL integration
│   ├── app/
│   │   ├── Auth/               # Bearer-token identity/provider
│   │   ├── Http/Controllers/   # HTTP request orchestration
│   │   ├── Http/Requests/      # Input validation
│   │   ├── Models/             # User model
│   │   ├── Prisma/             # PDO query/data access bridge
│   │   ├── Services/           # Auth, locations, profiles, bookmarks
│   │   └── Support/            # Shared API response helpers
│   ├── database/seeders/       # Location and demo-data seeders
│   ├── prisma/                 # Schema, migrations, generated client
│   ├── routes/api.php          # REST route declarations
│   └── tests/                  # PHPUnit unit and feature tests
├── tests/                      # Playwright browser tests
├── tools/                      # Local helper scripts (if present)
├── README.md                   # Project overview and local setup
└── playwright.config.ts        # Browser test configuration
```

## Request and component flow

```text
Browser -> Vue route/view -> shared UI components -> HTTP request
        -> Laravel route -> controller -> request validation
        -> service -> PrismaClient/ModelQuery (PDO prepared statements)
        -> PostgreSQL
```

### Frontend responsibilities

- `src/main.ts` creates and mounts the Vue application and installs Vue Router.
- `src/router/index.ts` maps paths to page-level views.
- `src/views/` contains route screens; keep view-specific state and layout close to its page.
- `src/components/` is for reusable presentation and interaction elements.
- `src/stores/` is for cross-view state that genuinely needs central management.
- `src/style.css` contains app-wide design rules and responsive breakpoints.

### Backend responsibilities

- `routes/api.php` declares the HTTP contract.
- Form Request classes validate untrusted input before controller/service work.
- Controllers translate the HTTP request/response and delegate domain logic to services.
- Services own auth, location lookup, profile updates, and saved-location workflows.
- `App\Prisma\PrismaClient` / `ModelQuery` provide parameterized PostgreSQL access.
- Prisma `schema.prisma` plus checked-in SQL migrations define persisted data; seeders provide repeatable sample data.

For schema, migrations, and seed commands see [DATABASE.md](DATABASE.md); for runtime endpoint payloads see [API.md](API.md). This tree describes the current project organization rather than a promise that every optional module is actively used.

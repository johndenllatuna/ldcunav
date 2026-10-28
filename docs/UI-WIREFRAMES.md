# LdcuNav UI wireframes and implementation

This document records the implemented screen flow and low-fidelity layouts. The wireframes describe structure, not final visual styling; the Vue views and `ldcunav-fe/src/style.css` are the implementation source.

## Navigation map

```text
/ (redirects to /login)
├── /login ── /register
│          └─ /forgot-password
└── /home
    ├── /explore
    ├── /map
    ├── /search
    ├── /saved
    └── /profile ── /account-settings
                 ├─ /privacy
                 └─ /terms
```

## Wireframes

### Sign in (`/login`)

```text
┌──────────────────────────────────┐
│ LdcuNav / Liceo de Cagayan Univ. │
│                                  │
│       Welcome back               │
│  Institutional email [________]  │
│  Password           [________]  │
│  [ Sign in ]                     │
│  Forgot password?  Create account│
└──────────────────────────────────┘
```

### Home and explore (`/home`, `/explore`)

```text
┌──────────────────────────────────────────────────────┐
│ Brand                         Search     Profile      │
├──────────────┬───────────────────────────────────────┤
│ Home         │ Welcome / campus overview             │
│ Explore      │ [Search locations................]    │
│ Campus map   │ Featured destinations                 │
│ Saved        │ [Location card] [Location card]       │
│ Profile      │ Categories and recent places          │
└──────────────┴───────────────────────────────────────┘
```

### Campus map (`/map`)

```text
┌──────────────────────────────────────────────────────┐
│ Campus map                            [Search]       │
├──────────────────────────────────────────────────────┤
│                                                      │
│      [North Academic]       [Rodolsa Hall]           │
│                campus map / location markers         │
│      [West Academic]        [Civic Center]           │
│                                                      │
│ Selected destination details and directions          │
└──────────────────────────────────────────────────────┘
```

### Search and saved destinations (`/search`, `/saved`)

```text
┌──────────────────────────────────────────────────────┐
│ Search destinations                                  │
│ [Type a building, office, or service........]        │
│ [All] [Academic] [Facilities] [Services] [Offices]   │
│ Results: icon  Name / type / summary       [Save]    │
│          icon  Name / type / summary       [Save]    │
└──────────────────────────────────────────────────────┘
```

## Current implementation

| Screen / feature | Vue implementation | Notes |
|---|---|---|
| Sign in | `src/views/LoginView.vue` | Client form and authentication flow |
| Registration | `src/views/RegisterView.vue` | Liceo email and password validation |
| Password recovery | `src/views/ForgotPasswordView.vue` | Request form |
| Home | `src/views/HomeView.vue` | Landing/dashboard screen |
| Explore | `src/views/ExploreView.vue` | Campus destination discovery |
| Map | `src/views/MapView.vue` | Campus map view and markers |
| Search | `src/views/SearchView.vue` | Location search |
| Saved | `src/views/SavedView.vue` | Bookmarked locations |
| Profile/settings | `src/views/ProfileView.vue`, `AccountSettingsView.vue` | Account pages |
| Legal pages | `PrivacyView.vue`, `TermsView.vue` | Informational pages |
| Shared shell/styles | `src/App.vue`, `src/style.css` | Root layout and global/responsive styling |

The router currently redirects `/` to `/login`. Route declarations are in `src/router/index.ts`. Confirm backend connectivity and authentication behavior against `docs/API.md`; the wireframes do not imply capabilities beyond the listed routes and views.

## Responsive and accessibility checklist

- Keep primary navigation reachable on small screens; avoid horizontal scrolling.
- Give form controls persistent labels and expose validation errors next to the relevant field.
- Provide keyboard focus indicators and descriptive link/button names.
- Do not use color or emoji as the sole indicator of category or map selection.
- Keep map destinations available as a readable list for keyboard and assistive-technology users.

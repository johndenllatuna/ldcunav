# LdcuNav REST API Specification

The **LdcuNav API** is a JSON-based RESTful service providing authentication, student profile management, campus location catalogs, search, map coordinate groupings, and user bookmarking functionality.

- **Base URL (Local)**: `http://localhost:8000/api`
- **Default Format**: `application/json`
- **Authentication**: HTTP Bearer Token (`Authorization: Bearer <token>`)

---

## 1. Response Envelope Format

All responses conform to a predictable envelope structure.

### Success Response (`200 OK`, `201 Created`)
```json
{
  "success": true,
  "message": "Human-readable status summary.",
  "data": { ... } | [ ... ] | null
}
```

### Error Response (`400 Bad Request`, `401 Unauthorized`, `404 Not Found`, `422 Unprocessable Entity`)
```json
{
  "success": false,
  "message": "Human-readable explanation of the error.",
  "errors": {
    "field_name": [
      "Specific validation or error description."
    ]
  }
}
```

---

## 2. Authentication & Authorization

Authentication uses token-based Bearer authentication. When a user registers or logs in, the backend issues an unhashed plaintext bearer token and stores its SHA-256 hash in the `PersonalAccessToken` table.

For authenticated endpoints, include the header:
```http
Authorization: Bearer <your_token_here>
```

---

## 3. Cross-Origin Resource Sharing (CORS)

The backend allows preflight `OPTIONS` requests and cross-origin requests from the trusted frontend origin configured via `FRONTEND_ORIGIN` in `.env` (default: `http://localhost:5173`).

### CORS Headers Returned
```http
Access-Control-Allow-Origin: http://localhost:5173
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Authorization, Content-Type, Accept, X-Requested-With
```

---

## 4. Endpoints Summary

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/api/register` | Public | Register a new Liceo student account |
| `POST` | `/api/login` | Public | Authenticate with email & password |
| `POST` | `/api/forgot-password` | Public | Request a password reset link |
| `GET` | `/api/locations` | Public | List campus locations (supports `?category=` filter) |
| `GET` | `/api/locations/search` | Public | Search campus locations by keyword (`?q=`) |
| `GET` | `/api/locations/map` | Public | Retrieve the 4 primary map buildings |
| `GET` | `/api/locations/{slug}` | Public | Retrieve full details of a location by slug |
| `POST` | `/api/logout` | Bearer | Revoke the caller's active bearer token |
| `GET` | `/api/me` | Bearer | Retrieve the authenticated user's profile |
| `GET` | `/api/profile` | Bearer | Retrieve detailed profile data |
| `PUT` | `/api/profile` | Bearer | Update user profile full name |
| `GET` | `/api/saved` | Bearer | List the user's saved/bookmarked locations |
| `POST` | `/api/saved` | Bearer | Bookmark a location (`locationId`) |
| `DELETE` | `/api/saved/{locationId}` | Bearer | Remove a location from user's bookmarks |

---

## 5. Detailed Endpoint Documentation

### `POST /api/register`
Creates a new student or faculty account.

#### Request Body
```json
{
  "fullName": "Maria Clara Santos",
  "studentId": "2024-00123",
  "email": "maria.santos@liceo.edu.ph",
  "password": "LdcuNav2026!"
}
```

#### Validation Rules
- `fullName`: required, string, max 255
- `studentId`: required, string, max 50, unique in database
- `email`: required, valid email, max 255, ends with `@liceo.edu.ph`, unique in database
- `password`: required, string, min 8, max 255, must contain at least 1 uppercase letter (`[A-Z]`) and 1 number (`[0-9]`)

#### Response (`201 Created`)
```json
{
  "success": true,
  "message": "Account created successfully.",
  "data": {
    "user": {
      "id": 1,
      "fullName": "Maria Clara Santos",
      "studentId": "2024-00123",
      "email": "maria.santos@liceo.edu.ph",
      "createdAt": "2026-10-28 07:07:31.000",
      "updatedAt": "2026-10-28 07:07:31.000"
    },
    "token": "7a8b9c0d1e2f3a4b..."
  }
}
```

---

### `POST /api/login`
Authenticates existing accounts.

#### Request Body
```json
{
  "email": "maria.santos@liceo.edu.ph",
  "password": "LdcuNav2026!"
}
```

#### Response (`200 OK`)
```json
{
  "success": true,
  "message": "Logged in successfully.",
  "data": {
    "user": {
      "id": 1,
      "fullName": "Maria Clara Santos",
      "studentId": "2024-00123",
      "email": "maria.santos@liceo.edu.ph"
    },
    "token": "9f8e7d6c5b4a3..."
  }
}
```

#### Error Response (`401 Unauthorized`)
```json
{
  "success": false,
  "message": "These credentials do not match our records.",
  "errors": {
    "email": ["These credentials do not match our records."]
  }
}
```

---

### `POST /api/logout`
Revokes the calling bearer token immediately.

#### Headers
`Authorization: Bearer <token>`

#### Response (`200 OK`)
```json
{
  "success": true,
  "message": "Logged out successfully.",
  "data": null
}
```

---

### `GET /api/me`
Fetches the current authenticated user identity.

#### Headers
`Authorization: Bearer <token>`

#### Response (`200 OK`)
```json
{
  "success": true,
  "message": "Authenticated user.",
  "data": {
    "id": 1,
    "fullName": "Maria Clara Santos",
    "studentId": "2024-00123",
    "email": "maria.santos@liceo.edu.ph",
    "createdAt": "2026-10-28 07:07:31.000",
    "updatedAt": "2026-10-28 07:07:31.000"
  }
}
```

---

### `PUT /api/profile`
Updates profile information for the authenticated user.

#### Headers
`Authorization: Bearer <token>`

#### Request Body
```json
{
  "fullName": "Maria Clara Santos-Dela Cruz"
}
```

#### Response (`200 OK`)
```json
{
  "success": true,
  "message": "Profile updated successfully.",
  "data": {
    "id": 1,
    "fullName": "Maria Clara Santos-Dela Cruz",
    "studentId": "2024-00123",
    "email": "maria.santos@liceo.edu.ph"
  }
}
```

---

### `GET /api/locations`
Lists all campus locations, with optional category filtering.

#### Query Parameters
- `category` *(optional)*: Filter by category (`Academic`, `Facilities`, `Services`, `Offices`, `Administrative`)
- `q` *(optional)*: Free-text search query across name, type, and description

#### Example
```http
GET /api/locations?category=Academic
```

#### Response (`200 OK`)
```json
{
  "success": true,
  "message": "Locations retrieved.",
  "data": [
    {
      "id": 1,
      "name": "North Academic Cluster",
      "slug": "north-academic-cluster",
      "category": "Academic",
      "type": "Academic Building",
      "description": "Home to the College of Arts and Sciences...",
      "icon": "🏢",
      "mapOrder": 1,
      "createdAt": "2026-10-28 07:07:31.000",
      "updatedAt": "2026-10-28 07:07:31.000"
    }
  ]
}
```

---

### `GET /api/locations/search`
Searches locations using case-insensitive keyword matching.

#### Query Parameters
- `q` *(required)*: Search keyword (minimum 1 non-whitespace character)

#### Response (`200 OK`)
```json
{
  "success": true,
  "message": "Search results.",
  "data": [
    {
      "id": 6,
      "name": "Rodolsa Hall",
      "slug": "rodolsa-hall",
      "category": "Facilities",
      "type": "University Facility",
      "description": "Multi-purpose auditorium...",
      "icon": "🏢",
      "mapOrder": 3
    }
  ]
}
```

---

### `GET /api/locations/map`
Returns locations that have a designated `mapOrder` (1 to 4) for campus map rendering.

#### Response (`200 OK`)
```json
{
  "success": true,
  "message": "Campus map locations retrieved.",
  "data": [
    {
      "id": 1,
      "name": "North Academic Cluster",
      "slug": "north-academic-cluster",
      "mapOrder": 1
    },
    {
      "id": 2,
      "name": "West Academic Cluster",
      "slug": "west-academic-cluster",
      "mapOrder": 2
    },
    {
      "id": 6,
      "name": "Rodolsa Hall",
      "slug": "rodolsa-hall",
      "mapOrder": 3
    },
    {
      "id": 5,
      "name": "Liceo Civic Center",
      "slug": "liceo-civic-center",
      "mapOrder": 4
    }
  ]
}
```

---

### `GET /api/locations/{slug}`
Retrieves a single location by its unique slug identifier.

#### Example
```http
GET /api/locations/west-academic-cluster
```

#### Response (`200 OK`)
```json
{
  "success": true,
  "message": "Location retrieved.",
  "data": {
    "id": 2,
    "name": "West Academic Cluster",
    "slug": "west-academic-cluster",
    "category": "Academic",
    "type": "Academic Building",
    "description": "Houses the College of Engineering, College of Architecture...",
    "icon": "🏛️",
    "mapOrder": 2
  }
}
```

#### Error Response (`404 Not Found`)
```json
{
  "success": false,
  "message": "Location not found.",
  "errors": {
    "slug": ["No location matches that slug."]
  }
}
```

---

### `GET /api/saved`
Retrieves bookmarked locations for the authenticated user, sorted latest first.

#### Headers
`Authorization: Bearer <token>`

#### Response (`200 OK`)
```json
{
  "success": true,
  "message": "Saved locations retrieved.",
  "data": [
    {
      "id": 1,
      "userId": 1,
      "locationId": 1,
      "createdAt": "2026-10-28 07:07:31.000",
      "location": {
        "id": 1,
        "name": "North Academic Cluster",
        "slug": "north-academic-cluster",
        "category": "Academic",
        "type": "Academic Building"
      }
    }
  ]
}
```

---

### `POST /api/saved`
Bookmarks a location. This operation is **idempotent**: attempting to bookmark an already saved location returns the existing saved record without error.

#### Headers
`Authorization: Bearer <token>`

#### Request Body
```json
{
  "locationId": 2
}
```

#### Response (`201 Created`)
```json
{
  "success": true,
  "message": "Location saved.",
  "data": {
    "id": 5,
    "userId": 1,
    "locationId": 2,
    "createdAt": "2026-10-28 07:15:00.000",
    "location": {
      "id": 2,
      "name": "West Academic Cluster",
      "slug": "west-academic-cluster"
    }
  }
}
```

---

### `DELETE /api/saved/{locationId}`
Removes a location from the authenticated user's saved list.

#### Headers
`Authorization: Bearer <token>`

#### Response (`200 OK`)
```json
{
  "success": true,
  "message": "Location removed from saved.",
  "data": null
}
```

#### Error Response (`404 Not Found`)
```json
{
  "success": false,
  "message": "Location was not in your saved list.",
  "errors": {
    "locationId": ["Nothing to remove."]
  }
}
```

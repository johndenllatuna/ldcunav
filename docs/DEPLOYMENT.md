# LdcuNav Production Deployment Guide

This guide describes the complete procedure for deploying the **LdcuNav** campus navigation system into a production or staging hosting environment.

---

## 1. System Requirements & Prerequisites

| Component | Minimum Version | Recommended | Notes |
|---|---|---|---|
| **Operating System** | Ubuntu 22.04 LTS / Debian 12 / Rocky Linux 9 | Ubuntu 24.04 LTS | Standard Linux server (x86_64 or ARM64) |
| **PHP** | 8.2+ | 8.3 or 8.4 | Required extensions: `pdo`, `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `curl`, `zip` |
| **Node.js** | 18.x LTS | 20.x or 22.x LTS | Used for Prisma CLI migrations and Vite production compilation |
| **Composer** | 2.5+ | 2.8+ | PHP dependency manager |
| **PostgreSQL** | 14+ | 16+ or 18+ | Managed (AWS RDS, Supabase, Neon) or self-hosted PostgreSQL instance |
| **Web Server** | Nginx 1.20+ / Caddy 2+ | Nginx with PHP-FPM 8.2+ | Reverse proxy and static asset server |

---

## 2. Infrastructure Architecture

```
                    Internet
                       │
             HTTPS:443 │ (TLS via Let's Encrypt)
                       ▼
            ┌─────────────────────┐
            │   Nginx Web Server  │
            └──────────┬──────────┘
                       │
       ┌───────────────┴───────────────┐
       │                               │
  Static / SPA Requests         API Requests (/api/*)
       │                               │
       ▼                               ▼
┌──────────────────┐        ┌──────────────────────┐
│  Vue 3 Assets    │        │  PHP-FPM (Port 9000  │
│  (dist/ folder)  │        │  or unix socket)     │
└──────────────────┘        └──────────┬───────────┘
                                       │
                                       ▼
                            ┌──────────────────────┐
                            │ Laravel 12 Backend   │
                            └──────────┬───────────┘
                                       │ PDO / TCP:5432
                                       ▼
                            ┌──────────────────────┐
                            │ PostgreSQL Database  │
                            │      (ldcunav)       │
                            └──────────────────────┘
```

---

## 3. Database Provisioning

Connect to PostgreSQL as a superuser and create a dedicated database and least-privilege application user:

```sql
-- 1. Create dedicated user with a strong password
CREATE USER ldcunav_app WITH PASSWORD 'CHANGE_TO_A_SECURE_PASSWORD';

-- 2. Create the production database
CREATE DATABASE ldcunav_prod OWNER ldcunav_app;

-- 3. Grant schema permissions
\c ldcunav_prod
GRANT ALL ON SCHEMA public TO ldcunav_app;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO ldcunav_app;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO ldcunav_app;
```

---

## 4. Backend Deployment (`ldcunav-be`)

### Step 1: Clone and Configure Environment
```bash
cd /var/www/ldcunav/ldcunav-be

# Create production environment configuration
cp .env.example .env
nano .env
```

Set the following production settings in `.env`:
```env
APP_NAME=LdcuNav
APP_ENV=production
APP_KEY=                            # Will be generated in Step 2
APP_DEBUG=false
APP_URL=https://api.ldcunav.liceo.edu.ph

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ldcunav_prod
DB_USERNAME=ldcunav_app
DB_PASSWORD=CHANGE_TO_A_SECURE_PASSWORD

DATABASE_URL="postgresql://ldcunav_app:CHANGE_TO_A_SECURE_PASSWORD@127.0.0.1:5432/ldcunav_prod?schema=public"

# Restrict CORS strictly to your production frontend domain
FRONTEND_ORIGIN=https://ldcunav.liceo.edu.ph

SESSION_DRIVER=array
CACHE_STORE=array
QUEUE_CONNECTION=sync
LOG_LEVEL=info
```

### Step 2: Install Dependencies & Generate Key
```bash
# Generate encryption key
php artisan key:generate --force

# Install optimized PHP dependencies
composer install --no-dev --optimize-autoloader --no-interaction
```

### Step 3: Run Prisma Migrations & Seeders
```bash
# Install local Node dependencies for Prisma CLI
npm ci --omit=dev

# Apply database migrations to PostgreSQL
npx prisma migrate deploy

# Seed baseline campus locations and demo accounts (optional in production)
php artisan db:seed --force
```

### Step 4: Cache Laravel Configurations
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Set Permissions
```bash
chown -R www-data:www-data /var/www/ldcunav/ldcunav-be/storage
chown -R www-data:www-data /var/www/ldcunav/ldcunav-be/bootstrap/cache
chmod -R 775 /var/www/ldcunav/ldcunav-be/storage
chmod -R 775 /var/www/ldcunav/ldcunav-be/bootstrap/cache
```

---

## 5. Frontend Deployment (`ldcunav-fe`)

### Step 1: Configure Environment
```bash
cd /var/www/ldcunav/ldcunav-fe

# Set production API URL
echo "VITE_API_BASE_URL=https://api.ldcunav.liceo.edu.ph/api" > .env
```

### Step 2: Install & Build
```bash
# Install dependencies
npm ci

# Compile production assets into dist/
npx vite build
```

The compiled assets will be placed in `/var/www/ldcunav/ldcunav-fe/dist/`.

---

## 6. Nginx Web Server Configuration

Below is a complete, production-ready Nginx configuration hosting both the Vue 3 SPA frontend and the Laravel REST API under standard subdomains (or unified under a single domain).

```nginx
# -----------------------------------------------------------------------------
# Frontend SPA Configuration (ldcunav.liceo.edu.ph)
# -----------------------------------------------------------------------------
server {
    listen 80;
    listen [::]:80;
    server_name ldcunav.liceo.edu.ph;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name ldcunav.liceo.edu.ph;

    ssl_certificate /etc/letsencrypt/live/ldcunav.liceo.edu.ph/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/ldcunav.liceo.edu.ph/privkey.pem;

    root /var/www/ldcunav/ldcunav-fe/dist;
    index index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; connect-src 'self' https://api.ldcunav.liceo.edu.ph;" always;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(?:ico|css|js|gif|jpe?g|png|woff2?|eot|ttf|svg)$ {
        expires 6M;
        access_log off;
        add_header Cache-Control "public, max-age=15552000, immutable";
    }
}

# -----------------------------------------------------------------------------
# Backend API Configuration (api.ldcunav.liceo.edu.ph)
# -----------------------------------------------------------------------------
server {
    listen 80;
    listen [::]:80;
    server_name api.ldcunav.liceo.edu.ph;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.ldcunav.liceo.edu.ph;

    ssl_certificate /etc/letsencrypt/live/api.ldcunav.liceo.edu.ph/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.ldcunav.liceo.edu.ph/privkey.pem;

    root /var/www/ldcunav/ldcunav-be/public;
    index index.php;

    # Security headers
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 7. Security Best Practices

1. **Disable `APP_DEBUG`**: Verify `APP_DEBUG=false` in production. Enabling debug exposes stack traces, database credentials, and internal environment details upon errors.
2. **Restrict CORS**: Never set `FRONTEND_ORIGIN=*` in production. Limit it strictly to the verified frontend origin.
3. **Database Network Isolation**: Ensure PostgreSQL port 5432 is bound to `127.0.0.1` or accessible only inside a private VPC. Never expose port 5432 to the public internet.
4. **Token Security**: Personal access tokens are stored strictly as SHA-256 hashes (`tokenHash`). Even in the event of an unauthorized database dump, plaintext bearer tokens cannot be recovered.
5. **Password Encryption**: All user passwords use strong bcrypt hashes (`BCRYPT_ROUNDS=12`).

---

## 8. Backup & Rollback Strategy

### Daily Database Backups
```bash
# Automated cron backup command
pg_dump -U ldcunav_app -h 127.0.0.1 -d ldcunav_prod -F c -b -v -f /backups/ldcunav_$(date +\%F_\%T).dump
```

### Rollback Procedure
If a release must be rolled back:
1. Revert to the prior Git release tag: `git checkout <tag>`.
2. Rebuild frontend assets: `npx vite build`.
3. If database changes occurred, restore the pre-deployment PostgreSQL snapshot:
   ```bash
   pg_restore -U ldcunav_app -d ldcunav_prod --clean /backups/pre_deploy_snapshot.dump
   ```
4. Clear and rebuild application caches: `php artisan config:cache && php artisan route:cache`.

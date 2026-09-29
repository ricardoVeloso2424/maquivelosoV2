# Deployment Guide — MaquiVeloso

Production deployment guide for the MaquiVeloso Laravel 12 application
(public site + admin backoffice, machine catalogue with image uploads and
GD thumbnails).

> This document describes **how to deploy**. It does not assume a specific
> hosting provider. Adapt paths, users and service names to your server.

---

## 1. Requirements

### Server (runtime)

| Component        | Minimum / recommended                                            |
| ---------------- | ---------------------------------------------------------------- |
| PHP              | **8.2+** (developed/tested on 8.2)                               |
| Composer         | 2.x                                                              |
| Database         | **MySQL 8.0+** or **MariaDB 10.6+**                              |
| Web server       | Nginx or Apache                                                  |
| TLS              | Valid HTTPS certificate (e.g. Let's Encrypt)                     |

### Required PHP extensions

These ship with most PHP builds; confirm with `php -m`:

- `pdo_mysql` — database
- `mbstring`, `openssl`, `tokenizer`, `ctype`, `json` — Laravel core
- `fileinfo` — upload validation (MIME detection)
- `curl`, `xml`, `dom` — framework / HTTP
- **`gd`** — image thumbnail generation (see note below)

> **GD is required for thumbnails.** Without GD the app still works: uploads
> succeed and listings fall back to the full-size original image
> (`thumb_url` → original). But thumbnails will not be generated and
> `php artisan machines:generate-thumbnails` will refuse to run with a clear
> message. Install it (Debian/Ubuntu: `sudo apt install php8.2-gd`) and
> restart PHP-FPM.

### Build toolchain (assets)

- **Node.js 20+ and npm** — only needed to **build** front-end assets
  (`npm run build` / Tailwind + Vite). Node is **not** required at runtime if
  you build locally and upload the compiled `public/build` directory.

### Not used by this project

The app currently uses **no** queue workers, scheduled tasks, jobs,
notifications or outbound email. You do **not** need a cron entry or a
`queue:work` supervisor process. (Cache, session and queue are configured to
use the database so there is no Redis dependency.) See §10.

---

## 2. Local preparation (developer machine)

Run and verify everything locally before shipping:

```bash
composer install
npm install
npm run build          # compiles public/build (Tailwind + Vite)
php artisan test       # full suite must pass
```

Commit your code (assets in `public/build` are git-ignored — see §5 for how
they reach the server).

---

## 3. First install on production

> Run these **on the server** unless marked *(local)*.

1. **Copy the code** to the server (git clone or upload a release archive).

2. **Create the environment file** from the template and edit real values:

   ```bash
   cp .env.production.example .env
   # edit .env: APP_URL, DB_*, SESSION_SECURE_COOKIE=true, MAIL_* (if used)
   ```

3. **Install PHP dependencies (no dev packages):**

   ```bash
   composer install --no-dev --optimize-autoloader
   ```

4. **Generate the application key:**

   ```bash
   php artisan key:generate
   ```

5. **Run database migrations** (creates all tables; `--force` is required in
   production because it is non-interactive):

   ```bash
   php artisan migrate --force
   ```

6. **Link public storage** (so uploaded images are reachable at `/storage/...`):

   ```bash
   php artisan storage:link
   ```

7. **Create the administrator** (interactive, no default password):

   ```bash
   php artisan admin:create
   ```

8. **Compiled assets.** Either build on the server *or* upload the locally
   built `public/build` directory:

   ```bash
   # Option A — build on the server (needs Node 20+):
   npm ci
   npm run build

   # Option B — build locally and upload the resulting public/build/ folder.
   ```

9. **(Optional) backfill thumbnails** for any pre-existing images that have
   none (safe to run anytime; requires GD):

   ```bash
   php artisan machines:generate-thumbnails
   ```

10. **Optimize framework caches** (config/route/view/event caching):

    ```bash
    php artisan optimize
    ```

11. **Set filesystem permissions** — see §6.

---

## 4. Recommended commands (and where to run them)

| Command                                              | Where   | Purpose                                  |
| ---------------------------------------------------- | ------- | ---------------------------------------- |
| `composer install --no-dev --optimize-autoloader`    | server  | Install runtime PHP deps                 |
| `php artisan key:generate`                           | server  | One-time APP_KEY (first install only)    |
| `php artisan migrate --force`                        | server  | Apply DB migrations                      |
| `php artisan storage:link`                           | server  | Symlink for public uploads (one-time)    |
| `php artisan admin:create`                           | server  | Create/promote an administrator          |
| `php artisan machines:generate-thumbnails`           | server  | Backfill missing thumbnails (needs GD)   |
| `php artisan optimize`                               | server  | Cache config/routes/views/events         |
| `npm ci` + `npm run build`                           | either  | Build front-end assets                   |
| `php artisan test`                                   | local   | Verify before deploying                  |

> Do **not** run `migrate:fresh`, `db:wipe` or demo seeders in production —
> they are destructive. The demo data seeders (`DatabaseSeeder`,
> `AdminUserSeeder`) are intentionally **no-ops outside local/testing**.

---

## 5. Future updates / releases

```bash
php artisan down                                    # maintenance mode
git pull            # (or upload the new release)
composer install --no-dev --optimize-autoloader
php artisan migrate --force                         # apply new migrations
# rebuild assets: either `npm ci && npm run build` here, or upload public/build
php artisan optimize:clear                          # drop stale caches
php artisan optimize                                # rebuild caches
php artisan up                                      # back online
```

Always rebuild/refresh caches after a deploy so changed config, routes and
views take effect.

---

## 6. Filesystem permissions

Only these directories must be writable by the web-server / PHP-FPM user
(commonly `www-data`):

- `storage/` (logs, sessions, cache, compiled views, uploaded images)
- `bootstrap/cache/` (compiled config/routes/packages)

Recommended (replace `www-data` with your PHP-FPM user):

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

> **Never use `chmod 777`.** Group-writable (`775`/`664`) owned by the PHP
> user is sufficient and safe. Everything else can stay read-only for the web
> server.

---

## 7. Web server configuration

The document root **must** point at `public/` (never the project root).

### Nginx (reference)

```nginx
server {
    listen 80;
    server_name example.com www.example.com;
    root /var/www/maquiveloso/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files such as .env
    location ~ /\.(?!well-known).* { deny all; }

    client_max_body_size 8M;   # allow image uploads (app caps each file at 5 MB)
}
```

### Apache (reference)

Laravel ships `public/.htaccess` with the rewrite rules. Ensure
`mod_rewrite` is enabled and the vhost allows overrides:

```apache
<VirtualHost *:80>
    ServerName example.com
    DocumentRoot /var/www/maquiveloso/public

    <Directory /var/www/maquiveloso/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## 8. HTTPS

1. Set `APP_URL=https://your-domain` in `.env`.
2. Set `SESSION_SECURE_COOKIE=true` (cookies only sent over HTTPS).
3. Redirect HTTP → HTTPS at the web-server/load-balancer level (e.g. Certbot's
   Nginx redirect, or a `301` redirect server block).
4. Keep `SESSION_SAME_SITE=lax` (already the default) for CSRF hardening.

After enabling TLS, run `php artisan optimize:clear && php artisan optimize`
so cached config picks up the new `APP_URL`.

---

## 9. Backups

Back up regularly and **test a restore** at least once:

| What                       | Why                                            | Frequency        |
| -------------------------- | ---------------------------------------------- | ---------------- |
| Database (`mysqldump`)     | Machines, categories, settings, users          | Daily (min.)     |
| `storage/app/public/`      | Uploaded original images **and thumbnails**    | Daily (min.)     |
| `.env`                     | `APP_KEY` + credentials (store **encrypted**)  | On every change  |

Example database dump:

```bash
mysqldump -u maquiveloso -p maquiveloso > backup-$(date +%F).sql
```

> **Restore test:** periodically restore the latest dump + `storage/app/public`
> into a staging environment and confirm the catalogue and images load. A
> backup you have never restored is not a backup you can trust.

---

## 10. Scheduler, queues & background work

This project does **not** use them. There is:

- no scheduled task (`routes/console.php` only defines the stock `inspire`
  command; no `withSchedule` is configured), so **no cron entry is required**;
- no application jobs/notifications/outbound email, so **no `queue:work`
  worker is required**.

`QUEUE_CONNECTION`/`CACHE_STORE` default to the database purely to avoid a
Redis dependency. If you later add emails or jobs, document and provision the
worker (e.g. a Supervisor program running `php artisan queue:work`) and, if you
add scheduled commands, a single cron entry:
`* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`.

---

## 11. Contact form

The contact page builds a **WhatsApp deep link** (`wa.me/...`) from the number
configured in the backoffice settings. It does **not** send email or store
messages, and it never claims a message was sent ("nada é enviado
automaticamente"). When no WhatsApp number is configured the quick-contact form
is hidden gracefully. A real contact backend (email/database) remains
**optional** and is out of scope for this release.

---

## 12. Health check & diagnostics

### Health check

Laravel's built-in health endpoint is enabled at **`GET /up`** (configured in
`bootstrap/app.php`). It returns HTTP 200 when the framework boots correctly
and exposes no sensitive information — safe to point an uptime monitor at it.

### Diagnostic commands

```bash
php artisan about            # environment, versions, cache/driver summary
php artisan migrate:status   # which migrations have run
php artisan route:list       # registered routes & middleware
php artisan config:show app  # inspect resolved config (e.g. app, auth, session)
php artisan storage:link     # (re)create the public storage symlink
php artisan test             # run the test suite (local/staging)
```

---

## 13. Rollback strategy (honest)

There is **no automatic rollback of destructive migrations**. Plan for it:

1. **Before each release**, take a fresh database dump and snapshot
   `storage/app/public` (see §9).
2. **Code rollback:** redeploy the previous release/commit, then
   `composer install --no-dev --optimize-autoloader` and
   `php artisan optimize:clear && php artisan optimize`.
3. **Database rollback:** if a migration caused problems, restore the
   pre-release dump. Do **not** rely on `php artisan migrate:rollback` for
   data-destructive changes — `down()` cannot recover dropped data.
4. Keep the app in `php artisan down` until you have confirmed the rollback
   is healthy, then `php artisan up`.

> The safest rollback is "restore the last known-good dump + previous code".
> Test this path before you need it.

---

## 14. Project-specific notes

- **Public registration is disabled by default** (`REGISTRATION_ENABLED=false`).
  Administrators are created with `php artisan admin:create`. There is **no**
  hardcoded/default admin password in production.
- **Demo seeders are local/testing only.** In production, `db:seed` creates no
  accounts (`DatabaseSeeder` and `AdminUserSeeder` short-circuit). The
  well-known `admin@maquiveloso.com / password` account exists **only** for
  local development.
- **Thumbnails** are generated with GD on upload and can be backfilled with
  `php artisan machines:generate-thumbnails [--force]`. Missing GD degrades
  gracefully to original images.
- **Image URLs are relative** (`/storage/...`), so they are domain-agnostic and
  HTTPS-safe — keep `FILESYSTEM_DISK=local` and run `php artisan storage:link`.
- **`debug` is off in production** (`APP_DEBUG=false`): users see a generic
  error page, never a stack trace.

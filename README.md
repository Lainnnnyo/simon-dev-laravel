# BYTE & BREW — Laravel POS Final Project
Developer: Simon Andrei Dimaculangan. Currency: PHP. Cyber coffee shop theme.

## Scope and architecture
Laravel 13, Breeze's generated session authentication backend, Eloquent, Blade, relational MySQL in production / SQLite locally, Intervention Image v3 with GD. Customer and staff resource controllers support create, read, update, delete; products support CRUD with archive replacing permanent delete. Sales are immutable records of one product per transaction; a customer is optional. All management, images, sales recording and history require an authenticated staff account. Staff management additionally requires an administrator. Public registration is disabled.

## Core flow
SalesController validates submitted IDs, integer quantity and UUID request key. SaleService starts a database transaction, locks staff then product and optional customer rows, rechecks account/product/customer availability and stock, computes exact total using integer cents from the database price, deducts stock, and creates the sale. A failure rolls back both writes. Unique request keys prevent duplicate submissions. MySQL InnoDB row locks serialize competing stock deductions. SQLite tests verify behavior but do not prove MySQL concurrency; run the concurrency check below on a disposable MySQL database.

Product editing includes expected_stock: an old edit form cannot overwrite stock changed by a sale. Products are archived instead of removed. Customers and staff use SoftDeletes, and sales relationships use withTrashed to retain history. Sale names and prices are snapshots and do not change after account/product edits. Uploaded product images become 800x800 JPEGs; avatars remain 256x256 JPEGs, generated UUID filenames on the public disk, served via staff-protected routes. Inputs allow JPG/PNG/WebP <=2 MB, dimensions <=4096x4096. Staff passwords use Laravel hashing, require 12+ characters on administration forms, and are not redisplayed. Breeze handles login throttling, CSRF, session regeneration, and logout invalidation. New generated accounts default to nonstaff at database level; only admin/CLI provisioning grants access.

## Main routes
Public: /, /about, /login, password reset endpoints. No /register.
Staff: /dashboard; /products resource; /customers resource; /sales (history), /sales/create, POST /sales; image routes.
Admin: /users resource. Own deletion and removal of own staff/admin access are blocked.

## Local setup from a completed source export
1. PHP 8.3+ and Composer 2. Enable required Laravel extensions plus GD, pdo_sqlite for tests and pdo_mysql for MySQL. Keep existing Composer platform.php=8.3.4 for Wasmer compatibility.
2. Run composer install. Copy .env.example to .env and run php artisan key:generate only for a NEW local installation.
3. Set local DB_CONNECTION=sqlite and DB_DATABASE to the absolute local database/database.sqlite file; create it if needed. SESSION_DRIVER=file, CACHE_STORE=file, QUEUE_CONNECTION=sync; SESSION_SECURE_COOKIE=false for local HTTP. Clear config.
4. Run php artisan migrate on LOCAL SQLite only. Run php artisan pos:create-admin and supply a new username/email and private password. Existing TFA8 public accounts do not automatically become staff.
5. Optionally run php artisan db:seed --class=ProductSeeder to add 5 coffee products without images. Add product images using Edit. Seeds use firstOrCreate and do not reset edited records or stock. Never seed sales or a known admin password.
6. php artisan serve. Login with the provisioned email/password. CSS uses a new byte-brew-final.css filename to avoid the stale TFA8 stylesheet URL. No frontend build is required by these Blade views.

## Tests
php artisan config:clear
php vendor/phpunit/phpunit/phpunit --configuration=phpunit.xml
The final test suite explicitly selects FinalPosTest; old coursework test files refer to now-removed public registration and anonymous account access and are not part of this suite. PHPUnit forces SQLite :memory:, array sessions/cache/mail and an isolated test key, even if the shell has database variables. Tests include login, guest/staff/admin restrictions, disabled registration, integer money, server-calculated totals, overstock/invalid quantities, rollback on insert failure, duplicate POST protection, archive, stale product edits, optional customer phone, historical references, password hashing, avatar cleanup and CSRF. These tests were written but NOT run in the preparation environment, which lacks PHP/Composer.

MySQL concurrency check on a disposable database: set one product stock to 1. In two different staff sessions open Record Sale and submit quantity 1 nearly simultaneously, each with its own form key. Exactly one sale should succeed; the other must reject insufficient stock. Query sales and stock: stock=0 and exactly one new sale. Repeat with a double submit of the same form key: only one sale/deduction. Do not claim this passed until performed.

## Existing Wasmer deployment
Export a DB backup first. Your old tables were created manually, so do not run migrate:fresh or the complete migration history there. Use docs/WASMER_SCHEMA.sql for the additive schema upgrade; migrations remain provided for clean installs. Keep existing users/customers/avatar schema from TFA7, including unsigned BIGINT primary keys and InnoDB. If schema differs, stop and resolve the error rather than dropping tables. Manual production schema and migration history remain separate; document this if asked.
Generate a new hosted administrator locally using php artisan pos:create-admin --sql. It writes only a password HASH and profile fields to storage/app/private/admin-bootstrap.sql. Import once using Wasmer Database Explorer. Choose a new email and username not already in the hosted DB. Never commit that private file. Do not create an unauthenticated setup web route.
Keep DB_CONNECTION=mysql, DB_DATABASE=simon_pos, and the database's actual host/port/username/password. Keep the private APP_KEY; APP_ENV=production, APP_DEBUG=false, APP_URL=https://simon-pos-tfa5.wasmer.app. Set SESSION_DRIVER=database and CACHE_STORE=database after the supplied tables exist; this shares sessions and login throttling across workers. QUEUE_CONNECTION=sync, LOG_CHANNEL=stderr. For HTTPS: SESSION_SECURE_COOKIE=true, SESSION_HTTP_ONLY=true, SESSION_SAME_SITE=lax. MAIL_* must use a real mail service for password reset; do not expose reset tokens or debug logs. Basic login does not send mail.
Keep the volume /data/pos-public and PUBLIC_DISK_ROOT=/data/pos-public. Uploaded images must remain after a deployment. PHP GD must work on Wasmer. Build Composer dependencies according to composer.lock without ignoring platform requirements. Commit generated Breeze Auth controllers, LoginRequest and routes/auth.php; the production runtime does not require the dev-only Breeze package once scaffolding exists. Do not cache configuration with local environment values.

## Submission: exactly four entries
1. GitHub link: https://github.com/Lainnnnyo/simon-dev-laravel (verify updated source is pushed).
2. Complete project ZIP from export-submission.ps1 after applying and testing the upgrade. Excludes .env, vendor, node_modules, .git, SQLite DBs, runtime caches, logs and uploaded/private files. Includes .env.example, composer.lock, source, assets, migrations and seeders.
3. Deployment: https://simon-pos-tfa5.wasmer.app/ (verify the final release yourself).
4. Documentation PDF: complete docs/documentation.html with your actual screenshots, section, professor, member list and personal reflection; print to PDF from your browser. A DRAFT reference PDF is provided, not final evidence.

## Honest limitations
No payments, refunds, discounts, taxes, multi-item cart, or automated stock notifications are claimed. PHP tests, MySQL concurrency, runtime UI and hosting were not executed here. No live database was changed and no Git push was performed. Final hosted evidence and member reflection must come from the actual working app and your experience. All figures in sample tests are synthetic.

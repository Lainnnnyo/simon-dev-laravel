# Byte & Brew — Simon POS / TFA8
Developer: Simon Andrei Dimaculangan. Cyber coffee shop account-management coursework.

## Features
- Public Home and Our story pages with espresso, cream, copper, and neon teal design.
- Breeze login, registration, logout, login throttling, password hashing and session regeneration.
- One resource registration each for customers and users: index/create/store/show/edit/update/destroy.
- Auth middleware protects every customer/user action, the dashboard, and avatar delivery.
- Delete confirmation + CSRF + DELETE verb. Server rejects self-deletion. Deleted user avatars are removed; storage failures are reported without undoing the already-completed database deletion.
- TFA7 validation and 256x256 JPEG avatar processing retained, including 2 MB and 4096x4096 input limits.
- Registration saves username, full_name, name, email, and hashed password. Login uses email.

## Breeze installation provenance
Composer installs laravel/breeze (^2.4 in the supplied project). The upgrade installer copies the official `vendor/laravel/breeze/stubs/default/app/Http/Controllers/Auth`, `app/Http/Requests/Auth`, and `routes/auth.php` into the application. This is a selective installation of Breeze's real Blade backend scaffold, NOT an independently invented authentication system. It then replaces only RegisteredUserController to populate the POS fields and uses custom Blade views with plain CSS. The default login throttling, logout/session invalidation, password broker, confirmation and password rules remain in Breeze's generated code.

Do not run breeze:install over the finished project: it would overwrite the customized routes/views. No Tailwind, Alpine, or Vite build is required by the themed views. Existing package.json/Vite files may remain from the original skeleton; their Node requirements do not affect these plain-CSS pages. External Google Fonts are optional with local fallback fonts.

## Setup from this GitHub project
1. Install PHP 8.3+ with required Laravel extensions, pdo_sqlite for local tests and GD for avatars; Composer 2.
2. `composer install` (do not bypass platform checks). Preserve the PHP 8.3.4 platform constraint for hosted compatibility.
3. Copy .env.example to .env if needed and run `php artisan key:generate` once for a new install.
4. Configure LOCAL SQLite (DB_CONNECTION=sqlite, DB_DATABASE=<absolute local database/database.sqlite path>), create that file if absent, clear config, then `php artisan migrate` and `php artisan db:seed --class=PosSeeder` on a new local database only.
5. Set SESSION_DRIVER=file, CACHE_STORE=file, QUEUE_CONNECTION=sync. Run `php artisan serve`. No npm build needed for this UI.
6. Open /register and create your own account with a new email and username. Demo POS seed accounts have random/unusable passwords; do not try them as logins.
7. Run `php artisan config:clear`, then `php vendor/phpunit/phpunit/phpunit --configuration=phpunit.xml`. Config forces SQLite :memory: and testing mail/cache/session; tests must never use the hosted DB.

## Access model
This assignment uses authentication, not admin roles: every registered account can manage customers and other users. Registration is public as the assignment requests. This is a coursework demonstration, not a production-ready restricted staff system. Login uses email; user records made through the POS Create User form still receive an internal placeholder email and an unknown random password. Use /register for login-capable accounts. No shared/default admin password is provided.

## Wasmer
- Keep DB_CONNECTION=mysql and DB_DATABASE equal to your actual Wasmer DB_NAME (currently simon_pos). Keep host/port/username/password from the current database connection.
- APP_ENV=production, APP_DEBUG=false, APP_URL=https://simon-pos-tfa5.wasmer.app; keep the existing private APP_KEY.
- SESSION_DRIVER=file, CACHE_STORE=file, QUEUE_CONNECTION=sync, LOG_CHANNEL=stderr. For HTTPS set SESSION_SECURE_COOKIE=true, SESSION_HTTP_ONLY=true, SESSION_SAME_SITE=lax.
- Keep the persistent avatar volume mounted at /data/pos-public and PUBLIC_DISK_ROOT=/data/pos-public. GD must work in the hosted PHP runtime.
- File sessions depend on the host filesystem; if login is lost across workers/restarts, use a shared session store. The upgrade does not claim durable sessions from the avatar volume.
- Hosted users/customers tables were previously made manually. Do not run all migrations or migrate:fresh on that database. No new users/customer columns are needed for TFA8 beyond the existing TFA7 avatar column.
- Password reset requires password_reset_tokens. If absent, run the supplied wasmer-password-reset-table.sql in Database Explorer. The standard initial migration already includes it for fresh/local installs.
- Password reset emails need a real MAIL_* transport on Wasmer. Do not publish reset-token logs. Login/registration/logout do not require email delivery; email verification is not enforced by this assignment.
- Push the generated app/Http/Controllers/Auth, app/Http/Requests/Auth, routes/auth.php and customized source files, tests, README, composer.json and composer.lock. Do not commit .env, vendor, SQLite data, uploaded avatars, or generated bootstrap/cache/*.php.

## Verify before submitting
1. Logged out: /customers, /users, create/edit/show and write actions redirect to /login.
2. Register a new account; log out; log in with its email/password. Confirm intended URL redirect.
3. Create, view, edit and delete a disposable customer and user. Cancel a Delete prompt once; then confirm. Your own user has no Delete action and direct self-delete is rejected.
4. Invalid email/duplicate username/empty required fields show errors and retain appropriate inputs. Passwords are not repopulated.
5. Upload a <=2 MB JPG/PNG/WebP. Confirm image appears. Delete the test user and confirm its avatar file is removed. Redeploy and verify a retained avatar still loads.
6. Confirm hosted login/logout work in an incognito browser; never enable public APP_DEBUG to troubleshoot.

## Submission
GitHub raw project files plus migrations/seeders and hosted URL: https://simon-pos-tfa5.wasmer.app/
Repository: https://github.com/Lainnnnyo/simon-dev-laravel

## Validation limits
The upgrade was reviewed and statically checked in the preparation environment, which did not have PHP or Composer. PHPUnit and real Breeze execution must pass on your computer before deployment. Browser preview could not run in the restricted preparation environment; check the theme at desktop and mobile widths locally. No live database or deployed app was modified during preparation. Intervention Image instructor approval from TFA7 remains a separate requirement. Delete is included by explicit request for TFA8.

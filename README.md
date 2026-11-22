# Simon Dev POS — TFA6: From Arrays to Eloquent
Developer: Simon Andrei Dimaculangan

## What this upgrade does
Preserves the TFA5 routes, shared layout, homepage, about page and navy/teal CSS. Replaces the two static-array controllers with Eloquent queries and updates the tables to display model properties. Customer fields are full_name, email and phone. User fields are unique username and full_name. The earlier Role column is removed because TFA6 does not request it. No login or editing interface is required or included.

This is an upgrade overlay for the standard Laravel project generated during TFA5, not a standalone framework download. Keep your current composer.json and composer.lock, including the Wasmer PHP compatibility fix. Do not rerun the TFA5 installer.

## Apply on Windows
Stop php artisan serve with Ctrl+C. Extract this archive. Open PowerShell in the folder containing apply-upgrade.ps1 and source, then run:

    powershell -ExecutionPolicy Bypass -File .\apply-upgrade.ps1 -ProjectPath "C:\Users\PC\Desktop\Simon_Dev_Laravel_TFA5\simon-dev-laravel"

The script backs up replaced files to a timestamped folder beside the project. It checks that the default users migration exists and refuses a schema that already has username/full_name/customer migrations. If it stops, inspect the message rather than force overwriting. It does not run migrations, change .env, modify dependencies, or push to GitHub.

## Local database setup (SQLite)
SQLite is a real database and is sufficient for the activity; migrations also use portable schema operations for MySQL. In the project folder:

    php -m | Select-String pdo_sqlite

If absent, open the php.ini reported by php --ini and enable extension=pdo_sqlite. In .env set DB_CONNECTION=sqlite. Remove or comment out any active DB_URL and DB_DATABASE lines so Laravel uses database/database.sqlite. Keep SESSION_DRIVER=file, CACHE_STORE=file and QUEUE_CONNECTION=sync. Do not share .env or commit it.

Create the file only if it does not already exist:

    if (!(Test-Path .\database\database.sqlite)) { New-Item .\database\database.sqlite -ItemType File | Out-Null }
    php artisan config:clear
    php artisan migrate
    php artisan db:seed --class=PosSeeder
    php artisan migrate:status

Run each command separately; stop on an error. migrate preserves existing tables/data while adding the new schema. Never use migrate:fresh here: it deletes tables. If you have valuable database data, back it up before migrations. No old CodeIgniter database is imported or modified.

## Verify
    php artisan test --configuration=phpunit.tfa6.xml
    php artisan serve

Open http://127.0.0.1:8000 and check all four pages. Customers and users each show at least five sample records. A pre-existing Laravel test user may also appear, with a generated legacy_user_ID username. Tests use a separate in-memory SQLite database; they check DB-driven rendering, changes to records, repeatable seeding, empty tables and unique usernames.

Seeder reruns add only missing samples by email/username. They do not reset edits or passwords. Model creation requires the requested fields in application code. The new users columns allow NULL for compatibility with default Laravel factories; existing names are backfilled. The original Laravel users migration creates the table; the new migration adds the assignment fields without creating a duplicate users table. Standard name/email/password columns remain for framework compatibility. No usable demo login password is provided.

## GitHub
After local tests pass, review git status and commit only the upgrade files:

    git add app/Models/Customer.php app/Models/User.php app/Http/Controllers/CustomerController.php app/Http/Controllers/UserController.php database/migrations/2026_11_21_000001_create_customers_table.php database/migrations/2026_11_21_000002_add_pos_fields_to_users_table.php database/seeders/PosSeeder.php database/seeders/DatabaseSeeder.php resources/views/customers/index.blade.php resources/views/users/index.blade.php tests/Feature/PosPagesTest.php phpunit.tfa6.xml README.md
    git commit -m "Use Eloquent models and seeded database for TFA6"
    git push

The original default users migration MUST remain in GitHub. Do not commit .env, the SQLite database, vendor, or backup directories. Submission requires the full project repository URL and a working hosted URL. This upgrade does not publish anything automatically.

## Hosted database / Wasmer
The previously unresolved Wasmer build issue must be fixed independently. A hosted TFA6 app now needs a persistent database. Local SQLite is not automatically transferred into Wasmer, and an ephemeral deployment filesystem is unsuitable for persistent records. Provision a persistent hosted MySQL-compatible database (or verify the actual type Wasmer offers), then set the matching DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME and DB_PASSWORD privately. Follow the database provider's TLS requirements. Do not guess credentials or use local SQLite on the host without a persistent volume.

On the deployed environment using the correct database connection, run:

    php artisan migrate --force
    php artisan db:seed --class=PosSeeder --force

Use the hosting provider's supported release command or a trusted local CLI configured for that database. Do not create public web routes that run migrations. Keep APP_ENV=production and APP_DEBUG=false. Confirm both account pages query the hosted database, then add the real site URL and GitHub URL to this README before submission.

## Validation status
Files and schema logic reviewed during preparation. PHP and Composer were unavailable in the preparation workspace, so the included tests have not been executed here. Local test success and hosted verification are still required.

References: https://laravel.com/docs/13.x/eloquent ; https://laravel.com/docs/13.x/migrations ; https://laravel.com/docs/13.x/seeding

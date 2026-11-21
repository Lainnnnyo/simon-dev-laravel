# Simon Dev POS — Laravel TFA5
Developer: Simon Andrei Dimaculangan

Converted from the uploaded CodeIgniter Simon Dev POS, preserving its navy/teal styling. This TFA5 edition has four public pages, PageController, CustomerController, UserController, five fictional records in each account controller, and Blade @foreach tables. Tasks, authentication and editable database records belong to the later CodeIgniter activity and are not included in this four-page exercise.

## Windows setup
Install PHP 8.3+ and Composer 2, available on PATH, with Laravel-required extensions and PDO SQLite. Internet access is needed. Extract this ZIP, open PowerShell in the extracted folder and run:

    powershell -ExecutionPolicy Bypass -File .\setup.ps1
    cd .\simon-dev-laravel
    php artisan serve

Open http://127.0.0.1:8000 . The setup creates the official Laravel 13 skeleton and copies source/ into it. It refuses to overwrite an existing project folder. This ZIP contains the application overlay and installer, not downloaded framework dependencies. If setup fails, read the error; remove only the newly created incomplete simon-dev-laravel directory before retrying.

## Inspect and verify
    php artisan route:list --except-vendor
    php artisan view:cache
    php artisan test --filter=PosPagesTest

Open /, /about, /customers and /users and follow every navigation link. Confirm five rows per account table and check at mobile width. PHP/Composer were unavailable in the preparation environment, so runtime tests and hosting remain unverified.

## Database / submission
The account listings deliberately use static arrays as the assignment requires. Keep the official skeleton's database/migrations, database/seeders and database/factories in the GitHub submission to satisfy the request for migrations/seeder files. These default database files do not supply the displayed account records. No private database export is needed. Setup changes sessions and cache to file storage; queue uses sync. No database server or frontend build is needed to display the four pages.

Commit the CONTENTS of the generated simon-dev-laravel folder, including composer.json, composer.lock, README.md, app, routes, resources, public, config, bootstrap, artisan, tests and database. Do not submit just source/. Keep .env and vendor out of Git. Add your actual hosted URL and GitHub URL to this README once deployed.

## Wasmer deployment handoff
Create a separate Laravel-compatible application for this version. The old CodeIgniter runtime configuration is not interchangeable with Laravel. Before deploying, verify the Wasmer PHP runtime supports PHP 8.3+ and the extensions required by composer.lock. Configure the document root as public/ and route non-file requests to public/index.php. Use HTTPS for APP_URL and set APP_ENV=production, APP_DEBUG=false, SESSION_DRIVER=file, CACHE_STORE=file, QUEUE_CONNECTION=sync. Supply a fresh APP_KEY privately (php artisan key:generate --show). storage/ and bootstrap/cache/ must be writable. Install dependencies with composer install --no-dev --optimize-autoloader in a compatible build environment. No Wasmer manifest is included because the current Wasmer runtime/template has not been verified. Never expose the project root or .env. Verify all four URLs after deployment. The existing live site has not been modified.

## MVC explanation
routes/web.php maps URLs to controller methods. Controllers choose views and define temporary account arrays. resources/views contains Blade presentation templates; the shared layout holds navigation. public/css/site.css carries the original design. A database model is unnecessary for the required static data.

Official reference: https://laravel.com/docs/13.x/installation

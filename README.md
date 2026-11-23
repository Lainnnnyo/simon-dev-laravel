# Simon Dev POS — TFA7
Developer: Simon Andrei Dimaculangan

Extends the uploaded TFA6 project with create/edit customers and users, validation, old input, CSRF-protected forms, and avatar thumbnails. No delete or authentication workflow is part of this exercise. These account-editing pages are public; use fictional coursework data only.

## Image package
Intervention Image v3 with GD is selected. Obtain instructor approval for this choice before submission. Incoming JPEG/PNG/WebP images must be <=2048 KB and <=4096 pixels per side. Intervention creates a 256x256 JPEG. Storage::disk('public') stores a UUID-named file; only its relative path is saved in users.avatar. Existing image remains on edits without uploads. Old files are removed after a successful replacement. Placeholder shown when no image exists. Browser file inputs must be reselected following validation errors; text input is retained.

## Install into existing project
1. Close any PowerShell window previously configured with remote DB environment variables. Start a NEW one for local testing.
2. Extract the upgrade ZIP and open PowerShell alongside apply-upgrade.ps1. Run:

    powershell -ExecutionPolicy Bypass -File .\apply-upgrade.ps1 -ProjectPath "C:\Users\PC\Desktop\Simon_Dev_Laravel_TFA5\simon-dev-laravel"

3. Switch to that project folder. Enable extension=gd in the PHP ini shown by php --ini, then verify php -m shows gd, fileinfo, mbstring and pdo_sqlite. Set upload_max_filesize=2M and post_max_size=8M or higher. Restart php artisan serve after PHP ini changes.
4. Run separately, stopping on errors:

    composer require intervention/image:~3.11.0
    php artisan config:clear
    php artisan migrate
    php .\vendor\phpunit\phpunit\phpunit --configuration=phpunit.tfa7.xml
    php artisan serve

Composer updates composer.json and composer.lock together and preserves the uploaded PHP 8.3.4 platform target. Do NOT bypass platform requirements. No dependencies or lock files are overwritten by this overlay. Confirm your local .env still uses SQLite before migrations. No migrate:fresh or data deletion is needed.

## Test in browser
/customers/create: reject blank name, invalid email and duplicate email. Verify old text survives errors, then create and edit a valid customer. Phone is required because the existing schema makes it non-null.
/users/create: reject duplicate username and missing full name. Edit a user with unchanged username, then try one belonging to another user. Upload a valid image <=2MB from Edit; confirm thumbnail displays. Reject a text file and oversized image. Edit without uploading to retain the avatar. Check Home, About, both listings and missing-record 404s.
The retained Laravel name/email/password fields are populated internally for new users. Generated email is unique, and a random hashed password is never exposed; this is not a login system.

## Hosted database: manually created TFA6 tables
Your hosted database was created manually, so do NOT run all migrations against it. In Wasmer database explorer, first check:

    SHOW COLUMNS FROM users LIKE 'avatar';

If there is no result, execute ONCE:

    ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL;

If it exists, do not rerun ALTER. Keep the avatar migration in GitHub for clean local installs. Hosted historical migration reconciliation is still outstanding; this ALTER does not pretend earlier migrations ran.

## Wasmer avatar persistence
A hosted writable persistent volume is required; an ordinary deployment directory can lose uploads. In the existing app configuration, merge (do not replace the rest of app.yaml):

    volumes:
      - name: pos-avatars
        mount: /data/pos-public

Then set PUBLIC_DISK_ROOT=/data/pos-public in Wasmer environment variables. If using dashboard storage controls, create the volume with the equivalent mount. Never rename/remove a volume after uploading data. Verify available plan/region support before creating it. The exact existing app.yaml was not in your ZIP; no replacement manifest is shipped.

Uploads are served by /avatars/{uuid}.jpg via the same public Storage disk, so a storage symlink is not required. The route only accepts generated JPEG filenames, not arbitrary paths. Keep the public disk local with the persistent root; this implementation's serving route does not require public bucket credentials.

Wasmer's PHP runtime must support GD and JPEG decoding/encoding, fileinfo and mbstring. Local GD availability does not prove the hosted runtime has it. If upload fails, inspect runtime logs before changing libraries. Retain DB_CONNECTION=mysql, DB_DATABASE=simon_pos and current Wasmer database credentials; SESSION_DRIVER=file, CACHE_STORE=file, APP_DEBUG=false. Test upload, refresh, and redeploy: the same avatar must still load.

## Publish after local tests and hosting preparation
Review git status. Stage the specific changed files from this overlay plus composer.json/composer.lock. Never add .env, vendor, databases or private cached config. Commit and push to your existing repository. Make the hosted avatar-column change before deploying routes that read it. Add actual repo and hosted URL to your submission.

Existing URLs:
https://github.com/Lainnnnyo/simon-dev-laravel
https://simon-pos-tfa5.wasmer.app/

## Verification status
Generated against the uploaded project. Structure reviewed; PHP/Composer are not installed in the preparation environment. Automated tests are included but NOT run here. Hosting volume, GD support, upload limits and instructor package approval remain to be verified. Do not claim the hosted upload requirement is complete until a real upload survives redeployment.

References:
https://image.intervention.io/v3/basics/configuration-drivers
https://image.intervention.io/v3/modifying-images/resizing
https://docs.wasmer.io/edge/learn/volumes/

<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): \Illuminate\Foundation\Application
    {
        // Windows can retain inherited DB settings in $_SERVER even when
        // PHPUnit replaces $_ENV. Set all three before Laravel boots.
        $settings = [
            'APP_ENV' => 'testing',
            'APP_KEY' => 'base64:QUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUE=',
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:',
            'DB_URL' => '',
            'SESSION_DRIVER' => 'array',
            'CACHE_STORE' => 'array',
            'MAIL_MAILER' => 'array',
            'QUEUE_CONNECTION' => 'sync',
        ];
        foreach ($settings as $key => $value) {
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv($key.'='.$value);
        }

        $app = parent::createApplication();

        // This executes BEFORE setUpTraits / RefreshDatabase can migrate.
        // Refuse stale cached configuration rather than touching a real DB.
        if ($app->configurationIsCached()
            || $app['config']->get('database.default') !== 'sqlite'
            || $app['config']->get('database.connections.sqlite.database') !== ':memory:'
            || !empty($app['config']->get('database.connections.sqlite.url'))) {
            throw new \RuntimeException(
                'Tests stopped: database is not isolated. Run php artisan config:clear and retry.'
            );
        }

        $connection = $app['db']->connection();
        if ($connection->getDriverName() !== 'sqlite') {
            throw new \RuntimeException('Tests stopped: only in-memory SQLite is allowed.');
        }
        foreach ($connection->select('PRAGMA database_list') as $database) {
            if (!empty($database->file)) {
                throw new \RuntimeException('Tests stopped: a file-backed database is attached.');
            }
        }

        return $app;
    }
}

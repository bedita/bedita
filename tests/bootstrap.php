<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
require dirname(__DIR__) . '/vendor/autoload.php';

$_SERVER['PHP_SELF'] = '/';

use BEdita\App\Application;
use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\Fixture\SchemaLoader;
use Cake\Utility\Hash;
use Migrations\TestSuite\Migrator;

$app = new Application(dirname(__DIR__) . '/config');
$app->bootstrap();
$app->pluginBootstrap();

TableRegistry::getTableLocator()->clear();

if (getenv('db_dsn')) {
    ConnectionManager::drop('test');
    ConnectionManager::setConfig('test', ['url' => getenv('db_dsn')]);
}
if (!defined('API_KEY')) {
    define('API_KEY', 'API_KEY');
}

Cache::drop('_bedita_object_types_');
Cache::setConfig('_bedita_object_types_', ['className' => 'Null']);

if (getenv('DEBUG_LOG_QUERIES')) {
    ConnectionManager::get('test')->logQueries(true);
    Log::setConfig('queries', [
        'className' => 'Console',
        'stream' => 'php://stdout',
        'scopes' => ['queriesLog'],
    ]);
}

$now = FrozenTime::parse('2018-01-01T00:00:00Z');
FrozenTime::setTestNow($now);
FrozenDate::setTestNow($now);

FilesystemRegistry::dropAll();
Configure::write('Filesystem', [
    'default' => [
        'className' => 'BEdita/Core.Local',
        'path' => Plugin::path('BEdita/Core') . 'tests' . DS . 'uploads',
        'baseUrl' => 'https://static.example.org/files',
    ],
    'thumbnails' => [
        'className' => 'BEdita/Core.Local',
        'path' => Plugin::path('BEdita/Core') . 'tests' . DS . 'thumbnails',
        'baseUrl' => 'https://static.example.org/thumbs',
    ],
]);
Configure::write('Thumbnails', [
    'allowAny' => false,
    'presets' => [
        'default' => [
            'generator' => 'async',
            'w' => 100,
            'h' => 100,
        ],
        'favicon-sync' => [
            'generator' => 'default',
            'w' => 16,
            'h' => 16,
            'fm' => 'png',
        ],
    ],
    'generators' => [
        'default' => [
            'className' => 'BEdita/Core.Glide',
        ],
        'async' => [
            'className' => 'BEdita/Core.Async',
        ],
    ],
]);
Configure::write('debug', true);

Configure::write('Plugins', []);

Cache::clear('_cake_core_');
Cache::clear('_cake_model_');

/*
 * Load schema.
 * First load fake schema for specific test purpose
 * then it runs BEdita/Core migrations avoiding to drop tables creating by fake schema.
 * Schema is loaded in unit test context but not in phpstan context.
 */
if (defined('UNIT_TEST_RUN')) {
    $fakeSchemaPath = dirname(__DIR__) . '/plugins/BEdita/Core/tests/fake_schema.php';
    $schemaLoader = new SchemaLoader();
    $schemaLoader->loadInternalFile($fakeSchemaPath);

    $fakeTables = include $fakeSchemaPath;
    $fakeTables = Hash::extract((array)$fakeTables, '{n}.table');

    $migrator = new Migrator();

    // Run migrations for multiple plugins
    $migrator->run([
        'plugin' => 'BEdita/Core',
        'skip' => $fakeTables,
    ]);
}

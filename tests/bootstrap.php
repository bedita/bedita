<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
require dirname(__DIR__) . '/vendor/autoload.php';

require dirname(__DIR__) . '/config/bootstrap.php';

$_SERVER['PHP_SELF'] = '/';

use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;

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

FilesystemRegistry::dropAll();
Configure::write('Filesystem', [
    'default' => [
        'className' => 'BEdita/Core.Local',
        'path' => Plugin::path('BEdita/Core') . DS . 'tests' . DS . 'uploads',
        'baseUrl' => 'https://static.example.org/files',
    ],
]);
Configure::write('debug', true);

Cache::clear(false, '_cake_core_');
Cache::clear(false, '_cake_model_');

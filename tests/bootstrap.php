<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bootstrap.php';

$_SERVER['PHP_SELF'] = '/';

use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;

if (getenv('db_dsn')) {
    ConnectionManager::drop('test');
    ConnectionManager::config('test', ['url' => getenv('db_dsn')]);
}
if (!defined('API_KEY')) {
    define('API_KEY', 'API_KEY');
}

if (getenv('DEBUG_LOG_QUERIES')) {
    ConnectionManager::get('test')->logQueries(true);
    Log::config('queries', [
        'className' => 'Console',
        'stream' => 'php://stdout',
        'scopes' => ['queriesLog'],
    ]);
}

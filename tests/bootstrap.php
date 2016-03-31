<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bootstrap.php';

use Cake\Core\Configure;
use Cake\Core\Configure\Engine\JsonConfig;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;

if (getenv('db_dsn')) {
    ConnectionManager::drop('test');
    ConnectionManager::config('test', ['url' => getenv('db_dsn')]);
}

// #820 Temporarily use `ROOT/plugins/BEdita/Core/config/be4-schema.json` to obtain schema.
Configure::write('schema', (new JsonConfig())->read('BEdita/Core.schema/be4-schema'));

if (getenv('DEBUG_LOG_QUERIES')) {
    ConnectionManager::get('test')->logQueries(true);
    Log::config('queries', [
        'className' => 'Console',
        'stream' => 'php://stdout',
        'scopes' => ['queriesLog'],
    ]);
}

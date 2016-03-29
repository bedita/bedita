<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bootstrap.php';

use Cake\Datasource\ConnectionManager;

if (getenv('db_dsn')) {
    ConnectionManager::drop('test');
    ConnectionManager::config('test', ['url' => getenv('db_dsn')]);
}

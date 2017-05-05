<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

// @codingStandardsIgnoreFile
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);
    throw new Exception('Cannot find the root of the application, unable to run tests');
};
$root = $findRoot(__FILE__);
unset($findRoot);
chdir($root);
require_once 'vendor/cakephp/cakephp/src/basics.php';
require_once 'vendor/autoload.php';
define('ROOT', $root . DS . 'tests' . DS . 'test_app' . DS);
define('APP', ROOT . 'App' . DS);
define('TMP', sys_get_temp_dir() . DS);
Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'App',
    'paths' => [
        'plugins' => [ROOT . 'Plugin' . DS],
        'templates' => [ROOT . 'App' . DS . 'Template' . DS]
    ]
]);
if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite:///:memory:');
}
ConnectionManager::setConfig('test', ['url' => getenv('db_dsn')]);
Plugin::load('BEdita/Core', [
    'path' => dirname(dirname(__FILE__)) . DS,
]);

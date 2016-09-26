<?php

use BEdita\Core\Configure\Engine\DatabaseConfig;
use BEdita\Core\ORM\Locator\TableLocator;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\IniConfig;
use Cake\ORM\TableRegistry;

/**
 * Register tables.
 */
TableRegistry::locator(new TableLocator());

/**
 * Load 'core' configuration parameters
 */
Configure::config('database', new DatabaseConfig());
if (!defined('UNIT_TEST_RUN') && (PHP_SAPI !== 'cli')) {
    Configure::load('core', 'database');
}

/**
 * Load BEdita meta config.
 */
if (!Configure::configured('ini')) {
    Configure::config('ini', new IniConfig());
}
Configure::load('BEdita/Core.bedita', 'ini');

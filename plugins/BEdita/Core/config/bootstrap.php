<?php

use BEdita\Core\Configure\Engine\DatabaseConfig;
use BEdita\Core\ORM\Locator\TableLocator;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\IniConfig;
use Cake\ORM\TableRegistry;

/**
 * Plug table locator.
 */
TableRegistry::locator(new TableLocator());

/**
 * Default user with id = 1 for unit tests
 */
if (defined('UNIT_TEST_RUN')) {
    LoggedUser::setUser(['id' => 1]);
}

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

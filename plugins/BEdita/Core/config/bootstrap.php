<?php

use BEdita\Core\Configure\Engine\DatabaseConfig;
use BEdita\Core\Database\Type\DateTimeType;
use BEdita\Core\ORM\Locator\TableLocator;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\IniConfig;
use Cake\Database\Type;
use Cake\ORM\TableRegistry;

/**
 * Plug table locator.
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

/**
 * Use custom DateTimeType
 */
Type::set('datetime', new DateTimeType());
Type::set('timestamp', new DateTimeType());

Configure::load('BEdita/Core.bedita', 'ini');

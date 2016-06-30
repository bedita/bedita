<?php

use BEdita\Core\Configure\Engine\DatabaseConfig;
use Cake\Core\Configure;
use Cake\Database\Type;
use Cake\ORM\TableRegistry;

/**
 * Map custom types.
 */
Type::map('json', 'BEdita\Core\Database\Type\JsonType');

/**
 * Register tables.
 */
TableRegistry::config('AuthProviders', ['className' => 'BEdita/Core.AuthProviders']);
TableRegistry::config('ExternalAuth', ['className' => 'BEdita/Core.ExternalAuth']);
TableRegistry::config('Config', ['className' => 'BEdita/Core.Config']);
TableRegistry::config('Roles', ['className' => 'BEdita/Core.Roles']);
TableRegistry::config('Users', ['className' => 'BEdita/Core.Users']);

/**
 * Load 'core' configuration parameters
 */
Configure::config('database', new DatabaseConfig());
if (!defined('UNIT_TEST_RUN') && (PHP_SAPI !== 'cli')) {
    Configure::load('core', 'database');
}

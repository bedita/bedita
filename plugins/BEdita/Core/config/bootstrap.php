<?php

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

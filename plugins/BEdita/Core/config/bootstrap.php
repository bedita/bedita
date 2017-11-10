<?php

use BEdita\Core\Configure\Engine\DatabaseConfig;
use BEdita\Core\Database\Type\BoolType;
use BEdita\Core\Database\Type\DateTimeType;
use BEdita\Core\I18n\MessagesFileLoader;
use BEdita\Core\ORM\Locator\TableLocator;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\IniConfig;
use Cake\Database\Type;
use Cake\I18n\ChainMessagesLoader;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;

/**
 * Plug table locator.
 */
TableRegistry::setTableLocator(new TableLocator());

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

/**
 * Use custom BoolType
 */
Type::set('boolean', new BoolType());

/**
 * Set loader for translation domain "bedita".
 */
I18n::translators()->registerLoader('bedita', function ($name, $locale) {
    $chain = new ChainMessagesLoader([
        new MessagesFileLoader($name, $locale, 'mo', ['BEdita/Core', 'BEdita/API']),
        new MessagesFileLoader($name, $locale, 'po', ['BEdita/Core', 'BEdita/API']),
    ]);

    return function () use ($chain) {
        $package = $chain();
        $package->setFormatter('default');

        return $package;
    };
});

Configure::load('BEdita/Core.bedita', 'ini');

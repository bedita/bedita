<?php

use BEdita\Core\Configure\Engine\DatabaseConfig;
use BEdita\Core\Database\Type\BoolType;
use BEdita\Core\Database\Type\DateTimeType;
use BEdita\Core\Database\Type\DateType;
use BEdita\Core\Database\Type\JsonObjectType;
use BEdita\Core\Filesystem\Thumbnail;
use BEdita\Core\I18n\MessagesFileLoader;
use BEdita\Core\ORM\Locator\TableLocator;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\IniConfig;
use Cake\Database\Type;
use Cake\I18n\ChainMessagesLoader;
use Cake\I18n\Date;
use Cake\I18n\FrozenDate;
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
if (!defined('UNIT_TEST_RUN') && !in_array(PHP_SAPI, ['cli', 'phpdbg'])) {
    Configure::load('core', 'database');
}

/**
 * Load BEdita meta config.
 */
if (!Configure::isConfigured('ini')) {
    Configure::config('ini', new IniConfig());
}

/*
 * Enable immutable time objects in the ORM.
 *
 * You can enable default locale format parsing by adding calls
 * to `useLocaleParser()`. This enables the automatic conversion of
 * locale specific date formats. For details see
 * @link https://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html#parsing-localized-datetime-data
 */
// Type::build('time')
//     ->useImmutable();
// Type::build('date')
//     ->useImmutable();
// Type::build('datetime')
//     ->useImmutable();

/*
 * Set the default format used converting a date to json
 */
FrozenDate::setJsonEncodeFormat('yyyy-MM-dd');
Date::setJsonEncodeFormat('yyyy-MM-dd');

/**
 * Use custom DateType & DateTimeType
 *
 * Timezone conversion is not currently supported in CakePHP 3.8.x saving to database-
 * We should use `timezone` as defined in `Datasource` configuration, with 'UTC' as default.
 * For now is safe to use system default set in main bootstrap (defaults to 'UTC').
 *
 * See https://github.com/cakephp/cakephp/issues/13646
 */
Type::set('date', new DateType());
Type::set('datetime', (new DateTimeType())->setDatabaseTimezone(date_default_timezone_get()));
Type::set('timestamp', (new DateTimeType())->setDatabaseTimezone(date_default_timezone_get()));

/**
 * Use custom BoolType
 */
Type::set('boolean', new BoolType());

/**
 * Register custom JSON Object type.
 */
Type::map('jsonobject', JsonObjectType::class);

/**
 * Set loader for translation domain "bedita".
 */
I18n::translators()->registerLoader('bedita', function ($name, $locale) {
    $chain = new ChainMessagesLoader([
        new MessagesFileLoader($name, $locale, 'mo', ['BEdita/Core', 'BEdita/API']),
        new MessagesFileLoader($name, $locale, 'po', ['BEdita/Core', 'BEdita/API']),
    ]);
    $package = $chain();
    $package->setFormatter('default');

    return $package;
});

Configure::load('BEdita/Core.bedita', 'ini');

/**
 * Load thumbnail generators.
 */
if (!Thumbnail::configured()) {
    Thumbnail::setConfig(Configure::read('Thumbnails.generators') ?: []);
}

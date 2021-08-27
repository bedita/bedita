<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

/*
 * Configure paths required to find CakePHP + general filepath constants
 */
require __DIR__ . '/paths.php';

/*
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

use BEdita\API\Error\ErrorHandler;
use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Cache\Cache;
use Cake\Console\ConsoleErrorHandler;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\JsonConfig;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Datasource\ConnectionManager;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Mailer\TransportFactory;
use Cake\Utility\Security;

/**
 * In `config/environment.php` you may set some environment variables
 * used in configuration
 */
if (file_exists(CONFIG . 'environment.php')) {
    include CONFIG . 'environment.php';
}

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

try {
    Configure::config('json', new JsonConfig());
    Configure::load('app', 'json');
} catch (\Exception $e) {
    // Do not halt if `app.json` is missing.
}

/*
 * Load an environment local configuration file.
 * You can use a file like app_local.php to provide local overrides to your
 * shared configuration.
 */
//Configure::load('app_local', 'default');

// Load default values for object types cache, if missing.
if (!Configure::check('Cache._bedita_object_types_')) {
    Configure::write('Cache._bedita_object_types_', [
        'className' => 'File',
        'prefix' => 'bedita_object_types_',
        'path' => CACHE . 'object_types/',
        'serialize' => true,
        'duration' => '+1 year',
    ]);
}
if (!Configure::check('Cache._bedita_core_')) {
    Configure::write('Cache._bedita_core_', [
        'className' => 'File',
        'prefix' => 'bedita_core_',
        'path' => CACHE . 'object_types/',
        'serialize' => true,
        'duration' => '+1 year',
    ]);
}

/* When debug = true the metadata cache should last
 * for a very very short time, as we want
 * to refresh the cache while developers are making changes.
 */
if (Configure::read('debug')) {
    Configure::write('Cache._bedita_core_.duration', '+2 minutes');
    Configure::write('Cache._bedita_object_types_.duration', '+2 minutes');
    Configure::write('Cache._cake_model_.duration', '+2 minutes');
    Configure::write('Cache._cake_core_.duration', '+2 minutes');
}

/*
 * Set server timezone using 'BEDITA_DEFAULT_TIMEZONE' with 'UTC' as default.
 * 'UTC' makes time calculations / conversions easier, it is the recommended choice.
 * Check http://php.net/manual/en/timezones.php for list of valid timezone strings.
 */
date_default_timezone_set(env('BEDITA_DEFAULT_TIMEZONE', 'UTC'));

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/*
 * Register application error and exception handlers.
 */
$isCli = PHP_SAPI === 'cli';
if ($isCli) {
    (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
    (new ErrorHandler(Configure::read('Error')))->register();
}

/*
 * Include the CLI bootstrap overrides.
 */
if ($isCli) {
    require __DIR__ . '/bootstrap_cli.php';
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 *
 * If you define fullBaseUrl in your config file you can remove this.
 */
if (!Configure::read('App.fullBaseUrl')) {
    $s = null;
    if (env('HTTPS')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        Configure::write('App.fullBaseUrl', 'http' . $s . '://' . $httpHost);
    }
    unset($httpHost, $s);
}

Cache::setConfig(Configure::consume('Cache') ?: []);
ConnectionManager::setConfig(Configure::consume('Datasources') ?: []);
TransportFactory::setConfig(Configure::consume('EmailTransport') ?: []);
Email::setConfig(Configure::consume('Email') ?: []);
Log::setConfig(Configure::consume('Log') ?: []);
Security::setSalt((string)Configure::consume('Security.salt'));
FilesystemRegistry::setConfig(Configure::consume('Filesystem') ?: []);

/*
 * Setup detectors for mobile and tablet.
 */
ServerRequest::addDetector('mobile', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isTablet();
});

/*
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 */
//Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
//Inflector::rules('irregular', ['red' => 'redlings']);
//Inflector::rules('uninflected', ['dontinflectme']);
//Inflector::rules('transliteration', ['/å/' => 'aa']);

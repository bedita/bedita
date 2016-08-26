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

use BEdita\API\Controller\Component\JsonApiComponent;
use BEdita\API\Middleware\CorsMiddleware;
use Cake\Console\ConsoleErrorHandler;
use Cake\Core\Configure;
use Cake\Error\ErrorHandler;
use Cake\Event\EventManager;
use Cake\Network\Request;

/**
 * Load 'api' configuration parameters
 */
if (!defined('UNIT_TEST_RUN') && (PHP_SAPI !== 'cli')) {
    Configure::load('api', 'database');
}

/** Set API exception renderer. This also requires error handler to be reset. */
Configure::write('Error.exceptionRenderer', 'BEdita\API\Error\ExceptionRenderer');
restore_error_handler();
restore_exception_handler();
if (PHP_SAPI === 'cli') {
    (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
    (new ErrorHandler(Configure::read('Error')))->register();
}

/** Add custom request detectors. */
Request::addDetector('html', ['accept' => ['text/html', 'application/xhtml+xml', 'application/xhtml', 'text/xhtml']]);
Request::addDetector('jsonapi', function (Request $request) {
    return $request->accepts(JsonApiComponent::CONTENT_TYPE);
});

/**
 * Customize middlewares for API needs
 *
 * Setup CORS from configuration
 * An optional 'CORS' key in should be like this example:
 *
 * 'CORS' => [
 *   'allowOrigin' => '*.example.com',
 *   'allowMethods' => ['GET', 'POST'],
 *   'allowHeaders' => ['X-CSRF-Token']
 * ]
 *
 * @see \BEdita\API\Middleware\CorsMiddleware
 */
EventManager::instance()->on('Server.buildMiddleware', function ($event, $middleware) {
    $middleware->insertAfter(
        'Cake\Error\Middleware\ErrorHandlerMiddleware',
        new CorsMiddleware(Configure::read('CORS'))
    );
});

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
use BEdita\API\Event\CommonEventHandler;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Network\Request;

/**
 * Load 'api' configuration parameters
 */
if (!defined('UNIT_TEST_RUN') && (PHP_SAPI !== 'cli')) {
    Configure::load('api', 'database');
}

/**
 * When debug is active and ExceptionRenderer configured is different
 * from BEdita\API\Error\ExceptionRenderer then write an info log.
 * If you want to use a different renderer comment it to avoid writing log
 */
$exceptionRenderer = Configure::read('Error.exceptionRenderer');
if ($exceptionRenderer !== 'BEdita\API\Error\ExceptionRenderer' && Configure::read('debug')) {
    Log::info('ExceptionRenderer used is ' . $exceptionRenderer . '.  BEdita/API should use BEdita\API\Error\ExceptionRenderer.');
}

/** Add custom request detectors. */
ServerRequest::addDetector('jsonapi', function (Request $request) {
    return $request->accepts(JsonApiComponent::CONTENT_TYPE);
});

EventManager::instance()->on(new CommonEventHandler());

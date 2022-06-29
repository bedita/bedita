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
use BEdita\API\Error\ExceptionRenderer;
use BEdita\API\Event\CommonEventHandler;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Http\ServerRequest;
use Cake\Log\Log;

/**
 * Load 'api' configuration parameters
 */
if (!defined('UNIT_TEST_RUN') && !in_array(PHP_SAPI, ['cli', 'phpdbg'])) {
    Configure::load('api', 'database');
}

$exceptionRenderer = Configure::read('Error.exceptionRenderer');
if ($exceptionRenderer !== ExceptionRenderer::class) {
    if (Configure::read('debug')) {
        Log::info(sprintf('ExceptionRenderer used is %s. BEdita/API should use %s.', $exceptionRenderer, ExceptionRenderer::class));
    }
    Configure::write('Error.exceptionRenderer', ExceptionRenderer::class);
}

/** Add custom request detectors. */
ServerRequest::addDetector('jsonapi', function ($request) {
    return $request->accepts(JsonApiComponent::CONTENT_TYPE);
});

EventManager::instance()->on(new CommonEventHandler());

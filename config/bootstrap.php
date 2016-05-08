<?php
/**
 * BEdita - a semantic content management framework
 * Copyright (C) 2008-2016  Chia Lab s.r.l., Channelweb s.r.l.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use BEdita\API\Controller\Component\JsonApiComponent;
use Cake\Console\ConsoleErrorHandler;
use Cake\Core\Configure;
use Cake\Error\ErrorHandler;
use Cake\Network\Request;

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
Request::addDetector('jsonApi', function (Request $request) {
    return $request->accepts(JsonApiComponent::CONTENT_TYPE);
});

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

namespace BEdita\API\Error;

use Cake\Core\Configure;
use Cake\Error\Debugger;
use Cake\Error\ExceptionRenderer as CakeExceptionRenderer;

/**
 * Exception renderer.
 *
 * @since 4.0.0
 */
class ExceptionRenderer extends CakeExceptionRenderer
{
    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $isDebug = Configure::read('debug');

        $code = $this->_code($this->error);
        $message = $this->_message($this->error, $code);
        $trace = null;
        if ($isDebug) {
            $trace = Debugger::formatTrace($this->_unwrap($this->error)->getTrace(), [
                'format' => 'array',
                'args' => false,
            ]);
        }

        if ($this->controller->request->is(['json', 'jsonApi'])) {
            $this->controller->loadComponent('RequestHandler');
            $this->controller->RequestHandler->config('viewClassMap.json', 'BEdita/API.JsonApi');
            $this->controller->loadComponent('BEdita/API.JsonApi', [
                'contentType' => $this->controller->request->is('json') ? 'json' : null,
                'checkMediaType' => $this->controller->request->is('jsonApi'),
            ]);

            $this->controller->JsonApi->error($code, $message, '', array_filter(compact('trace')));
        }

        return parent::render();
    }
}

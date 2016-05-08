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

            $this->controller->JsonApi->error($code, $message, '', compact('trace'));
        }

        return parent::render();
    }
}

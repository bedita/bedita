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
use Cake\Core\Plugin;
use Cake\Error\Debugger;
use Cake\Error\ExceptionRenderer as CakeExceptionRenderer;
use Cake\Network\Request;

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
    public function __construct(\Exception $exception)
    {
        parent::__construct($exception);
        Request::addDetector('html', ['accept' => ['text/html', 'application/xhtml+xml', 'application/xhtml', 'text/xhtml']]);
    }

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

        if ($this->isHtmlToSend()) {
            $this->setupView();
            $this->controller->set('method', $this->controller->request->method());
            $this->controller->set('responseBody', $this->jsonError($code, $message, $trace));

            return parent::render();
        }

        $this->controller->loadComponent('RequestHandler');
        $this->controller->RequestHandler->config('viewClassMap.json', 'BEdita/API.JsonApi');
        $this->controller->loadComponent('BEdita/API.JsonApi', [
            'contentType' => $this->controller->request->is('json') ? 'json' : null,
            'checkMediaType' => $this->controller->request->is('jsonapi'),
        ]);

        $this->controller->JsonApi->error($code, $message, '', array_filter(compact('trace')));
        $this->controller->RequestHandler->renderAs($this->controller, 'jsonapi');

        return parent::render();
    }

    /**
     * If the response should be a HTML content type.
     *
     * HTML content type is sent if HTML is requested
     * and debug is active or it is configured to accept html
     *
     * @return bool
     */
    public function isHtmlToSend()
    {
        if ($this->controller->request->is('html') && (Configure::read('debug') || Configure::read('Accept.html'))) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function _outputMessageSafe($template)
    {
        if ($this->isHtmlToSend()) {
            return parent::_outputMessageSafe('error');
        }

        $this->controller
            ->viewBuilder()
            ->className('BEdita\API\View\JsonApiView');

        $view = $this->controller->createView();
        $this->controller->response->body($view->render());

        return $this->controller->response;
    }

    /**
     * Setup the view params used in rendering.
     *
     * If BEdita/API plugin is loaded set the view builder to use it
     * else add the plugin template path to configured template paths
     * to assure to find it.
     *
     * @return void
     */
    protected function setupView()
    {
        if (Plugin::loaded('BEdita/API')) {
            $this->controller->viewBuilder()->plugin('BEdita/API');

            return;
        }

        $templatePaths = array_merge([dirname(__DIR__) . DS . 'Template' . DS], Configure::read('App.paths.templates'));
        Configure::write('App.paths.templates', $templatePaths);
    }

    /**
     * {@inheritDoc}
     */
    protected function _template(\Exception $exception, $method, $code)
    {
        return $this->template = 'error';
    }

    /**
     * Build json error string for HTML error display
     *
     * @param string $code Error code
     * @param string $message Error message
     * @param string $trace Error stacktrace
     * @return string JSON error
     */
    public function jsonError($code, $message, $trace)
    {
        $res = [
            'error' => [
                'status' => $code,
                'title' => $message,
                'meta' => array_filter(compact('trace'))
            ]
        ];

        return json_encode($res);
    }
}

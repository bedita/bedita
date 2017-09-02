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
use Cake\Core\Exception\Exception as CakeException;
use Cake\Error\ExceptionRenderer as CakeExceptionRenderer;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;

/**
 * Exception renderer.
 *
 * @since 4.0.0
 */
class ExceptionRenderer extends CakeExceptionRenderer
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct(\Exception $exception)
    {
        parent::__construct($exception);

        ServerRequest::addDetector('html', [
            'accept' => ['text/html', 'application/xhtml+xml', 'application/xhtml', 'text/xhtml'],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $isDebug = Configure::read('debug');

        $status = $this->_code($this->error);
        $title = $this->_message($this->error, $status);
        $detail = $this->errorDetail($this->error);
        $code = $this->appErrorCode($this->error);
        $trace = null;
        if ($isDebug) {
            $trace = explode("\n", $this->_unwrap($this->error)->getTraceAsString());
        }

        $this->controller->loadComponent('RequestHandler');
        $this->controller->RequestHandler->setConfig('viewClassMap.json', 'BEdita/API.JsonApi');
        $this->controller->loadComponent('BEdita/API.JsonApi', [
            'contentType' => $this->controller->request->is('json') ? 'json' : null,
            'checkMediaType' => $this->controller->request->is('jsonapi'),
        ]);

        $this->controller->JsonApi->error($status, $title, $detail, $code, array_filter(compact('trace')));
        $this->controller->RequestHandler->renderAs($this->controller, 'jsonapi');

        return parent::render();
    }

    /**
     * {@inheritDoc}
     */
    protected function _message(\Exception $error, $status)
    {
        $message = parent::_message($error, $status);
        if (empty($message) && $error instanceof CakeException) {
            $errorAttributes = $error->getAttributes();
            if (!empty($errorAttributes['title'])) {
                $message = $errorAttributes['title'];
            }
        }

        return $message;
    }

    /**
     * Human readable error detail from error attributes
     * In case of 'detail' array, format like this is expected
     *  [
     *    ['field1' => ['unique' => 'The provided value is invalid']],
     *    ['field2' => [...]],
     *  ],
     *
     *
     *
     * @param \Exception $error Exception.
     * @return string Error message
     */
    protected function errorDetail(\Exception $error)
    {
        if (!$error instanceof CakeException) {
            return '';
        }

        $errorAttributes = $error->getAttributes();
        if (empty($errorAttributes['detail'])) {
            return '';
        }
        $d = $errorAttributes['detail'];
        if (is_string($d)) {
            return $d;
        }

        $res = '';
        if (is_array($d)) {
            $d = Hash::flatten($d);
            foreach ($d as $item => $errDetail) {
                $res .= "[$item]: $errDetail ";
            }
        }

        return $res;
    }

    /**
     * Application specific error code.
     *
     * @param \Exception $error Exception.
     * @return string Error code
     */
    protected function appErrorCode(\Exception $error)
    {
        if (!$error instanceof CakeException) {
            return '';
        }

        $errorAttributes = $error->getAttributes();
        if (empty($errorAttributes['code']) || !is_scalar($errorAttributes['code'])) {
            return '';
        }

        return (string)$errorAttributes['code'];
    }

    /**
     * {@inheritDoc}
     */
    protected function _outputMessageSafe($template)
    {
        $this->controller
            ->viewBuilder()
            ->setClassName('BEdita\API\View\JsonApiView');

        $view = $this->controller->createView();

        return $this->controller->response->withStringBody($view->render());
    }

    /**
     * {@inheritDoc}
     */
    protected function _template(\Exception $exception, $method, $status)
    {
        return $this->template = 'error';
    }
}

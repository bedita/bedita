<?php
declare(strict_types=1);

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

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Exception\ImmutableResourceException;
use BEdita\Core\Exception\InvalidDataException;
use BEdita\Core\Exception\LockedResourceException;
use BEdita\Core\Exception\UserExistsException;
use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;
use Cake\Error\Renderer\WebExceptionRenderer;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Exception renderer.
 *
 * @since 4.0.0
 */
class ExceptionRenderer extends WebExceptionRenderer
{
    /**
     * Additional exception codes
     *
     * @var array
     */
    protected $additionalHttpCodes = [
        BadFilterException::class => 400,
        LockedResourceException::class => 403,
        ImmutableResourceException::class => 403,
        InvalidDataException::class => 400,
        UserExistsException::class => 400,
    ];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct(Throwable $exception)
    {
        parent::__construct($exception);

        ServerRequest::addDetector('html', [
            'accept' => ['text/html', 'application/xhtml+xml', 'application/xhtml', 'text/xhtml'],
        ]);

        $this->exceptionHttpCodes += $this->additionalHttpCodes;
    }

    /**
     * @inheritDoc
     */
    public function render(): ResponseInterface
    {
        $isDebug = Configure::read('debug');

        $status = $this->getHttpCode($this->error);
        $title = $this->_message($this->error, $status);
        $detail = $this->errorDetail($this->error);
        $code = $this->appErrorCode($this->error);
        $trace = null;
        if ($isDebug) {
            $trace = explode("\n", $this->error->getTraceAsString());
        }

        $this->controller->loadComponent('RequestHandler');
        $this->controller->RequestHandler->setConfig('viewClassMap.json', 'BEdita/API.JsonApi');
        $this->controller->loadComponent('BEdita/API.JsonApi', [
            'contentType' => 'json',
            // 'contentType' => $this->controller->request->is('json') ? 'json' : null,
            // 'checkMediaType' => $this->controller->request->is('jsonapi'),
        ]);

        $this->controller->JsonApi->error($status, $title, $detail, $code, array_filter(compact('trace')));
        $this->controller->RequestHandler->renderAs($this->controller, 'jsonapi');

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    protected function _message(Throwable $error, int $status): string
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
     * @param \Throwable $error Exception.
     * @return string Error message
     */
    protected function errorDetail(Throwable $error)
    {
        if (!$error instanceof CakeException) {
            return '';
        }

        $errorAttributes = $error->getAttributes();
        if (empty($errorAttributes['detail'])) {
            return $error->getPrevious() ? $error->getPrevious()->getMessage() : '';
        }
        $d = $errorAttributes['detail'];
        if (is_string($d)) {
            return $d;
        }

        $res = '';
        if (is_array($d)) {
            $d = Hash::flatten($d);
            $res = implode(
                ' ',
                array_map(
                    function ($key, $val) {
                        return sprintf('[%s]: %s', $key, $val);
                    },
                    array_keys($d),
                    array_values($d)
                )
            );
        }

        return $res;
    }

    /**
     * Application specific error code.
     *
     * @param \Throwable $error Exception.
     * @return string Error code
     */
    protected function appErrorCode(Throwable $error): string
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
     * @inheritDoc
     */
    protected function _outputMessageSafe(string $template): Response
    {
        $this->controller
            ->viewBuilder()
            ->setClassName('BEdita\API\View\JsonApiView');

        $view = $this->controller->createView();

        return $this->controller->getResponse()->withStringBody($view->render());
    }

    /**
     * @inheritDoc
     */
    protected function _template(Throwable $exception, string $method, int $code): string
    {
        return $this->template = 'error';
    }
}

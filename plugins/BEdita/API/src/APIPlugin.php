<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API;

use BEdita\API\Controller\Component\JsonApiComponent;
use BEdita\API\Error\ExceptionRenderer;
use BEdita\API\Event\CommonEventHandler;
use BEdita\API\Middleware\AnalyticsMiddleware;
use BEdita\API\Middleware\CorsMiddleware;
use BEdita\API\Middleware\TokenMiddleware;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Event\EventManager;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequest;
use Cake\Log\LogTrait;
use Psr\Log\LogLevel;

/**
 * Plugin class for BEdita/API.
 */
class APIPlugin extends BasePlugin
{
    use LogTrait;

    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        if (!defined('UNIT_TEST_RUN') && (PHP_SAPI !== 'cli')) {
            Configure::load('api', 'database');
        }

        $this->ensureExceptionRenderer();

        /** Add custom request detectors. */
        ServerRequest::addDetector('jsonapi', function ($request) {
            return $request->accepts(JsonApiComponent::CONTENT_TYPE);
        });

        EventManager::instance()->on(new CommonEventHandler());
    }

    /**
     * Ensure to use the BEdita/API ExceptionRenderer.
     *
     * @return void
     */
    protected function ensureExceptionRenderer(): void
    {
        $exceptionRenderer = Configure::read('Error.exceptionRenderer');
        if ($exceptionRenderer === ExceptionRenderer::class) {
            return;
        }

        if (Configure::read('debug')) {
            $this->log(sprintf(
                'ExceptionRenderer used is %s. BEdita/API should use %s.',
                $exceptionRenderer,
                ExceptionRenderer::class
            ), LogLevel::INFO);
        }

        Configure::write('Error.exceptionRenderer', ExceptionRenderer::class);
    }

    /**
     * @inheritDoc
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue
            ->prepend(new AnalyticsMiddleware())

            // Setup CORS from configuration
            // An optional 'CORS' key in should be like this example:
            //
            // ```
            // 'CORS' => [
            //   'allowOrigin' => '*.example.com',
            //   'allowMethods' => ['GET', 'POST'],
            //   'allowHeaders' => ['X-CSRF-Token']
            // ]
            // ```
            ->insertBefore(
                ErrorHandlerMiddleware::class,
                new CorsMiddleware(Configure::read('CORS'))
            );

            // ->insertAfter(
            //     ErrorHandlerMiddleware::class,
            //     new TokenMiddleware()
            // );
    }
}

<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Event;

use BEdita\API\Middleware\CorsMiddleware;
use BEdita\API\Middleware\HtmlMiddleware;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Http\MiddlewareQueue;

/**
 * CommonEventsHandler class.
 *
 * This class contains common event listener attached bootstrapping the API plugin
 *
 * @since 4.0.0
 */
class CommonEventHandler implements EventListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Server.buildMiddleware' => 'buildMiddlewareStack',
            'Auth.afterIdentify' => 'afterIdentify',
        ];
    }

    /**
     * Customize middlewares for API needs
     *
     * Setup CORS from configuration
     * An optional 'CORS' key in should be like this example:
     *
     * ```
     * 'CORS' => [
     *   'allowOrigin' => '*.example.com',
     *   'allowMethods' => ['GET', 'POST'],
     *   'allowHeaders' => ['X-CSRF-Token']
     * ]
     * ```
     *
     * @param \Cake\Event\Event $event The event object
     * @param \Cake\Http\MiddlewareQueue $middleware The middleware queue
     * @return void
     * @see \BEdita\API\Middleware\CorsMiddleware to more info on CORS configuration
     */
    public function buildMiddlewareStack(Event $event, MiddlewareQueue $middleware)
    {
        if (Configure::read('debug') || Configure::read('Accept.html')) {
            $middleware->insertAt(0, new HtmlMiddleware());
        }

        $middleware->insertAfter(
            ErrorHandlerMiddleware::class,
            new CorsMiddleware(Configure::read('CORS'))
        );
    }

    /**
     * Set the user identified.
     * Called after user authentication.
     *
     * @param \Cake\Event\Event $event The event object
     * @param array $user The user data
     * @return void
     * @throws \Cake\Network\Exception\UnauthorizedException
     */
    public function afterIdentify(Event $event, array $user)
    {
        LoggedUser::setUser($user);
    }
}

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
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Http\MiddlewareQueue;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\Table;

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
     * A whitelist of plugins that skips `self::checkAuthorized()`
     *
     * @var array
     */
    protected $pluginWhitelist = ['DebugKit', 'Migrations'];

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Server.buildMiddleware' => 'buildMiddlewareStack',
            'Auth.afterIdentify' => 'afterIdentify',
            'Model.beforeSave' => 'checkAuthorized',
            'Model.beforeDelete' => 'checkAuthorized',
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
        $middleware->insertAfter(
            ErrorHandlerMiddleware::class,
            new CorsMiddleware(Configure::read('CORS'))
        );
    }

    /**
     * Check if user is logged.
     * Called before saving/deleting resources.
     *
     * @param \Cake\Event\Event $event The event object
     * @return void
     * @throws \Cake\Network\Exception\UnauthorizedException
     */
    public function checkAuthorized(Event $event)
    {
        $subject = $event->getSubject();
        if ($subject instanceof Table) {
            list($plugin) = pluginSplit($subject->getRegistryAlias());
            if (in_array($plugin, $this->pluginWhitelist)) {
                return;
            }
        }

        if (LoggedUser::id() === null) {
            throw new UnauthorizedException('User not authorized');
        }
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

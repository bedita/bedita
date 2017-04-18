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

use BEdita\Core\Utility\LoggedUser;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Exception\UnauthorizedException;

/**
 * WriteActionsEventHandler class.
 *
 * This class contains event listener attached on write API actions (POST, PATCH, DELETE methods)
 *
 * @since 4.0.0
 */
class WriteActionsEventHandler implements EventListenerInterface
{

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Model.beforeSave' => 'checkAuthorized',
            'Model.beforeDelete' => 'checkAuthorized',
        ];
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
        if (LoggedUser::id() === null) {
            throw new UnauthorizedException('User not authorized');
        }
    }
}

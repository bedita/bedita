<?php
declare(strict_types=1);

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

use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;

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
     * @inheritDoc
     */
    public function implementedEvents(): array
    {
        return [
            'Error.beforeRender' => 'errorBeforeRender',
        ];
    }

    /**
     * Avoid to render error to not break the API.
     * `Cake\Error\ErrorTrap` takes care of logging the error.
     * A better implementation (maybe with an ErrorService) could try to collect errors and render them as json.
     *
     * @param \Cake\Event\EventInterface $event The event.
     * @return void
     * @codeCoverageIgnore
     */
    public function errorBeforeRender(EventInterface $event): void
    {
        $event->stopPropagation();
    }
}

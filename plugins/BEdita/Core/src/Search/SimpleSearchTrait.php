<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Search;

use BEdita\Core\Search\Adapter\SimpleAdapter;
use Cake\Event\Event;
use Cake\Event\EventDispatcherTrait;
use Cake\ORM\Table;

/**
 * A trait to help to setup simple search adapter.
 *
 * @since 5.14.0
 */
trait SimpleSearchTrait
{
    use EventDispatcherTrait;

    /**
     * Setup a listener on `SearchAdapter.initialize` event
     * to override configuration of `\BEdita\Core\Search\Adapter\SimpleAdapter`.
     *
     * @param array $config Configuration
     * @param \Cake\ORM\Table|null $refTable The reference table
     * @return void
     */
    protected function setupSimpleSearch(array $config, ?Table $refTable = null): void
    {
        $refTable ??= $this;
        $this->getEventManager()->on(
            'SearchAdapter.initialize',
            function (Event $event, Table $table) use ($config, $refTable): void {
                if ($table !== $refTable || !$event->getSubject() instanceof SimpleAdapter) {
                    return;
                }

                $event->getSubject()->setConfig($config, null, false);
            }
        );
    }
}

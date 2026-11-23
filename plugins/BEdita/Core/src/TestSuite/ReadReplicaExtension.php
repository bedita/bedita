<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\TestSuite;

use BEdita\Core\Database\Connection;
use Cake\Datasource\ConnectionManager;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * PHPUnit extension that, when the `db_replica` environment variable is set, runs the suite with
 * a `test` connection that has a separate `read` session on the same database.
 *
 * Register it in `phpunit.xml.dist`:
 *
 * ```xml
 * <extensions>
 *     <bootstrap class="BEdita\Core\TestSuite\ReadReplicaExtension"/>
 * </extensions>
 * ```
 *
 * @codeCoverageIgnore
 */
class ReadReplicaExtension implements Extension
{
    /**
     * @inheritDoc
     */
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        if (!filter_var(getenv('db_replica'), FILTER_VALIDATE_BOOL)) {
            return;
        }

        $config = (array)ConnectionManager::getConfig('test');
        ConnectionManager::drop('test');
        ConnectionManager::setConfig('test', ['className' => Connection::class, 'read' => []] + $config);

        $facade->registerSubscriber(new class implements PreparedSubscriber {
            /**
             * @inheritDoc
             */
            public function notify(Prepared $event): void
            {
                // Fixture inserts in `setUp()` make the process sticky, and tests would never read on `read`.
                Connection::setSticky(false);
            }
        });
    }
}

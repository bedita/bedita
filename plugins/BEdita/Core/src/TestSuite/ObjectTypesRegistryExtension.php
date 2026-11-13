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

use BEdita\Core\Utility\ObjectTypesRegistry;
use PHPUnit\Event\Test\PreparationStarted;
use PHPUnit\Event\Test\PreparationStartedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * PHPUnit extension that resets {@see \BEdita\Core\Utility\ObjectTypesRegistry} before each test,
 * so that data loaded from fixtures of a test does not leak into the following ones.
 *
 * Register it in `phpunit.xml.dist`:
 *
 * ```xml
 * <extensions>
 *     <bootstrap class="BEdita\Core\TestSuite\ObjectTypesRegistryExtension"/>
 * </extensions>
 * ```
 *
 * @since 6.14.0
 * @codeCoverageIgnore
 */
class ObjectTypesRegistryExtension implements Extension
{
    /**
     * @inheritDoc
     */
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber(new class implements PreparationStartedSubscriber {
            /**
             * @inheritDoc
             */
            public function notify(PreparationStarted $event): void
            {
                ObjectTypesRegistry::reset();
            }
        });
    }
}

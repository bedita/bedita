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
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Model\Table\ObjectTypesTable;
use BEdita\Core\Utility\ObjectTypesRegistry;
use Cake\Cache\Cache;
use Cake\Cache\Event\CacheAfterGetEvent;
use Cake\ORM\TableRegistry;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Test object types cache usage during a request.
 */
#[CoversNothing]
class ObjectTypesCacheTest extends IntegrationTestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Cache::drop(ObjectTypesTable::CACHE_CONFIG);
        Cache::setConfig(ObjectTypesTable::CACHE_CONFIG, ['className' => 'Array', 'prefix' => '']);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Cache::drop(ObjectTypesTable::CACHE_CONFIG);
        Cache::setConfig(ObjectTypesTable::CACHE_CONFIG, ['className' => 'Null']);

        parent::tearDown();
    }

    /**
     * Test that a request reads object types data from cache only once, with a warm cache.
     *
     * @return void
     */
    public function testSingleReadPerRequest(): void
    {
        // Warm up cache.
        $this->configRequestHeaders();
        $this->get('/documents?include=test');
        $this->assertResponseCode(200);

        // Simulate a new process: nothing in memory, warm cache.
        ObjectTypesRegistry::reset();
        TableRegistry::getTableLocator()->clear();

        $reads = [];
        Cache::pool(ObjectTypesTable::CACHE_CONFIG)
            ->getEventManager()
            ->on(CacheAfterGetEvent::NAME, function (CacheAfterGetEvent $event) use (&$reads): void {
                $reads[] = $event->getKey();
            });

        $this->configRequestHeaders();
        $this->get('/documents?include=test');
        $this->assertResponseCode(200);

        $counts = array_count_values($reads);
        static::assertSame(1, $counts[ObjectTypesRegistry::CACHE_KEY] ?? 0, json_encode($counts));
        // Besides the registry, only JSON Schema revisions of types in response are read.
        $others = array_diff(array_keys($counts), [ObjectTypesRegistry::CACHE_KEY]);
        static::assertSame([], array_values(preg_grep('/^revision_schema_/', $others, PREG_GREP_INVERT)), json_encode($counts));
    }
}

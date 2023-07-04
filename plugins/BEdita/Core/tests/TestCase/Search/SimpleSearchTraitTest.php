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
namespace BEdita\Core\Test\TestCase\Search;

use BEdita\Core\Search\Adapter\SimpleAdapter;
use BEdita\Core\Search\BaseAdapter;
use BEdita\Core\Search\SimpleSearchTrait;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\Core\Search\SimpleSearchTrait
 */
class SimpleSearchTraitTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.FakeSearches',
    ];

    /**
     * Test `setupSimpleSearch()`
     *
     * @return void
     * @covers ::setupSimpleSearch()
     */
    public function testSetupSimpleSearch(): void
    {
        $table = $this->fetchTable('FakeSearches');
        $adapter = new SimpleAdapter();
        $subject = $this->getMockForTrait(SimpleSearchTrait::class);
        $conf = [
            'fields' => ['name'],
        ];

        $subject->setupSimpleSearch($conf, $table);

        $expected = ['*'];
        static::assertEquals($expected, $adapter->getConfig('fields'));
        static::assertCount(1, $subject->getEventManager()->listeners('SearchAdapter.initialize'));

        $event = new Event('SearchAdapter.initialize', $adapter, [$table]);
        $subject->getEventManager()->dispatch($event);

        static::assertEquals(Hash::get($conf, 'fields'), $adapter->getConfig('fields'));
    }

    /**
     * Test that for adapter different from `SimpleAdapter` conf is not changed.
     *
     * @return void
     * @covers ::setupSimpleSearch()
     */
    public function testSetupSimpleSearchWrongAdapter(): void
    {
        $table = $this->fetchTable('FakeSearches');
        $adapter = new class extends BaseAdapter {
            protected $_defaultConfig = [
                'customConf' => 'complicated configuration',
            ];

            public function indexResource(EntityInterface $entity, string $operation): void
            {
            }

            public function search(Query $query, string $text, array $options = []): Query
            {
                return $query;
            }
        };

        $subject = $this->getMockForTrait(SimpleSearchTrait::class);
        $conf = [
            'customConf' => 'very simple configuration',
        ];
        $subject->setupSimpleSearch($conf, $table);

        $expected = 'complicated configuration';
        static::assertEquals($expected, $adapter->getConfig('customConf'));
        static::assertCount(1, $subject->getEventManager()->listeners('SearchAdapter.initialize'));

        $event = new Event('SearchAdapter.initialize', $adapter, [$table]);
        $subject->getEventManager()->dispatch($event);

        static::assertEquals($expected, $adapter->getConfig('customConf'));
    }

    /**
     * Test that conf is not changed if table.
     *
     * @return void
     * @covers ::setupSimpleSearch()
     */
    public function testSetupSimpleSearchWrongTable(): void
    {
        $table = new class extends Table {
            use SimpleSearchTrait;

            public function initialize(array $config): void
            {
                parent::initialize($config);

                $this->setupSimpleSearch(['adapterConf' => 'table conf']);
            }
        };

        $expected = 'default conf';
        $adapter = new SimpleAdapter();
        $adapter->initialize(['adapterConf' => 'default conf']);
        static::assertEquals($expected, $adapter->getConfig('adapterConf'));
        static::assertCount(1, $table->getEventManager()->listeners('SearchAdapter.initialize'));

        $event = new Event('SearchAdapter.initialize', $adapter, [$this->fetchTable('FakeSearches')]);
        $table->getEventManager()->dispatch($event);

        static::assertEquals($expected, $adapter->getConfig('adapterConf'));
    }
}

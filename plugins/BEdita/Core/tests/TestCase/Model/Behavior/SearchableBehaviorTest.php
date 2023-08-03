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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\ORM\Inheritance\Table;
use BEdita\Core\Search\BaseAdapter;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\Query;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\SearchableBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\SearchableBehavior
 */
class SearchableBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
        'plugin.BEdita/Core.FakeMammals',
        'plugin.BEdita/Core.FakeFelines',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->fetchTable('FakeMammals', ['className' => Table::class])
            ->setDisplayField('name')
            ->extensionOf('FakeAnimals');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->getTableLocator()->remove('FakeMammals');
        $this->getTableLocator()->remove('FakeAnimals');
    }

    /**
     * Data provider for `testFindQuery` test case.
     *
     * @return array
     */
    public function findQueryProvider()
    {
        return [
            'ok' => [
                [
                    2 => 'koala',
                ],
                'ala',
            ],
            'ok with "string" key' => [
                [
                    2 => 'koala',
                ],
                ['string' => 'ala'],
            ],
            'query with string param not a string' => [
                new BadFilterException([
                    'title' => 'Invalid data',
                    'detail' => 'query filter requires a non-empty query string',
                ]),
                [
                    'string' => 1,
                ],
            ],
        ];
    }

    /**
     * Test finder for query string.
     *
     * @param array|\Exception $expected Expected result.
     * @param string|array $query Query string.
     * @return void
     * @dataProvider findQueryProvider()
     * @covers ::findQuery()
     * @covers ::getAdapter()
     * @covers ::getSearchRegistry()
     * @covers ::fitSimpleAdapterConf()
     */
    public function testFindQuery($expected, $query)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $table = $this->fetchTable('FakeMammals');
        $table->addBehavior('BEdita/Core.Searchable');

        static::assertTrue($table->hasFinder('query'));

        $result = $table
            ->find('query', (array)$query)
            ->find('list')
            ->toArray();

        static::assertEquals($expected, $result);
    }

    /**
     * Test that deprecated conf works yet.
     *
     * @return void
     * @covers ::findQuery()
     * @covers ::getAdapter()
     * @covers ::getSearchRegistry()
     * @covers ::fitSimpleAdapterConf()
     */
    public function testFitSimpleSearchWithDeprecatedConf(): void
    {
        $options = ['string' => 'koal'];

        $table = $this->fetchTable('FakeMammals');
        $table->addBehavior('BEdita/Core.Searchable'); // search on all fields
        $result = $table
            ->find('query', $options)
            ->toArray();

        static::assertCount(1, $result);
        static::assertEquals('koala', $result[0]->name);

        $table->removeBehavior('Searchable');
        $table->addBehavior('BEdita/Core.Searchable', [ // search on `subclass`
            'fields' => [
                'subclass' => 1,
            ],
        ]);

        $result = $table
            ->find('query', $options)
            ->toArray();

        static::assertCount(0, $result);
    }

    /**
     * Test afterSave() and afterDelete()
     *
     * @return void
     * @covers ::afterSave()
     * @covers ::afterDelete()
     * @covers ::indexEntity()
     * @covers ::getOperation()
     * @covers ::getSearchAdapters()
     * @covers ::getAdapter()
     */
    public function testAfterSaveDelete(): void
    {
        $adapter = new class extends BaseAdapter {
            public $initializedCount = 0;
            public $afterDeleteCount = 0;
            public $afterSaveCount = 0;

            public function search(Query $query, string $text, array $options = []): Query
            {
                return $query;
            }

            public function indexResource(EntityInterface $entity, string $operation): void
            {
                if ($operation === 'edit') {
                    $this->afterSaveCount++;
                }

                if ($operation === 'delete') {
                    $this->afterDeleteCount++;
                }
            }
        };

        EventManager::instance()->on('SearchAdapter.initialize', function (Event $event) {
            $event->getSubject()->initializedCount++;
        });

        $backupConf = Configure::read('Search');

        Configure::write('Search.adapters.default', [
            'className' => $adapter,
        ]);

        $table = $this->fetchTable('FakeMammals');
        $table->addBehavior('BEdita/Core.Searchable');
        $entity = $table->get(2);

        static::assertEquals(0, $adapter->afterSaveCount);
        $entity->setDirty('name');
        $table->saveOrFail($entity);
        static::assertEquals(1, $adapter->afterSaveCount);
        static::assertEquals(1, $adapter->initializedCount);

        static::assertEquals(0, $adapter->afterDeleteCount);
        $table->deleteOrFail($entity);
        static::assertEquals(1, $adapter->afterDeleteCount);
        static::assertEquals(1, $adapter->initializedCount);

        Configure::write('Search', $backupConf);
        EventManager::instance()->off('SearchAdapter.initialize');
    }
}

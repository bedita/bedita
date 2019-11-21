<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Exception\BadFilterException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Model\Table\ObjectHistoryTable Test Case
 */
class ObjectHistoryTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ObjectHistoryTable
     */
    public $ObjectHistory;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectHistory',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->ObjectHistory = TableRegistry::getTableLocator()->get('ObjectHistory');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ObjectHistory);

        parent::tearDown();
    }

    /**
     * Test `findHistory` method
     *
     * @covers ::findHistory()
     * @return void
     */
    public function testFindHistory()
    {
        $result = $this->ObjectHistory->find('history', [2])
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        $expected = [1 => 1, 2 => 2];
        static::assertEquals($expected, $result);
    }

    /**
     * Test `findActivity` method
     *
     * @covers ::findActivity()
     * @return void
     */
    public function testFindActivity()
    {
        $result = $this->ObjectHistory->find('activity', [5])
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        $expected = [2 => 2];
        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testFindFail`
     *
     * @return array
     */
    public function findFailProvider(): array
    {
        return [
            'missing' => [
                'history',
                [],
            ],
            'bad opts' => [
                'activity',
                [new \stdClass()],
            ],
        ];
    }

    /**
     * Test finder methods failures
     *
     * @dataProvider findFailProvider
     * @covers ::findActivity()
     * @covers ::findHistory()
     * @return void
     */
    public function testFindFail($finder, $options)
    {
        $this->expectException(BadFilterException::class);
        $this->expectExceptionMessage('Missing or malformed required parameter "id"');
        $this->ObjectHistory->find($finder, $options)->first();
    }
}

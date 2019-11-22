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
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\Core\Model\Table\HistoryTable
 */
class HistoryTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\HistoryTable
     */
    public $History;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.History',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->History = TableRegistry::getTableLocator()->get('History');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->History);

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
        $result = $this->History->find('history', [2])
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        $expected = [1 => 1, 2 => 2];
        static::assertEquals($expected, $result);
    }

    /**
     * Test `findHistory` method for resources
     *
     * @covers ::findHistory()
     * @return void
     */
    public function testFindResourceHistory()
    {
        $result = $this->History->find('history', [2, 'roles'])
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        static::assertEquals([], $result);
    }

    /**
     * Test `findActivity` method
     *
     * @covers ::findActivity()
     * @return void
     */
    public function testFindActivity()
    {
        $result = $this->History->find('activity', [5])
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
        $this->History->find($finder, $options)->first();
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'ok' => [
                [],
                [
                    'user_id' => 1,
                    'resource_id' => '2',
                ],
            ],
            'invalid 1' => [
                [
                    'resource_id._required',
                ],
                [
                    'application_id' => 2,
                    'user_id' => 1,
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param string[] $expected Expected errors.
     * @param array $data Data.
     * @return void
     *
     * @dataProvider validationProvider
     * @covers ::validationDefault()
     */
    public function testValidation(array $expected, array $data)
    {
        $entity = $this->History->newEntity();
        $entity = $this->History->patchEntity($entity, $data);
        $errors = array_keys(Hash::flatten($entity->getErrors()));

        static::assertEquals($expected, $errors);
    }
}

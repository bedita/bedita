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

namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\Relations;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Utility\Relations} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\Relations
 */
class RelationsTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
    ];

    /**
     * Test relations
     *
     * @return array
     */
    protected $relations = [
        [
            'name' => 'poster',
            'label' => 'Poster',
            'inverse_name' => 'poster_of',
            'inverse_label' => 'Poster of',
            'description' => 'Document or event has a poster file',
            'left' => ['documents', 'events'],
            'right' => ['files'],
        ],
    ];

    /**
     * Test `create` method.
     *
     * @covers ::create()
     * @covers ::validate()
     * @covers ::addTypes()
     */
    public function testCreate()
    {
        Relations::create($this->relations);

        $allRelations = TableRegistry::getTableLocator()->get('Relations')->find()->toArray();
        static::assertEquals(5, count($allRelations));
        static::assertEquals('poster', $allRelations[4]['name']);
    }

    /**
     * Test `remove` method.
     *
     * @covers ::remove()
     * @covers ::removeTypes()
     */
    public function testRemove()
    {
        Relations::create($this->relations);

        Relations::remove($this->relations);
        $allRelations = TableRegistry::getTableLocator()->get('Relations')->find()->toArray();
        static::assertEquals(4, count($allRelations));
    }

    /**
     * Test `addRelationType` method.
     *
     * @covers ::addRelationType()
     */
    public function testAddRelationType()
    {
        Relations::create($this->relations);

        Relations::addRelationType('poster', 'profiles', 'left');
        $leftTypes = TableRegistry::getTableLocator()
            ->get('RelationTypes')
            ->find()
            ->where(['relation_id' => 5, 'side' => 'left'])
            ->toArray();
        static::assertEquals(3, count($leftTypes));
    }

    /**
     * Test `removeRelationType` method.
     *
     * @covers ::removeRelationType()
     */
    public function testRemoveRelationType()
    {
        Relations::create($this->relations);

        Relations::removeRelationType('poster', 'documents', 'left');
        $leftTypes = TableRegistry::getTableLocator()
            ->get('RelationTypes')
            ->find()
            ->where(['relation_id' => 4, 'side' => 'left'])
            ->toArray();
        static::assertEquals(1, count($leftTypes));
    }

    /**
     * Test `validate` failure.
     *
     * @covers ::validate()
     */
    public function testValidate()
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Missing left/right relation types');

        unset($this->relations[0]['left']);
        Relations::create($this->relations);
    }

    /**
     * Test `update` method.
     *
     * @covers ::update()
     * @covers ::updateTypes()
     */
    public function testUpdate()
    {
        $update = [
            [
                'name' => 'test_abstract',
                'description' => 'a new description',
                'left' => ['events', 'documents'],
            ],
        ];

        $res = Relations::update($update);
        static::assertEquals('a new description', Hash::get($res, '0.description'));

        $leftTypes = TableRegistry::getTableLocator()
            ->get('RelationTypes')
            ->find()
            ->where(['relation_id' => 3, 'side' => 'left'])
            ->toArray();
        static::assertEquals(2, count($leftTypes));
    }
}

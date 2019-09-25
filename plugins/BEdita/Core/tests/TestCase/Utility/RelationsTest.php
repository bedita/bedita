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
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

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
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
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
        ]
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
        static::assertEquals(4, count($allRelations));
        static::assertEquals('poster', $allRelations[3]['name']);
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
        static::assertEquals(3, count($allRelations));
    }

    /**
     * Test `validate` failure.
     *
     * @covers ::validate()
     */
    public function testValidate()
    {
        static::expectException(BadRequestException::class);
        static::expectExceptionMessage('Missing left/right relation types');

        unset($this->relations[0]['left']);
        Relations::create($this->relations);
    }
}

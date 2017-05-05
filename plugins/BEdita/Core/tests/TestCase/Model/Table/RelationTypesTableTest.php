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

namespace BEdita\Core\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Model\Table\RelationTypesTable Test Case
 */
class RelationTypesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\RelationTypesTable
     */
    public $RelationTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.relations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->RelationTypes = TableRegistry::get('RelationTypes');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RelationTypes);

        parent::tearDown();
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'validLeft' => [
                true,
                [
                    'object_type_id' => 3,
                    'relation_id' => 1,
                    'side' => 'left',
                ],
            ],
            'validRight' => [
                true,
                [
                    'object_type_id' => 3,
                    'relation_id' => 1,
                    'side' => 'right',
                ],
            ],
            'invalidSide' => [
                false,
                [
                    'object_type_id' => 3,
                    'relation_id' => 1,
                    'side' => 'Dark side of the Moon',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     * @return void
     *
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $objectType = $this->RelationTypes->newEntity();
        $this->RelationTypes->patchEntity($objectType, $data);

        $success = $this->RelationTypes->save($objectType);
        static::assertEquals($expected, (bool)$success);
    }
}

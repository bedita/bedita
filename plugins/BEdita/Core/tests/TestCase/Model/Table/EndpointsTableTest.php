<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\EndpointsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Model\Table\EndpointsTable Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\EndpointsTable
 */
class EndpointsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\EndpointsTable
     */
    public $Endpoints;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.endpoints',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Endpoints = TableRegistry::get('Endpoints');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Endpoints);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->Endpoints->initialize([]);
        $this->assertEquals('endpoints', $this->Endpoints->table());
        $this->assertEquals('id', $this->Endpoints->primaryKey());
        $this->assertEquals('name', $this->Endpoints->displayField());

        $this->assertInstanceOf('\Cake\ORM\Behavior\TimestampBehavior', $this->Endpoints->behaviors()->get('Timestamp'));
        $this->assertInstanceOf('\Cake\ORM\Association\hasMany', $this->Endpoints->EndpointPermissions);
        $this->assertInstanceOf('\BEdita\Core\Model\Table\EndpointPermissionsTable', $this->Endpoints->EndpointPermissions->target());
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'valid' => [
                true,
                [
                    'name' => 'custom_endpoint',
                ],
            ],
            'notUniqueName' => [
                false,
                [
                    'name' => 'home',
                ],
            ],
            'missingName' => [
                false,
                [
                    'description' => 'Where is apendpoint name?',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     *
     * @return void
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $endpoint = $this->Endpoints->newEntity($data);
        $error = (bool)$endpoint->errors();
        $this->assertEquals($expected, !$error);
        if ($expected) {
            $success = $this->Endpoints->save($endpoint);
            $this->assertTrue((bool)$success);
        }
    }

    /**
     * Data provider for `testBuildRules` test case.
     *
     * @return array
     */
    public function buildRulesProvider()
    {
        return [
            'wrongObjectType' => [
                false,
                [
                    'name' => 'custom_endpoint',
                    'object_type_id' => 1234
                ]
            ],
            'notUnique' => [
                false,
                [
                    'name' => 'home'
                ]
            ]
        ];
    }

    /**
     * Test build rules validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     *
     * @return void
     * @dataProvider buildRulesProvider
     * @coversNothing
     */
    public function testBuildRules($expected, array $data)
    {
        $endpoint = $this->Endpoints->newEntity($data, ['validate' => false]);
        $success = $this->Endpoints->save($endpoint);
        $this->assertEquals($expected, (bool)$success, print_r($endpoint->errors(), true));
    }
}

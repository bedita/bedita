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

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\PropertyTypes} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\PropertyTypes
 */
class PropertyTypesTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\PropertyTypesTable
     */
    public $PropertyTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.property_types',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->PropertyTypes = TableRegistry::get('PropertyTypes');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->PropertyTypes);

        parent::tearDown();
    }

    /**
     * Test initialization.
     *
     * @return void
     * @coversNothing
     */
    public function testInitialization()
    {
        $this->PropertyTypes->initialize([]);
        $this->assertEquals('property_types', $this->PropertyTypes->getTable());
        $this->assertEquals('id', $this->PropertyTypes->getPrimaryKey());
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
                    'name' => 'propName',
                    'params' => 'some params',
                ],
            ],
            'notValid' => [
                false,
                [
                    'name' => '',
                    'params' => '',
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
        $PropertyTypes = $this->PropertyTypes->newEntity($data);

        $error = (bool)$PropertyTypes->getErrors();
        $this->assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->PropertyTypes->save($PropertyTypes);
            $this->assertTrue((bool)$success);
        }
    }
}

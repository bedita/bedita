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

namespace BEdita\Core\Test\TestCase\Model\Schema;

use BEdita\Core\Model\Schema\JsonSchema;
use Cake\Network\Exception\NotFoundException;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Schema\JsonSchema} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Schema\JsonSchema
 */
class JsonSchemaTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.locations',
        'plugin.BEdita/Core.media',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.streams',
    ];

    /**
     * Data provider for `testGenerate` test case.
     *
     * @return array
     */
    public function generateProvider()
    {
        return [
            'objects' => [
                [
                    'properties' => 15,
                    'required' => 0,
                ],
                'objects',
            ],
            'notfound' => [
                new NotFoundException('Type "gustavo" not found'),
                'gustavo',
            ],
            'users' => [
                [
                    'properties' => 42,
                    'required' => 0,
                ],
                'users',
            ],
            'roles' => [
                [
                    'properties' => 5,
                    'required' => 1,
                ],
                'roles',
            ],
            'documents' => [
                [
                    'properties' => 17,
                    'required' => 0,
                ],
                'documents',
            ],
            'streams' => [
                [
                    'properties' => 11,
                    'required' => 2,
                ],
                'streams',
            ],
        ];
    }

    /**
     * Test `generate` method.
     *
     * @param array|\Exception $expected Expected result.
     * @param string $name Type name.
     * @return void
     *
     * @dataProvider generateProvider()
     * @covers ::generate()
     * @covers ::resourceSchema()
     * @covers ::objectSchema()
     * @covers ::convertColumn()
     * @covers ::convertProperty()
     */
    public function testGenerate($expected, $name)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $url = 'http://api.example.com/model/schema/' . $name;
        $result = JsonSchema::generate($name, $url);
        static::assertNotEmpty($result);

        $keys = ['definitions', '$id', '$schema', 'type', 'properties', 'required'];
        static::assertArraySubset($keys, array_keys($result));
        static::assertEquals($expected['properties'], count($result['properties']));
        static::assertEquals($expected['required'], count($result['required']));
    }
}

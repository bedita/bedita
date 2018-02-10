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

namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Model\Table\ObjectTypesTable;
use BEdita\Core\Utility\JsonSchema;
use Cake\Cache\Cache;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Utility\JsonSchema} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\JsonSchema
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
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        Cache::clear(false, ObjectTypesTable::CACHE_CONFIG);
    }

    /**
     * Data provider for `testGenerate` test case.
     *
     * @return array
     */
    public function generateProvider()
    {
        return [
            'objects' => [
                false,
                'objects',
            ],
            'notfound' => [
                new NotFoundException('Type "gustavo" not found'),
                'gustavo',
            ],
            'users' => [
                [
                    'properties' => [
                        'another_email',
                        'another_username',
                        'birthdate',
                        'blocked',
                        'body',
                        'city',
                        'company',
                        'company_kind',
                        'company_name',
                        'country',
                        'created',
                        'created_by',
                        'deathdate',
                        'description',
                        'email',
                        'extra',
                        'gender',
                        'lang',
                        'last_login',
                        'last_login_err',
                        'locked',
                        'modified',
                        'modified_by',
                        'name',
                        'national_id_number',
                        'num_login_err',
                        'person_title',
                        'phone',
                        'publish_end',
                        'publish_start',
                        'published',
                        'state_name',
                        'status',
                        'street_address',
                        'surname',
                        'title',
                        'uname',
                        'username',
                        'vat_number',
                        'verified',
                        'website',
                        'zipcode',
                    ],
                    'required' => [
                        'username',
                    ],
                    'revision' => '',
                    'readOnly' => true,
                ],
                'users',
            ],
            'roles' => [
                [
                    'properties' => [
                        'created',
                        'description',
                        'modified',
                        'name',
                        'unchangeable',
                    ],
                    'required' => [
                        'name',
                    ],
                    'revision' => '',
                ],
                'roles',
            ],
            'documents' => [
                [
                    'properties' => [
                        'another_description',
                        'another_title',
                        'body',
                        'created',
                        'created_by',
                        'description',
                        'extra',
                        'lang',
                        'locked',
                        'modified',
                        'modified_by',
                        'publish_end',
                        'publish_start',
                        'published',
                        'status',
                        'title',
                        'uname',
                    ],
                    'required' => [],
                    'revision' => '',
                    'readOnly' => false,
                ],
                'documents',
            ],
            'streams' => [
                [
                    'properties' => [
                        'created',
                        'duration',
                        'file_name',
                        'file_size',
                        'hash_md5',
                        'hash_sha1',
                        'height',
                        'mime_type',
                        'modified',
                        'version',
                        'width',
                    ],
                    'required' => [
                        'file_name',
                        'mime_type',
                    ],
                    'revision' => '',
                ],
                'streams',
            ],
        ];
    }

    /**
     * Test `generate` method.
     *
     * @param array|bool|\Exception $expected Expected result.
     * @param string $name Type name.
     * @return void
     *
     * @dataProvider generateProvider()
     * @covers ::generate()
     * @covers ::typeSchema()
     * @covers ::resourceSchema()
     * @covers ::objectSchema()
     */
    public function testGenerate($expected, $name)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $url = 'http://api.example.com/model/schema/' . $name;
        $result = JsonSchema::generate($name, $url);
        if (is_bool($expected)) {
            static::assertSame($expected, $result);
        } else {
            static::assertNotEmpty($result);

            $keys = ['definitions', '$id', '$schema', 'type', 'properties', 'required', 'revision'];
            static::assertEquals($keys, array_keys($result), '', 0, 10, true);
            static::assertEquals($expected['properties'], array_keys($result['properties']), '', 0, 10, true);
            static::assertEquals($expected['required'], $result['required'], '', 0, 10, true);
        }
    }

    /**
     * Test revision change
     *
     * @covers ::addRevision()
     * @return void
     */
    public function testRevision()
    {
        $type = 'documents';
        $url = 'http://api.example.com/model/schema/' . $type;
        $result = JsonSchema::generate($type, $url);

        $revision = $result['revision'];
        static::assertNotEmpty($revision);

        // add custom property and check schema revision change
        $properties = TableRegistry::get('Properties');
        $data = [
            'name' => 'gustavo',
            'description' => '',
            'property_type_name' => 'string',
            'object_type_name' => 'documents',
        ];
        $entity = $properties->newEntity();
        $entity = $properties->patchEntity($entity, $data);
        $entity = $properties->save($entity);
        $result = JsonSchema::generate($type, $url);

        static::assertNotEmpty($result['revision']);
        static::assertNotEquals($revision, $result['revision']);

        // remove custom property and check schema revision is unchanged
        $properties->deleteOrFail($entity);
        $result = JsonSchema::generate($type, $url);

        static::assertNotEmpty($result['revision']);
        static::assertEquals($revision, $result['revision']);
    }

    /**
     * Test revision on abstract type
     *
     * @covers ::addRevision()
     * @return void
     */
    public function testNoRevision()
    {
        $type = 'objects';
        $url = 'http://api.example.com/model/schema/' . $type;
        $result = JsonSchema::generate($type, $url);
        static::assertFalse($result);
    }

    /**
     * Data provider for `testGenerate` test case.
     *
     * @return array
     */
    public function schemaRevisionProvider()
    {
        return [
            'objects' => [
                'objects',
                false,
            ],
            'documents' => [
                'documents',
                '1389311771',
            ],
        ];
    }

    /**
     * Test schemaRevision method

     * @param string $type Type name
     * @param string|bool $expected Expected revision
     * @return void
     * @covers ::schemaRevision()
     * @dataProvider schemaRevisionProvider
     */
    public function testSchemaRevision($type, $expected)
    {
        $result = JsonSchema::schemaRevision($type);
        static::assertEquals($expected, $result);
    }

    /**
     * Test schemaRevision method with cache

     * @return void
     * @covers ::schemaRevision()
     */
    // public function testSchemaRevisionCache()
    // {
    //     $type = 'documents';
    //     $result = JsonSchema::schemaRevision($type);
    //     $expected = Cache::read('revision_schema_' . $type, ObjectTypesTable::CACHE_CONFIG);
    //     static::assertEquals($expected, $result);

    //     // this will read from cache
    //     $resultCache = JsonSchema::schemaRevision($type);
    //     static::assertEquals($resultCache, $result);
    // }
}

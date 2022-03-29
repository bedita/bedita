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

use BEdita\API\Test\TestConstants;
use BEdita\Core\Model\Table\ObjectTypesTable;
use BEdita\Core\Utility\JsonSchema;
use Cake\Cache\Cache;
use Cake\Http\Exception\NotFoundException;
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
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Cache::clear(ObjectTypesTable::CACHE_CONFIG);
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
                        'id',
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
                        'password_modified',
                        'person_title',
                        'phone',
                        'pseudonym',
                        'publish_end',
                        'publish_start',
                        'published',
                        'state_name',
                        'status',
                        'street_address',
                        'surname',
                        'title',
                        'uname',
                        'user_preferences',
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
                        'id',
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
                        'id',
                        'another_description',
                        'another_title',
                        'body',
                        'categories',
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
                        'uuid',
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
                        'file_metadata',
                        'private_url',
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

            $keys = ['definitions', '$id', '$schema', 'type', 'properties', 'required', 'associations', 'relations', 'revision'];
            $found = array_keys($result);
            static::assertEquals(sort($keys), sort($found));
            static::assertEquals($expected['properties'], array_keys($result['properties']), '');
            static::assertEqualsCanonicalizing($expected['properties'], array_keys($result['properties']), '');
            static::assertEqualsWithDelta($expected['properties'], array_keys($result['properties']), 0, '');
            static::assertEquals($expected['required'], $result['required'], '');
            static::assertEqualsCanonicalizing($expected['required'], $result['required'], '');
            static::assertEqualsWithDelta($expected['required'], $result['required'], 0, '');
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
        $properties = TableRegistry::getTableLocator()->get('Properties');
        $data = [
            'name' => 'gustavo',
            'description' => '',
            'property_type_name' => 'string',
            'object_type_name' => 'documents',
        ];
        $entity = $properties->newEntity([]);
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
                TestConstants::SCHEMA_REVISIONS['documents'],
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
}

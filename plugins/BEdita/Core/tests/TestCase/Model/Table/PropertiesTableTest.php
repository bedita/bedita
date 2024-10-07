<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
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
use BEdita\Core\Model\Entity\Property;
use BEdita\Core\Model\Entity\StaticProperty;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Validation\Validation;

/**
 * {@see \BEdita\Core\Model\Table\PropertiesTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\PropertiesTable
 */
class PropertiesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\PropertiesTable
     */
    public $Properties;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Properties = TableRegistry::getTableLocator()->get('Properties');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Properties);

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
        $this->Properties->initialize([]);
        static::assertEquals('properties', $this->Properties->getTable());
        static::assertEquals('id', $this->Properties->getPrimaryKey());
        static::assertEquals('name', $this->Properties->getDisplayField());

        static::assertInstanceOf('\Cake\ORM\Association\BelongsTo', $this->Properties->ObjectTypes);
        static::assertInstanceOf('\Cake\ORM\Association\BelongsTo', $this->Properties->PropertyTypes);
        static::assertInstanceOf('\Cake\ORM\Behavior\TimestampBehavior', $this->Properties->behaviors()->get('Timestamp'));
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
                    'name' => 'long_body',
                    'description' => 'long text of a document',
                ],
            ],
            'emptyName' => [
                false,
                [
                    'name' => '',
                    'description' => 'another description',
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
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $property = $this->Properties->newEntity([]);
        $this->Properties->patchEntity($property, $data);
        $property->object_type_id = 1;
        $property->property_type_id = 1;
        $property->property = 'string';

        $error = (bool)$property->getErrors();
        static::assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->Properties->save($property);
            static::assertTrue((bool)$success);
        }
    }

    /**
     * Data provider for `testFindObjectType` test case.
     *
     * @return array
     */
    public function findObjectTypeProvider()
    {
        return [
            'objects' => [
                [],
                ['objects'],
            ],
            'documents' => [
                [
                    'another_title',
                    'another_description',
                ],
                ['documents'],
            ],
            'media' => [
                [
                    'media_property',
                ],
                ['media'],
            ],
            'files' => [
                [
                    'default_val_property',
                    'disabled_property',
                    'media_property',
                    'files_property',
                ],
                ['files'],
            ],
            'profiles' => [
                [
                    'another_birthdate',
                    'another_surname',
                    'number_of_friends',
                    'street_address',
                ],
                ['profiles'],
            ],
            'users' => [
                [
                    'another_username',
                    'another_email',
                ],
                ['users'],
            ],
            'too few' => [
                new BadFilterException(__d('bedita', 'Missing object type to get properties for')),
                [],
            ],
            'too many' => [
                new BadFilterException(__d('bedita', 'Missing object type to get properties for')),
                ['gustavo', 'supporto'],
            ],
        ];
    }

    /**
     * Test finder by object type.
     *
     * @param array|\Exception $expected List of expected properties names.
     * @param array $options Options to be passed to finder.
     * @return void
     * @dataProvider findObjectTypeProvider()
     * @covers ::findObjectType()
     */
    public function testFindObjectType($expected, array $options)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $result = $this->Properties->find('objectType', $options)
            ->find('type', ['dynamic'])
            ->all()
            ->extract('name')
            ->toList();

        sort($expected);
        sort($result);
        static::assertEquals($expected, $result, '');
        static::assertEqualsCanonicalizing($expected, $result, '');
        static::assertEqualsWithDelta($expected, $result, 0, '');
    }

    /**
     * Data provider for `testFindType` test case.
     *
     * @return array
     */
    public function findTypeProvider()
    {
        $objects = [
            'id',
            'uname',
            'status',
            'published',
            'lang',
            'locked',

            'title',
            'description',
            'body',
            'extra',

            'publish_start',
            'publish_end',
            'created',
            'modified',
            'created_by',
            'modified_by',
        ];
        $media = [
            'name',

            'provider',
            'provider_uid',
            'provider_url',
            'provider_thumbnail',
            'provider_extra',
        ];
        $documentsCustom = [ // Documents custom properties.
            'another_title',
            'another_description',
        ];
        $mediaCustom = [ // Media custom properties.
            'media_property',
        ];
        $filesCustom = [ // Files custom properties.
            'default_val_property',
            'files_property',
        ];

        return [
            'objects both' => [
                $objects,
                'objects',
            ],
            'documents both' => [
                array_merge($objects, $documentsCustom),
                'documents',
            ],
            'media both' => [
                array_merge($objects, $media, $mediaCustom),
                'media',
            ],
            'files both' => [
                array_merge($objects, $media, $mediaCustom, $filesCustom),
                'files',
            ],
            'documents static' => [
                $objects,
                'documents',
                'static',
            ],
            'documents dynamic' => [
                $documentsCustom,
                'documents',
                'dynamic',
            ],
            'media dynamic' => [
                $mediaCustom,
                'media',
                'dynamic',
            ],
            'files dynamic' => [
                array_merge($mediaCustom, $filesCustom),
                'files',
                'dynamic',
            ],
            'locations dynamic' => [
                [],
                'locations',
                'dynamic',
            ],
            'invalid parameters' => [
                new BadFilterException('Invalid options for finder "type"'),
                'locations',
                'gustavo',
            ],
        ];
    }

    /**
     * Test finder by object type that includes static properties.
     *
     * @param array|\Exception $expected List of expected properties names.
     * @param string $objectType Object type to find properties for
     * @param string $type Type of properties to be returned.
     * @return void
     * @dataProvider findTypeProvider()
     * @covers ::findType()
     */
    public function testFindType($expected, $objectType, $type = 'both')
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $count = 0;
        $result = $this->Properties->find('objectType', [$objectType])
            ->find('type', [$type])
            ->where(['enabled' => true])
            ->all()
            ->each(function ($row) use (&$count) {
                $count++;
                static::assertTrue(is_object($row));

                $class = Property::class;
                if (Validation::uuid($row->id)) {
                    $class = StaticProperty::class;
                }

                static::assertSame($class, get_class($row));
            })
            ->extract('name')
            ->toList();

        sort($expected);
        sort($result);

        static::assertCount($count, $result);
        static::assertEquals($expected, $result, '');
        static::assertEqualsCanonicalizing($expected, $result, '');
        static::assertEqualsWithDelta($expected, $result, 0, '');
    }

    /**
     * Test that by default both static and custom properties are returned.
     *
     * @return void
     * @covers ::beforeFind()
     */
    public function testBeforeFindDefault()
    {
        $expected = [
            // Objects static properties.
            'id',
            'uname',
            'status',
            'published',
            'lang',
            'locked',

            'title',
            'description',
            'body',
            'extra',

            'publish_start',
            'publish_end',
            'created',
            'modified',
            'created_by',
            'modified_by',

            // Media static properties.
            'name',

            'provider',
            'provider_uid',
            'provider_url',
            'provider_thumbnail',
            'provider_extra',

            // Media custom properties.
            'media_property',
        ];

        $result = $this->Properties->find('objectType', ['media'])
            ->all()
            ->extract('name')
            ->toList();

        sort($expected);
        sort($result);
        static::assertEquals($expected, $result, '');
        static::assertEqualsCanonicalizing($expected, $result, '');
        static::assertEqualsWithDelta($expected, $result, 0, '');
    }

    /**
     * Test that default options do not overwrite user-defined options.
     *
     * @return void
     * @covers ::beforeFind()
     */
    public function testBeforeFindDoNotOverwrite()
    {
        $expected = [
            // Media custom properties.
            'media_property',
        ];

        $result = $this->Properties->find('objectType', ['media'])
            ->find('type', ['dynamic'])
            ->all()
            ->extract('name')
            ->toList();

        static::assertEquals($expected, $result, '');
        static::assertEqualsCanonicalizing($expected, $result, '');
        static::assertEqualsWithDelta($expected, $result, 0, '');
    }

    /**
     * Data provider for `testFindResource()`.
     *
     * @return array
     */
    public function findResourceProvider(): array
    {
        return [
            'property' => [
                1,
                [
                    'name' => 'another_title',
                    'object_type_name' => 'documents',
                ],
            ],
            'no name' => [
                new BadFilterException('Missing required parameter "name"'),
                [
                    'object_type_name' => 'documents',
                ],
            ],
            'no type' => [
                new BadFilterException('Missing required parameter "object_type_name"'),
                [
                    'name' => 'a-name',
                ],
            ],
        ];
    }

    /**
     * Test custom finder `findResource()`.
     *
     * @param int $expected The value expected
     * @param array $options The options for the finder
     * @return void
     * @covers ::findResource()
     * @dataProvider findResourceProvider()
     */
    public function testFindResource($expected, $options)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        $query = $this->Properties->find('resource', $options);
        $entity = $query->first();
        static::assertEquals(1, $query->count());
        static::assertEquals($expected, $entity->id);
    }
}

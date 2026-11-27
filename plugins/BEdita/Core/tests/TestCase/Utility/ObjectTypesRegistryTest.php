<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\Model\Table\ObjectTypesTable;
use BEdita\Core\Utility\ObjectTypesRegistry;
use Cake\Cache\Cache;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\Core\Utility\ObjectTypesRegistry} Test Case
 */
#[CoversClass(ObjectTypesRegistry::class)]
class ObjectTypesRegistryTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Cache::drop(ObjectTypesTable::CACHE_CONFIG);
        Cache::setConfig(ObjectTypesTable::CACHE_CONFIG, ['className' => 'Array']);
        ObjectTypesRegistry::reset();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Cache::drop(ObjectTypesTable::CACHE_CONFIG);
        Cache::setConfig(ObjectTypesTable::CACHE_CONFIG, ['className' => 'Null']);
        ObjectTypesRegistry::reset();

        parent::tearDown();
    }

    /**
     * Test that object types have the same data and shape as when loaded from database with relations.
     *
     * @return void
     */
    public function testObjectTypeShape(): void
    {
        $ObjectTypes = TableRegistry::getTableLocator()->get('ObjectTypes');
        $expected = $ObjectTypes->find()
            ->contain(['LeftRelations.RightObjectTypes', 'RightRelations.LeftObjectTypes'])
            ->all();
        static::assertNotEmpty($expected);

        foreach ($expected as $objectType) {
            $actual = ObjectTypesRegistry::getObjectType($objectType->id);

            static::assertInstanceOf(ObjectType::class, $actual);
            static::assertSame(
                $this->describe($objectType),
                $this->describe($actual),
                sprintf('Object type "%s" differs', $objectType->name),
            );
            static::assertSame($actual->id, ObjectTypesRegistry::getObjectType($objectType->name)?->id);
            static::assertSame($actual->id, ObjectTypesRegistry::getObjectType($objectType->singular)?->id);
            static::assertSame($actual->id, ObjectTypesRegistry::getObjectType(ucfirst($objectType->name))?->id);
        }
    }

    /**
     * Describe an entity recursively: fields, source and state, with lists sorted to ignore DB ordering.
     *
     * @param mixed $value Value to describe.
     * @return mixed
     */
    protected function describe(mixed $value): mixed
    {
        if ($value instanceof EntityInterface) {
            $fields = [];
            foreach ($value->getVisible() as $field) {
                $fields[$field] = $this->describe($value->get($field));
            }
            ksort($fields);

            return [
                '_source' => $value->getSource(),
                '_new' => $value->isNew(),
                '_dirty' => $value->getDirty(),
                '_fields' => $fields,
            ];
        }
        if (is_array($value)) {
            $value = array_map($this->describe(...), $value);
            if (array_is_list($value)) {
                usort($value, fn($a, $b) => strcmp(json_encode($a), json_encode($b)));
            }

            return $value;
        }
        if (is_object($value)) {
            return json_encode($value);
        }

        return $value;
    }

    /**
     * Test lookup of missing object types.
     *
     * @return void
     */
    public function testGetObjectTypeMissing(): void
    {
        static::assertNull(ObjectTypesRegistry::getObjectType(99999));
        static::assertNull(ObjectTypesRegistry::getObjectType('not_a_type'));
        static::assertNull(ObjectTypesRegistry::getObjectType(''));
    }

    /**
     * Test properties and property types.
     *
     * @return void
     */
    public function testProperties(): void
    {
        $Properties = TableRegistry::getTableLocator()->get('Properties');
        $expected = $Properties->find('type', propType: 'dynamic')
            ->where(['object_type_id' => 2])
            ->all()
            ->extract('id')
            ->toList();
        sort($expected);
        $actual = array_map(fn($property) => $property->id, ObjectTypesRegistry::getProperties(2));
        sort($actual);
        static::assertNotEmpty($expected);
        static::assertSame($expected, $actual);
        static::assertSame([], ObjectTypesRegistry::getProperties(99999));

        $string = ObjectTypesRegistry::getPropertyTypeByName('string');
        static::assertNotNull($string);
        static::assertSame('string', $string->name);
        static::assertEquals($string->toArray(), ObjectTypesRegistry::getPropertyType($string->id)?->toArray());
        static::assertNull(ObjectTypesRegistry::getPropertyType(99999));
        static::assertNull(ObjectTypesRegistry::getPropertyTypeByName('not_a_type'));
    }

    /**
     * Test that data is read once from cache, and kept in memory afterwards.
     *
     * @return void
     */
    public function testInMemory(): void
    {
        $document = ObjectTypesRegistry::getObjectType('documents');
        static::assertNotNull(Cache::read(ObjectTypesRegistry::CACHE_KEY, ObjectTypesTable::CACHE_CONFIG));

        Cache::clear(ObjectTypesTable::CACHE_CONFIG);
        static::assertEquals($document->toArray(), ObjectTypesRegistry::getObjectType('documents')?->toArray());
        static::assertNull(Cache::read(ObjectTypesRegistry::CACHE_KEY, ObjectTypesTable::CACHE_CONFIG));

        ObjectTypesRegistry::reset();
        $reloaded = ObjectTypesRegistry::getObjectType('documents');
        static::assertNotNull(Cache::read(ObjectTypesRegistry::CACHE_KEY, ObjectTypesTable::CACHE_CONFIG));
        static::assertEquals($document->toArray(), $reloaded?->toArray());
    }

    /**
     * Test that `clear()` drops both cached and in-memory data.
     *
     * @return void
     */
    public function testClear(): void
    {
        ObjectTypesRegistry::getObjectType('documents');

        ObjectTypesRegistry::clear();
        static::assertNull(Cache::read(ObjectTypesRegistry::CACHE_KEY, ObjectTypesTable::CACHE_CONFIG));

        // Data kept in memory would be served without writing cache again, see `testInMemory()`.
        ObjectTypesRegistry::getObjectType('documents');
        static::assertNotNull(Cache::read(ObjectTypesRegistry::CACHE_KEY, ObjectTypesTable::CACHE_CONFIG));
    }

    /**
     * Test that changes to returned entities do not leak into the registry.
     *
     * @return void
     */
    public function testReturnsCopies(): void
    {
        $document = ObjectTypesRegistry::getObjectType('documents');
        $name = $document->name;
        $document->name = 'changed';
        static::assertSame($name, ObjectTypesRegistry::getObjectType('documents')?->name);

        $properties = ObjectTypesRegistry::getProperties(2);
        static::assertNotEmpty($properties);
        $name = $properties[0]->name;
        $properties[0]->name = 'changed';
        static::assertSame($name, ObjectTypesRegistry::getProperties(2)[0]->name);

        $propertyType = ObjectTypesRegistry::getPropertyTypeByName('string');
        $id = $propertyType->id;
        $propertyType->name = 'changed';
        static::assertSame('string', ObjectTypesRegistry::getPropertyTypeByName('string')?->name);
        static::assertSame('string', ObjectTypesRegistry::getPropertyType($id)?->name);

        $propertyType = ObjectTypesRegistry::getPropertyType($id);
        $propertyType->name = 'changed';
        static::assertSame('string', ObjectTypesRegistry::getPropertyType($id)?->name);
        static::assertSame('string', ObjectTypesRegistry::getPropertyTypeByName('string')?->name);
    }
}

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
namespace BEdita\Core\Utility;

use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\Model\Entity\Property;
use BEdita\Core\Model\Entity\PropertyType;
use BEdita\Core\Model\Entity\Relation;
use BEdita\Core\Model\Entity\RelationType;
use BEdita\Core\Model\Table\ObjectTypesTable;
use Cake\Cache\Cache;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * In-process registry of object types, relations, properties and property types.
 *
 * All the data is loaded at once with a few queries and stored in object types cache
 * under a single key, so that a request needs a single cache read to resolve every object type.
 * Entities returned by this class are shared, callers that need to modify them must clone them first.
 *
 * @since 6.14.0
 */
class ObjectTypesRegistry
{
    /**
     * Cache key used to store registry data in object types cache.
     *
     * @var string
     */
    public const CACHE_KEY = 'registry';

    /**
     * Registry data, `null` if not loaded yet.
     *
     * @var array|null
     */
    protected static ?array $registry = null;

    /**
     * Reset in-process registry data, so that it is reloaded on next access.
     *
     * @return void
     */
    public static function reset(): void
    {
        static::$registry = null;
    }

    /**
     * Clear object types cache and reset in-process registry data.
     *
     * @return void
     */
    public static function clear(): void
    {
        Cache::clear(ObjectTypesTable::CACHE_CONFIG);
        static::reset();
    }

    /**
     * Get an object type by its ID, name or singular name.
     *
     * Relations are set in `left_relations` and `right_relations`, like with
     * `contain(['LeftRelations.RightObjectTypes', 'RightRelations.LeftObjectTypes'])`.
     *
     * @param string|int $key Object type ID, name or singular name.
     * @return \BEdita\Core\Model\Entity\ObjectType|null
     */
    public static function getObjectType(string|int $key): ?ObjectType
    {
        $registry = static::registry();
        if (!is_numeric($key)) {
            $key = Inflector::underscore($key);
            $key = $registry['names'][$key] ?? $registry['singulars'][$key] ?? null;
            if ($key === null) {
                return null;
            }
        }

        return $registry['objectTypes'][(int)$key] ?? null;
    }

    /**
     * Get properties directly defined on an object type, not considering inheritance.
     *
     * @param int $objectTypeId Object type ID.
     * @return array<\BEdita\Core\Model\Entity\Property>
     */
    public static function getProperties(int $objectTypeId): array
    {
        return static::registry()['properties'][$objectTypeId] ?? [];
    }

    /**
     * Get a property type by its ID.
     *
     * @param int $id Property type ID.
     * @return \BEdita\Core\Model\Entity\PropertyType|null
     */
    public static function getPropertyType(int $id): ?PropertyType
    {
        return static::registry()['propertyTypes'][$id] ?? null;
    }

    /**
     * Get all property types, indexed by name.
     *
     * @return array<string, \BEdita\Core\Model\Entity\PropertyType>
     */
    public static function getPropertyTypes(): array
    {
        return static::registry()['propertyTypesByName'];
    }

    /**
     * Get registry data, reading it from cache or database if not loaded yet.
     *
     * Data without object types (i.e., database not populated yet, like during migrations or
     * while test fixtures are being set up) is neither cached nor kept in memory.
     *
     * @return array
     */
    protected static function registry(): array
    {
        if (static::$registry !== null) {
            return static::$registry;
        }

        $data = Cache::read(static::CACHE_KEY, ObjectTypesTable::CACHE_CONFIG);
        if ($data === null) {
            $data = static::fetch();
            if (empty($data['object_types'])) {
                return static::build($data);
            }
            Cache::write(static::CACHE_KEY, $data, ObjectTypesTable::CACHE_CONFIG);
        }

        return static::$registry = static::build($data);
    }

    /**
     * Fetch raw data from database, as plain arrays to keep cached data small.
     *
     * @return array
     */
    protected static function fetch(): array
    {
        $locator = TableRegistry::getTableLocator();
        $ObjectTypes = $locator->get('ObjectTypes');
        $Relations = $locator->get('Relations');
        $RelationTypes = $locator->get('RelationTypes');
        $Properties = $locator->get('Properties');
        $PropertyTypes = $locator->get('PropertyTypes');

        return [
            'object_types' => $ObjectTypes->unhydratedFind()
                ->orderBy([$ObjectTypes->aliasField('id') => 'ASC'])
                ->toArray(),
            'relations' => $Relations->unhydratedFind()
                ->orderBy([$Relations->aliasField('id') => 'ASC'])
                ->toArray(),
            'relation_types' => $RelationTypes->unhydratedFind()
                ->orderBy([
                    $RelationTypes->aliasField('relation_id') => 'ASC',
                    $RelationTypes->aliasField('object_type_id') => 'ASC',
                ])
                ->toArray(),
            'properties' => $Properties->unhydratedFind('type', propType: 'dynamic')
                ->orderBy([$Properties->aliasField('id') => 'ASC'])
                ->toArray(),
            'property_types' => $PropertyTypes->unhydratedFind()
                ->orderBy([$PropertyTypes->aliasField('id') => 'ASC'])
                ->toArray(),
        ];
    }

    /**
     * Build entities from raw data, with the same shape the ORM would produce.
     *
     * @param array $data Raw data.
     * @return array
     */
    protected static function build(array $data): array
    {
        $entity = function (string $class, array $fields, string $source): EntityInterface {
            return new $class($fields, [
                'useSetters' => false,
                'markClean' => true,
                'markNew' => false,
                'guard' => false,
                'source' => $source,
            ]);
        };

        $types = array_column($data['object_types'], null, 'id');
        $relations = array_column($data['relations'], null, 'id');
        $joins = [];
        foreach ($data['relation_types'] as $join) {
            $joins[$join['relation_id']][$join['side']->value][] = $join;
        }

        // For every type, build relations it stays on, with object types on the other side of the relation.
        $typesRelations = [];
        foreach ($data['relation_types'] as $join) {
            $side = $join['side']->value;
            $other = $side === 'left' ? 'right' : 'left';
            $Side = ucfirst($side);
            $Other = ucfirst($other);

            $relation = $relations[$join['relation_id']];
            $relation[$other . '_object_types'] = array_map(
                fn(array $otherJoin): EntityInterface => $entity(
                    ObjectType::class,
                    $types[$otherJoin['object_type_id']] + [
                        '_joinData' => $entity(RelationType::class, $otherJoin, $Other . 'RelationTypes'),
                    ],
                    $Other . 'ObjectTypes',
                ),
                $joins[$join['relation_id']][$other] ?? [],
            );
            $relation['_joinData'] = $entity(RelationType::class, $join, $Side . 'RelationTypes');

            $typesRelations[$join['object_type_id']][$side . '_relations'][] = $entity(
                Relation::class,
                $relation,
                $Side . 'Relations',
            );
        }

        $objectTypes = $names = $singulars = [];
        foreach ($types as $id => $fields) {
            /** @var \BEdita\Core\Model\Entity\ObjectType $objectType */
            $objectType = $entity(
                ObjectType::class,
                $fields + ($typesRelations[$id] ?? []) + ['left_relations' => [], 'right_relations' => []],
                'ObjectTypes',
            );
            $objectTypes[$id] = $objectType;
            $names[$objectType->name] = $id;
            $singulars[$objectType->singular] = $id;
        }

        $properties = [];
        foreach ($data['properties'] as $fields) {
            $properties[$fields['object_type_id']][] = $entity(Property::class, $fields, 'Properties');
        }

        $propertyTypes = $propertyTypesByName = [];
        foreach ($data['property_types'] as $fields) {
            /** @var \BEdita\Core\Model\Entity\PropertyType $propertyType */
            $propertyType = $entity(PropertyType::class, $fields, 'PropertyTypes');
            $propertyTypes[$propertyType->id] = $propertyType;
            $propertyTypesByName[$propertyType->name] = $propertyType;
        }

        return compact('objectTypes', 'names', 'singulars', 'properties', 'propertyTypes', 'propertyTypesByName');
    }
}

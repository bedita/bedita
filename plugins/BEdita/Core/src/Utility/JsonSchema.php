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

namespace BEdita\Core\Utility;

use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\Model\Entity\StaticProperty;
use BEdita\Core\Model\Table\ObjectTypesTable;
use Cake\Cache\Cache;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * JSON Schema generation utilities
 *
 * Provides static methods to generate JSON Schema for objects and resources
 */
class JsonSchema
{
    /**
     * Valid resource names
     *
     * @var array
     */
    const VALID_RESOURCES = [
        'applications',
        'roles',
        'streams',
    ];

    /**
     * Generate JSON Schema draft-6 (https://tools.ietf.org/html/draft-wright-json-schema-01)
     *
     * @param string $type Type name of a resource or object
     * @param string $url Url of this schema
     * @return mixed
     */
    public static function generate($type, $url)
    {
        $schema = Cache::remember(
            'schema_' . $type,
            function () use ($type) {
                return static::addRevision(static::typeSchema($type), $type);
            },
            ObjectTypesTable::CACHE_CONFIG
        );

        if (!is_array($schema)) {
            return $schema;
        }

        $baseSchema = [
            'definitions' => new \stdClass(),
            '$id' => $url,
            '$schema' => 'http://json-schema.org/draft-06/schema#',
            'type' => 'object',
        ];

        return array_merge($baseSchema, $schema);
    }

    /**
     * Schema of a resource or object type
     *
     * @param string $type Resource or object type name
     * @return mixed
     * @throws \Cake\Network\Exception\NotFoundException if no type is found
     */
    public static function typeSchema($type)
    {
        if (in_array($type, static::VALID_RESOURCES)) {
            return static::resourceSchema($type);
        }

        /* @var \BEdita\Core\Model\Table\ObjectTypesTable $ObjectTypes */
        $ObjectTypes = TableRegistry::get('ObjectTypes');
        try {
            $objectType = $ObjectTypes->get($type);

            return static::objectSchema($objectType);
        } catch (RecordNotFoundException $e) {
            throw new NotFoundException(__d('bedita', 'Type "{0}" not found', $type));
        }
    }

    /**
     * Add revision information to schema
     *
     * @param array|bool $schema Schema array or `false`
     * @param string $type Resource or object type name
     * @return array|bool Schema with `revision` or `false`
     */
    protected static function addRevision($schema, $type)
    {
        if (!is_array($schema)) {
            return $schema;
        }
        // remove 'description' from crc32 signature calculation -> not available in Sqlite
        $schemaNoDesc = Hash::remove($schema, 'properties.{*}.description');
        // properties order also differs between Sqlite and Mysql
        ksort($schemaNoDesc['properties']);
        $schema['revision'] = sprintf("%u", crc32(json_encode($schemaNoDesc)));

        return $schema;
    }

    /**
     * Get current revision of a type schema
     *
     * @param string $type Resource or object type name
     * @return string|bool Schema revision or `false` if no schema is found
     * @throws \Cake\Network\Exception\NotFoundException if no type is found
     */
    public static function schemaRevision($type)
    {
        return Cache::remember(
            'revision_schema_' . $type,
            function () use ($type) {
                $schema = static::generate($type, '');
                if (!is_array($schema)) {
                    return $schema;
                }

                return $schema['revision'];
            },
            ObjectTypesTable::CACHE_CONFIG
        );
    }

    /**
     * Build resource properties directly from a db table
     *
     * @param string $resource Resource type name
     * @return array JSON Schema array with `properties` and `required`
     */
    public static function resourceSchema($resource)
    {
        $table = TableRegistry::get((string)Inflector::camelize($resource));
        $entity = $table->newEntity();
        $schema = $table->getSchema();
        $hiddenProperties = $entity->hiddenProperties();

        $properties = [];
        $required = [];
        foreach ($schema->columns() as $name) {
            if (in_array($name, $hiddenProperties)) {
                    continue;
            }

            $accessMode = null;
            if (!$entity->isAccessible($name)) {
                $accessMode = 'readOnly';
            } elseif (in_array($name, $hiddenProperties)) {
                $accessMode = 'writeOnly';
            }
            $property = new StaticProperty(compact('name', 'table'));
            $properties[$name] = $property->getSchema($accessMode);
            if ($property->required && $accessMode === null) {
                $required[] = $name;
            }
        }

        return compact('properties', 'required');
    }

    /**
     * Object type properties representation as array
     *
     * @param \BEdita\Core\Model\Entity\ObjectType $objectType Object type to represent
     * @return mixed JSON Schema array with `properties` and `required`
     */
    public static function objectSchema(ObjectType $objectType)
    {
        return $objectType->schema;
    }
}

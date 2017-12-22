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
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
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
     * @param string $typeName Type name of a resource or object
     * @param string $url Url of this schema
     * @return mixed
     */
    public static function generate($typeName, $url)
    {
        $schema = static::typeSchema($typeName);
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
     * @param string $typeName Resource or object type name
     * @return mixed
     * @throws \Cake\Network\Exception\NotFoundException if no type is found
     */
    public static function typeSchema($typeName)
    {
        if (in_array($typeName, static::VALID_RESOURCES)) {
            return static::resourceSchema($typeName);
        }

        /* @var \BEdita\Core\Model\Table\ObjectTypesTable $ObjectTypes */
        $ObjectTypes = TableRegistry::get('ObjectTypes');
        try {
            $objectType = $ObjectTypes->get($typeName);

            return static::objectSchema($objectType);
        } catch (RecordNotFoundException $e) {
            throw new NotFoundException(__d('bedita', 'Type "{0}" not found', $typeName));
        }
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
            if (in_array($name, (array)$table->getPrimaryKey()) || in_array($name, $hiddenProperties)) {
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

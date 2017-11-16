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

namespace BEdita\Core\Model\Schema;

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
     * @return array Associative array representing schema
     */
    public static function generate($typeName, $url)
    {
        $isResource = in_array($typeName, static::VALID_RESOURCES);
        $schema = [];
        if ($isResource) {
            $schema = static::resourceSchema($typeName);
        } else {
            try {
                $objectType = TableRegistry::get('ObjectTypes')->get($typeName);
                $schema = static::objectSchema($objectType);
            } catch (RecordNotFoundException $e) {
                throw new NotFoundException(__d('bedita', 'Type "{0}" not found', $typeName));
            }
        }

        $baseSchema = [
            'definitions' => json_decode("{}"),
            '$id' => $url,
            '$schema' => 'http://json-schema.org/draft-06/schema#',
            'type' => 'object',
        ];

        return array_merge($baseSchema, $schema);
    }

    /**
     * Build resource properties directly from a db table
     *
     * @param string $name Resource type name
     * @return array JSON Schema array with `properties` and `required`
     */
    public static function resourceSchema($name)
    {
        $table = TableRegistry::get((string)Inflector::camelize($name));
        $entity = $table->newEntity();
        $schema = $table->getSchema();
        $hiddenProperties = $entity->hiddenProperties();

        $properties = [];
        $required = [];
        foreach ($schema->columns() as $column) {
            if (in_array($column, (array)$table->getPrimaryKey()) || in_array($column, $hiddenProperties)) {
                continue;
            }

            $metadata = $schema->getColumn($column);
            $properties[$column] = static::convertColumn($column, $metadata, $entity->accessible($column));
            if ($metadata['default'] === null && $metadata['null'] === false) {
                $required[] = $column;
            }
        }

        return compact('properties', 'required');
    }

    /**
     * Convert column metadata to JSON Schema property
     *
     * @param string $name Column name
     * @param array $metadata Column metadata
     * @param bool $accessible Property accessibility
     * @return array JSON Schema single property data
     */
    protected static function convertColumn($name, $metadata, $accessible)
    {
        $res = [];
        $res['$id'] = sprintf('/properties/%s', $name);

        switch ($metadata['type']) {
            case 'datetime':
                $res['type'] = 'string';
                $res['format'] = 'date-time';
                break;

            case 'text':
                $res['type'] = 'string';
                break;

            case 'timestamp':
                $res['type'] = 'string';
                $res['format'] = 'date-time';
                break;

            default:
                $res['type'] = $metadata['type'];
                if ($metadata['type'] === 'string' && !empty($metadata['length'])) {
                    $res['maxLength'] = $metadata['length'];
                }
        }

        $res['default'] = $metadata['default'];
        if (!$accessible) {
            $res['isMeta'] = true;
        }

        return $res;
    }

    /**
     * Object type properties representation as array
     *
     * @param \Cake\Datasource\EntityInterface $objectType Object type to represent
     * @return array JSON Schema array with `properties` and `required`
     */
    public static function objectSchema($objectType)
    {
        $objectProperties = TableRegistry::get('Properties')->find('objectType', [$objectType->get('name')])
            ->cache(sprintf('id_%s_props', $objectType->get('id')), '_bedita_object_types_')
            ->toArray();

        $entity = TableRegistry::get($objectType->get('name'))->newEntity();
        $properties = [];
        $required = [];
        foreach ($objectProperties as $property) {
            $properties[$property['name']] = static::convertProperty($property, $entity->accessible($property['name']));
        }

        return compact('properties', 'required');
    }

    /**
     * Convert `Property` entity to JSON Schema property
     *
     * @param array $property Property to convert
     * @param bool $accessible Property accessibility
     * @return array JSON Schema single property data
     */
    protected static function convertProperty($property, $accessible)
    {
        $res = [];
        $res['$id'] = sprintf('/properties/%s', $property['name']);
        $res['type'] = $property['property_type_name'];
        $res['description'] = $property['description'];
        if (!empty($property['label'])) {
            $res['title'] = $property['label'];
        }
        if (!$accessible) {
            $res['isMeta'] = true;
        }

        return $res;
    }
}

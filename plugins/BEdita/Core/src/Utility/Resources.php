<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Utility;

use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Utility class to resources creation/update/removal in migrations, shell scripts and similar scenarios
 *
 * Provides static methods to create and remove resources like applications, object_types, property_types, roles
 * and potentially many other using an array format.
 *
 * Every resource must have a unique field like `name` to be used as index in environment agnostic way:
 * in different environments same resources will have a different `id` (or other primary key) but should have a unique
 * `name`.
 *
 * Array example for object_types:
 *   [
 *     [
 *       'name' => 'custom_objects',
 *       'singular' => 'custom_object',
 *       'parent' => 'objects', // optional
 *       'description' => 'my custom description', // optional
 *     ],
 *   ],
 */
class Resources
{
    /**
     * Default options array with following keys:
     *
     *  - 'save': default options performing `Table::save()`
     *  - 'delete': default options performing `Table::delete()`
     *  - 'object_types': default options on object types
     *  - 'property_types': default options on property types
     *
     * @var array
     */
    protected static $defaults = [
        // since default usage is in migrations
        // don't commit transactions but let migrations do it
        'save' => [
            'atomic' => false,
        ],
        'delete' => [
            'atomic' => false,
        ],

        'object_types' => [
            'plugin' => 'BEdita/Core',
            'model' => 'Objects',
            'parent' => 'objects',
            'enabled' => 1,
        ],
        'property_types' => [
            'core_type' => 0,
        ]
    ];

    /**
     * Allowed resource types
     *
     * @var array
     */
    protected static $allowed = [
        'applications',
        'auth_providers',
        'categories',
        'config',
        'property_types',
        'object_types',
        'roles',
        'endpoints',
        'endpoint_permissions',
    ];

    /**
     * Types map for classes handling other resources
     *
     * @var array
     */
    protected static $otherTypesMap = [
        'properties' => Properties::class,
        'relations' => Relations::class,
    ];

    /**
     * Create new resources using data array.
     *
     * @param string $type Resource type name
     * @param array $data Resource data
     * @param array $options Table locator options
     * @return array
     */
    public static function create(string $type, array $data, array $options = []): array
    {
        $Table = static::getTable($type, $options);
        $result = [];

        foreach ($data as $item) {
            $resource = $Table->newEntity();
            $defaults = (array)Hash::get(static::$defaults, $type);
            $item = array_merge($defaults, $item);
            foreach ($item as $k => $v) {
                $resource->set($k, $v);
            }
            $result[] = $Table->saveOrFail($resource, static::$defaults['save']);
        }

        return $result;
    }

    /**
     * Remove resources using data array.
     *
     * @param string $type Resource type name
     * @param array $data Resource data
     * @param array $options Table locator options
     * @return void
     */
    public static function remove(string $type, array $data, array $options = []): void
    {
        $Table = static::getTable($type, $options);

        foreach ($data as $item) {
            $entity = static::loadEntity($item, $Table);
            $Table->deleteOrFail($entity, static::$defaults['delete']);
        }
    }

    /**
     * Update resources using data array.
     *
     * @param string $type Resource type name
     * @param array $data Resource data
     * @param array $options Table locator options
     * @return array
     */
    public static function update(string $type, array $data, array $options = []): array
    {
        $Table = static::getTable($type, $options);
        $result = [];

        foreach ($data as $item) {
            $entity = static::loadEntity($item, $Table);
            foreach ($item as $k => $v) {
                $entity->set($k, $v);
            }
            $result[] = $Table->saveOrFail($entity, static::$defaults['save']);
        }

        return $result;
    }

    /**
     * Load single resource entity using `name` or `id` fields condition or `resource` finder if set
     *
     * @param array $item Single resource data
     * @param Table $Table Resource table class
     * @return EntityInterface
     */
    protected static function loadEntity(array $item, Table $Table): EntityInterface
    {
        if ($Table->hasFinder('resource')) {
            return $Table->find('resource', $item)->firstOrFail();
        }

        return $Table->find()
            ->where(static::findCondition($item))
            ->firstOrFail();
    }

    /**
     * Generic save on resources grouped by `action` with possible values: `create`, `update` and `remove`.
     *
     * Supported resources: resources in static::$allowed + `relations` and `properties`.
     *
     * Array example where a role is created, an object type is updated and an application is removed.
     *
     * 'create' => [
     *      'roles' => [
     *          [
     *              'name' => 'new-role',
     *          ],
     *      ],
     *  ],
     * 'update' => [
     *      'object_types' => [
     *          [
     *              'name' => 'news',
     *              'hidden' => '["description"]',
     *          ],
     *      ],
     *  ],
     * 'remove' => [
     *      'applications' => [
     *          [
     *              'name' => 'frontend-app',
     *          ],
     *      ],
     *  ],
     *
     * @param array $resources Resources array.
     * @param array $options Table locator options.
     * @return array
     */
    public static function save(array $resources, array $options = []): array
    {
        $result = [];
        foreach ($resources as $action => $params) {
            if (!is_string($action) || !in_array($action, ['create', 'remove', 'update'])) {
                throw new BadRequestException(
                    __d('bedita', 'Save action "{0}" not allowed', $action)
                );
            }
            $params = (array)$params;
            foreach ($params as $type => $data) {
                $result[$action][$type] = static::saveType($action, $type, $data, $options);
            }
        }

        return $result;
    }

    /**
     * Save action on a resource type.
     *
     * @param string $action Save acttion.
     * @param string $type Resource type.
     * @param array $data Data array.
     * @param array $options Table locator options.
     * @return array
     */
    protected static function saveType(string $action, string $type, array $data, array $options = []): array
    {
        if (
            !in_array($type, static::$allowed) &&
            !in_array($type, array_keys(static::$otherTypesMap))
        ) {
            throw new BadRequestException(
                __d('bedita', 'Resource type "{0}" not supported', $type)
            );
        }

        if (in_array($type, static::$allowed)) {
            $class = static::class;
            $args = [$type, $data, $options];
        } else {
            $class = static::$otherTypesMap[$type];
            $args = [$data, $options];
        }

        $res = call_user_func_array([$class, $action], $args);
        if (!is_array($res)) {
            return [];
        }

        return $res;
    }

    /**
     * Get resource table with type validation
     *
     * @param string $type Resource type name
     * @param array $options Table locator options
     * @return \Cake\ORM\Table
     * @throws BadRequestException
     */
    protected static function getTable(string $type, array $options = []): Table
    {
        if (!in_array($type, static::$allowed)) {
            throw new BadRequestException(
                __d('bedita', 'Resource type not allowed "{0}"', $type)
            );
        }
        TableRegistry::getTableLocator()->clear();

        return TableRegistry::getTableLocator()
            ->get(Inflector::camelize($type), $options);
    }

    /**
     * Extract find condition from input array
     *
     * @param array $item Single resource data
     * @return array
     * @throws BadRequestException
     */
    protected static function findCondition(array $item): array
    {
        // use `name` or `id` as condition
        $keys = array_flip(['id', 'name']);
        $condition = array_filter(array_intersect_key($item, $keys));
        if (empty($condition)) {
            throw new BadRequestException(
                __d('bedita', 'Missing mandatory fields "id" or "name"')
            );
        }

        return $condition;
    }
}

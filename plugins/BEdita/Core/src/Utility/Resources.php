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

use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Utility class to resources creation/update/removal in migrations, shell scripts and similar scenarios
 *
 * Provides static methods to create and remove resources like applications, object_types, roles....
 * using an array format
 *
 * Example:
 *   [
 *     [
 *       'name' => 'custom_objects',
 *       'singular' => 'custom_object',
 *       'parent' => 'objects', // optional
 *       'description' => 'my custom description', // optional
 *     ],
 *   ]
 */
class Resources
{
    /**
     * Resource defaults in creation
     *
     * @var array
     */
    protected static $defaults = [
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
        'property_types',
        'object_types',
        'roles',
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
            $result[] = $Table->saveOrFail($resource);
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
            $condition = static::findCondition($item);
            $entity = $Table->find()
                ->where($condition)
                ->firstOrFail();

            $Table->deleteOrFail($entity);
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
            $condition = static::findCondition($item);
            $entity = $Table->find()
                ->where($condition)
                ->firstOrFail();
            // $entity = $Table->patchEntity($entity, $item);
            foreach ($item as $k => $v) {
                $entity->set($k, $v);
            }
            $result[] = $Table->saveOrFail($entity);
        }

        return $result;
    }

    /**
     * Save resources
     *
     *
     *  'create' => [
     *      'roles' => [
     *          [
     *              'name' => 'new-role',
     *          ]
     *      ]
     *  ]
     *
     * @param array $resources Resources array.
     * @param array $options Table locator options.
     * @return void
     */
    public static function save(array $resources, array $options = []): void
    {
        foreach ($resources as $action => $params) {
            if (!is_string($action) || !in_array($action, ['create', 'remove', 'update'])) {
                throw new BadRequestException(
                    __d('bedita', 'Save action "{0}" not allowed', $action)
                );
            }
            $params = (array)$params;
            foreach ($params as $type => $details) {
                if (
                    !is_string($type) ||
                    (!in_array($type, static::$allowed) && !in_array($type, array_keys(static::$otherTypesMap)))
                ) {
                    throw new BadRequestException(
                        __d('bedita', 'Resource type "{0}" not supported', $type)
                    );
                }
                if (in_array($type, static::$allowed)) {
                    $class = static::class;
                    $args = [$type, $details, $options];
                } else {
                    $class = static::$otherTypesMap[$type];
                    $args = [$details, $options];
                }
                call_user_func_array([$class, $action], $args);
            }
        }
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

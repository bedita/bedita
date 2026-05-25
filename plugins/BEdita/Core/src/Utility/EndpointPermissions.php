<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Utility;

/**
 * Utility class to handle endpoint permissions in migrations, shell scripts and similar scenarios.
 *
 * Provides static methods to create, update and remove endpoint permissions using an array format
 * with symbolic names instead of IDs.
 *
 * Example:
 *   [
 *     [
 *       'endpoint_name' => 'my-endpoint', // null for all endpoints
 *       'application_name' => 'my-app',   // null for all applications
 *       'role_name' => 'my-role',         // null for anonymous users
 *       'read' => true,                   // true, false, 'mine', 'block'
 *       'write' => false,                 // true, false, 'mine', 'block'
 *     ],
 *   ]
 */
class EndpointPermissions extends ResourcesBase
{
    /**
     * Default options array.
     *
     * @var array
     */
    protected static array $defaults = [
        'save' => [
            'atomic' => false,
        ],
        'delete' => [
            'atomic' => false,
        ],
    ];

    /**
     * Fields used to uniquely identify an endpoint permission record.
     *
     * @var array
     */
    protected const IDENTIFIER_FIELDS = ['endpoint_name', 'role_name', 'application_name'];

    /**
     * Create new endpoint permissions using data array.
     *
     * @param array $data Endpoint permissions data
     * @param array $options Table locator options
     * @return array
     */
    public static function create(array $data, array $options = []): array
    {
        $Table = static::getTable('EndpointPermissions', $options);
        $result = [];

        foreach ($data as $item) {
            $entity = $Table->newEmptyEntity();
            foreach ($item as $k => $v) {
                $entity->set($k, $v);
            }
            $result[] = $Table->saveOrFail($entity, static::$defaults['save']);
        }

        return $result;
    }

    /**
     * Update existing endpoint permissions using data array.
     *
     * @param array $data Endpoint permissions data
     * @param array $options Table locator options
     * @return array
     */
    public static function update(array $data, array $options = []): array
    {
        $Table = static::getTable('EndpointPermissions', $options);
        $result = [];

        foreach ($data as $item) {
            $identifiers = array_intersect_key($item, array_flip(static::IDENTIFIER_FIELDS));
            $entity = $Table->find('resource', ...$identifiers)->firstOrFail();
            $entity->set('read', $item['read']);
            $entity->set('write', $item['write']);
            $result[] = $Table->saveOrFail($entity, static::$defaults['save']);
        }

        return $result;
    }

    /**
     * Remove endpoint permissions using data array.
     *
     * @param array $data Endpoint permissions data
     * @param array $options Table locator options
     * @return void
     */
    public static function remove(array $data, array $options = []): void
    {
        $Table = static::getTable('EndpointPermissions', $options);

        foreach ($data as $item) {
            $identifiers = array_intersect_key($item, array_flip(static::IDENTIFIER_FIELDS));
            $entity = $Table->find('resource', ...$identifiers)->firstOrFail();
            $Table->deleteOrFail($entity, static::$defaults['delete']);
        }
    }
}

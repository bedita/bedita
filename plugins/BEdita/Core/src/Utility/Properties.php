<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Utility class to handle properties in migrations, shell scripts and other scenarios
 *
 * Provides static methods to create and remove properties using an array format
 *
 * Example:
 *   [
 *     [
 *       'name' => 'custom_one',
 *       'object' => 'documents',
 *       'property' => 'boolean',
 *       'description' => 'my custom description', // optional
 *     ],
 *     [
 *       'name' => 'custom_two',
 *       'object' => 'documents',
 *       'property' => 'string',
 *     ],
 *   ]
 */
class Properties extends ResourcesBase
{
    /**
     * Default options array with following keys:
     *
     *  - 'save': default options performing `Table::save()`
     *  - 'delete': default options performing `Table::delete()`
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
        'update' => [
            'atomic' => false,
        ],
    ];

    /**
     * Create new properties in `properties` table using input `$properties` array
     *
     * @param array $properties Properties data
     * @param array $options Table locator options
     * @return void
     */
    public static function create(array $properties, array $options = []): void
    {
        $Properties = static::getTable('Properties', $options);

        foreach ($properties as $p) {
            static::validate($p);
            $property = $Properties->newEntity([
                'name' => $p['name'],
                'property_type_name' => $p['property'],
                'object_type_name' => $p['object'],
                'description' => Hash::get($p, 'description'),
            ]);

            $Properties->saveOrFail($property, static::$defaults['save']);
        }
    }

    /**
     * Remove properties from `properties` table using `$properties` array
     *
     * @param array $properties Properties data
     * @param array $options Table locator options
     * @return void
     */
    public static function remove(array $properties, array $options = []): void
    {
        $Properties = static::getTable('Properties', $options);
        $ObjectTypes = static::getTable('ObjectTypes', $options);

        foreach ($properties as $p) {
            static::validate($p);
            $objectType = $ObjectTypes->get(Inflector::camelize($p['object']));

            /** @var \Cake\Datasource\EntityInterface $property */
            $property = $Properties->find()
                ->where([
                    'name' => $p['name'],
                    'object_type_id' => $objectType->get('id'),
                ])
                ->firstOrFail();

            $Properties->deleteOrFail($property, static::$defaults['delete']);
        }
    }

    /**
     * Update properties in `properties` table using input `$properties` array
     *
     * @param array $properties Properties data
     * @param array $options Table locator options
     * @return void
     */
    public static function update(array $properties, array $options = []): void
    {
        $Properties = static::getTable('Properties', $options);
        $ObjectTypes = static::getTable('ObjectTypes', $options);

        foreach ($properties as $p) {
            static::validate($p);
            $objectType = $ObjectTypes->get(Inflector::camelize($p['object']));

            /** @var \Cake\Datasource\EntityInterface $property */
            $property = $Properties->find()
                ->where([
                    'name' => $p['name'],
                    'object_type_id' => $objectType->get('id'),
                ])
                ->firstOrFail();
            $property->set('property_type_name', $p['property']);
            $property->set('description', Hash::get($p, 'description'));

            $Properties->saveOrFail($property, static::$defaults['update']);
        }
    }

    /**
     * Validate properties before creation or removal
     *
     * @param array $data Properties data
     * @return void
     * @throws \Cake\Http\Exception\BadRequestException
     */
    protected static function validate(array $data): void
    {
        $required = ['name', 'object', 'property'];
        $diff = array_diff_key(array_flip($required), array_filter($data));
        if (!empty($diff)) {
            throw new BadRequestException(
                __d('bedita', 'Missing mandatory property data "{0}"', implode(array_keys($diff)))
            );
        }
    }
}

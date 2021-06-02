<?php

/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Utility;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Project Model generation utilities
 *
 * Provides static methods to generate Project model
 *
 * @since 4.5.0
 */
class ProjectModel
{
    /**
     * Generate Project model
     *
     * @return array
     */
    public static function generate(): array
    {
        return [
            'property_types' => static::propertyTypes(),
            'object_types' => static::objectTypes(),
            'relations' => static::relations(),
            'properties' => static::properties(),
        ];
    }

    /**
     * Retrieve property types
     *
     * @return array
     */
    protected static function propertyTypes(): array
    {
        return TableRegistry::getTableLocator()->get('PropertyTypes')
            ->find()
            ->select(['name', 'params'])
            ->where(['core_type' => 0])
            ->toArray();
    }

    /**
     * Retrieve property types
     *
     * @return array
     */
    protected static function objectTypes(): array
    {
        return TableRegistry::getTableLocator()->get('ObjectTypes')
            ->find()
            ->each(function ($row) {
                /** @var \BEdita\Core\Mode\Entity\ObjectType $row */
                $row->unsetProperty([
                    'id',
                    'left_relations',
                    'right_relations',
                    'created',
                    'modified',
                    'core_type',
                ]);
                $row->setHidden(['relations', 'alias'], true);
            })
            ->toArray();
    }

    /**
     * Retrieve relations.
     *
     * @return array
     */
    protected static function relations(): array
    {
        return TableRegistry::getTableLocator()
            ->get('Relations')
            ->find('all', ['contain' => ['LeftObjectTypes', 'RightObjectTypes']])
            ->each(function ($row) {
                /** @var \BEdita\Core\Mode\Entity\Relation $row */
                $left = Hash::extract($row, 'left_object_types.{n}.name');
                $right = Hash::extract($row, 'right_object_types.{n}.name');
                sort($left);
                sort($right);
                $row->unsetProperty([
                    'id',
                ]);
                $row->set('left_object_types', $left);
                $row->set('right_object_types', $right);
            })
            ->toArray();
    }

    /**
     * Retrieve properties.
     *
     * @return array
     */
    protected static function properties(): array
    {
        return TableRegistry::getTableLocator()->get('Properties')
            ->find('type', ['dynamic'])
            ->each(function ($row) {
                /** @var \BEdita\Core\Mode\Entity\Property $row */
                $row->unsetProperty([
                    'id',
                    'created',
                    'modified',
                    'label',
                    'is_static',
                ]);
            })
            ->toArray();
    }

    /**
     * Calculates the difference between the current project model
     * and a new project model passed by argument as array.
     * Diff array will contain 'create', 'update' and 'remove' keys
     * with corresponding model items in order to sync the current
     * project model to the new one.
     *
     * @param array $project New project model
     * @return array
     */
    public static function diff(array $project): array
    {
        $create = $update = $remove = [];
        $currentModel = json_decode(json_encode(static::generate()), true);
        foreach ($currentModel as $key => $value) {
            $current = Hash::combine((array)$value, '{n}.name', '{n}');
            $new = Hash::combine((array)Hash::get($project, $key), '{n}.name', '{n}');
            $create[$key] = array_values(array_diff_key($new, $current));
            $remove[$key] = array_values(array_diff_key($current, $new));
            $update[$key] = static::itemsToUpdate($current, $new);
        }

        return array_filter(
            array_map('array_filter', compact('create', 'update', 'remove'))
        );
    }

    /**
     * Calculate items to update in a project model set.
     *
     * @param array $current Current items
     * @param array $new New items
     * @return array
     */
    protected static function itemsToUpdate(array $current, array $new): array
    {
        return array_filter(array_map(
            function ($k, $v) use ($current) {
                $diff = Hash::diff($v, $current[$k]);
                if (empty($diff)) {
                    return null;
                }

                return $v;
            },
            array_keys($new),
            array_values($new)
        ));
    }
}

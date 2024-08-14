<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Entity\Relation;
use Cake\Datasource\EntityInterface;
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
            'applications' => static::applications(),
            'roles' => static::roles(),
            'property_types' => static::propertyTypes(),
            'object_types' => static::objectTypes(),
            'relations' => static::relations(),
            'properties' => static::properties(),
            'categories' => static::categories(),
        ];
    }

    /**
     * Retrieve applications
     *
     * @return array
     */
    protected static function applications(): array
    {
        return TableRegistry::getTableLocator()->get('Applications')
            ->find()
            ->select(['name', 'description', 'enabled'])
            ->order(['name' => 'ASC'])
            ->toArray();
    }

    /**
     * Retrieve roles
     *
     * @return array
     */
    protected static function roles(): array
    {
        return TableRegistry::getTableLocator()->get('Roles')
            ->find()
            ->select(['name', 'description'])
            ->order(['name' => 'ASC'])
            ->toArray();
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
            ->order(['name' => 'ASC'])
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
            ->order(['name' => 'ASC'])
            ->all()
            ->each(function (EntityInterface $row) {
                unset($row['id']);
                unset($row['left_relations']);
                unset($row['right_relations']);
                unset($row['created']);
                unset($row['modified']);
                unset($row['core_type']);
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
        $relations = TableRegistry::getTableLocator()
            ->get('Relations')
            ->find('all', ['contain' => ['LeftObjectTypes', 'RightObjectTypes']])
            ->order(['name' => 'ASC'])
            ->all()
            ->each(function (EntityInterface $row) {
                $left = (array)Hash::extract($row, 'left_object_types.{n}.name');
                $right = (array)Hash::extract($row, 'right_object_types.{n}.name');
                sort($left);
                sort($right);
                $row->unset(['id', 'left_object_types', 'right_object_types']);
                $row->set('left', $left);
                $row->set('right', $right);
            })
            ->toArray();

        // remove `definitions` and `$schema` from `params` to avoid issues in JSON-Schema validation
        return array_map(
            function (Relation $relation) {
                $r = $relation->jsonSerialize();
                $r['params'] = Hash::remove((array)$r['params'], 'definitions');
                $r['params'] = Hash::remove($r['params'], '$schema');

                return array_filter($r);
            },
            (array)$relations
        );
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
            ->order(['name' => 'ASC'])
            ->all()
            ->each(function (EntityInterface $row) {
                $hidden = [
                    'id',
                    'created',
                    'modified',
                    'label',
                    'is_static',
                    'object_type_name',
                    'property_type_name',
                ];
                $row->set('object', $row->get('object_type_name'));
                $row->set('property', $row->get('property_type_name'));
                $row->setHidden($hidden, true);
            })
            ->toArray();
    }

    /**
     * Retrieve categories.
     *
     * @return array
     */
    protected static function categories(): array
    {
        return TableRegistry::getTableLocator()->get('Categories')
            ->find()
            ->order(['tree_left' => 'ASC'])
            ->all()
            ->each(function (EntityInterface $row) {
                $row->setHidden([
                    'id',
                    'created',
                    'modified',
                    'object_type_id',
                    'object_type_name',
                    'parent_id',
                    'tree_left',
                    'tree_right',
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
        foreach ($currentModel as $key => $items) {
            if ($key === 'properties' || $key === 'categories') {
                $diff = static::byObjectDiff((array)$items, (array)Hash::get($project, $key));
                $create[$key] = $diff['create'];
                $remove[$key] = $diff['remove'];
                $update[$key] = $diff['update'];
            } else {
                $current = Hash::combine((array)$items, '{n}.name', '{n}');
                $new = Hash::combine((array)Hash::get($project, $key), '{n}.name', '{n}');
                $create[$key] = array_values(array_diff_key($new, $current));
                $remove[$key] = array_values(array_diff_key($current, $new));
                $update[$key] = array_values(static::itemsToUpdate($current, $new));
            }
        }
        if (Hash::check($update, 'categories.{n}.name')) {
            $names = (array)Hash::extract($update['categories'], '{n}.name');
            $found = TableRegistry::getTableLocator()->get('Categories')->find()->where(['name IN' => $names])->toArray();
            $found = (array)Hash::extract($found, '{n}.name');
            $update['categories'] = array_filter($update['categories'], function ($category) use ($found) {
                return !in_array($category['name'], $found);
            });
        }

        return array_filter(
            array_map('array_filter', compact('create', 'update', 'remove'))
        );
    }

    /**
     * Calculate diff between current and project model resources
     * grouping by `object`.
     * Needed for `properties` and `categories` resources that may
     * have duplicate `name` fields, but still unique by object.
     *
     * @param array $items Current items.
     * @param array $projectItems Project items.
     * @return array
     */
    protected static function byObjectDiff(array $items, array $projectItems): array
    {
        $create = $update = $remove = [];
        $current = Hash::combine($items, '{n}.name', '{n}', '{n}.object');
        $new = Hash::combine($projectItems, '{n}.name', '{n}', '{n}.object');
        $allObjects = array_unique(array_merge(array_keys($current), array_keys($new)));
        foreach ($allObjects as $object) {
            $newItems = (array)Hash::get($new, $object);
            $currItems = (array)Hash::get($current, $object);
            $create = array_merge($create, array_values(array_diff_key($newItems, $currItems)));
            $remove = array_merge($remove, array_values(array_diff_key($currItems, $newItems)));
            $update = array_merge($update, array_values(static::itemsToUpdate($currItems, $newItems)));
        }

        return compact('create', 'update', 'remove');
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
            function ($k, array $v) use ($current) {
                if (empty($current[$k])) {
                    return null;
                }
                if (empty(Hash::diff($v, (array)$current[$k]))) {
                    return null;
                }

                return $v;
            },
            array_keys($new),
            array_values($new)
        ));
    }
}

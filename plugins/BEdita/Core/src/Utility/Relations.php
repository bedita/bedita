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

use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Utility class to handle relations in migrations, shell scripts and other scenarios
 *
 * Provides static methods to create and remove relations using an array format
 *
 * Example:
 *   [
 *     [
 *      'name' => 'poster',
 *      'label' => 'Poster',
 *      'inverse_name' => 'poster_of',
 *      'inverse_label' => 'Poster of',
 *      'description' => 'Document or event has a poster image',
 *      'params' => '{...}', // optional JSON SCHEMA definition
 *      'left' => ['documents', 'events'],
 *      'right' => ['images'],
 *     ],
 *    ]
 */
class Relations
{
    /**
     * Create new relations in `relations` table using input `$relations` array
     *
     * @param array $relations Relation data
     * @param array $options Table locator options
     * @return void
     */
    public static function create(array $relations, array $options = []): void
    {
        $Relations = TableRegistry::getTableLocator()->get('Relations', $options);
        foreach ($relations as $data) {
            static::validate($data);
            $relation = $Relations->newEntity($data);
            $relation = $Relations->saveOrFail($relation);

            static::addTypes($relation->get('id'), $data['left'], 'left', $options);
            static::addTypes($relation->get('id'), $data['right'], 'right', $options);
        }
    }

    /**
     * Add relation type to relation
     *
     * @param string $relation Relation name or ID
     * @param string $type Object type name
     * @param string $side Relation side, 'left' or 'right'
     * @param array $options Table locator options
     * @return void
     */
    public static function addRelationType(string $relation, string $type, string $side, array $options = []): void
    {
        $relation = TableRegistry::getTableLocator()
            ->get('Relations', $options)
            ->get($relation);
        static::addTypes($relation->get('id'), [$type], $side, $options);
    }

    /**
     * Add relation types to relation
     *
     * @param string|int $relationId Relation id
     * @param array $types Object type names
     * @param string $side Relation side, 'left' or 'right'
     * @param array $options Table locator options
     * @return void
     */
    protected static function addTypes($relationId, array $types, string $side, array $options = []): void
    {
        $RelationTypes = TableRegistry::getTableLocator()->get('RelationTypes', $options);
        $ObjectTypes = TableRegistry::getTableLocator()->get('ObjectTypes', $options);

        foreach ($types as $name) {
            $objectType = $ObjectTypes->get(Inflector::camelize($name));

            $entity = $RelationTypes->newEntity([
                'relation_id' => $relationId,
                'object_type_id' => $objectType->get('id'),
                'side' => $side,
            ]);
            $RelationTypes->saveOrFail($entity);
        }
    }

    /**
     * Remove relations from `relations` table using `$this->relations` array
     *
     * @param array $relations Relation data
     * @param array $options Table locator options
     * @return void
     */
    public static function remove(array $relations, array $options = []): void
    {
        $Relations = TableRegistry::getTableLocator()->get('Relations', $options);
        foreach ($relations as $r) {
            static::validate($r);
            $relation = $Relations->find()
                ->where(['name' => Hash::get($r, 'name')])
                ->firstOrFail();

            static::removeTypes($relation->get('id'), $r['left'], 'left', $options);
            static::removeTypes($relation->get('id'), $r['right'], 'right', $options);

            $Relations->deleteOrFail($relation);
        }
    }

    /**
     * Remove relation types from relation
     *
     * @param string|int $relationId Relation id
     * @param array $types Object type names
     * @param string $side Relation side, 'left' or 'right'
     * @param array $options Table locator options
     * @return void
     */
    protected static function removeTypes($relationId, array $types, string $side, array $options = []): void
    {
        $RelationTypes = TableRegistry::getTableLocator()->get('RelationTypes', $options);
        $ObjectTypes = TableRegistry::getTableLocator()->get('ObjectTypes', $options);

        foreach ($types as $name) {
            $objectType = $ObjectTypes->get(Inflector::camelize($name));

            $relationType = $RelationTypes->find()
                ->where([
                    'relation_id' => $relationId,
                    'object_type_id' => $objectType->get('id'),
                    'side' => $side,
                ])
                ->firstOrFail();

            $RelationTypes->deleteOrFail($relationType);
        }
    }

    /**
     * Remove relation type from relation
     *
     * @param string $relation Relation name or ID
     * @param string $type Object type name
     * @param string $side Relation side, 'left' or 'right'
     * @param array $options Table locator options
     * @return void
     */
    public static function removeRelationType(string $relation, string $type, string $side, array $options = []): void
    {
        $relation = TableRegistry::getTableLocator()
            ->get('Relations', $options)
            ->get($relation);
        static::removeTypes($relation->get('id'), [$type], $side, $options);
    }

    /**
     * Update relations in `relations` and  related `relation_types` tables using an array
     *
     * @param array $data Relation data
     * @param array $options Table locator options
     * @return array
     */
    public static function update(array $data, array $options = []): array
    {
        $Relations = TableRegistry::getTableLocator()->get('Relations', $options);

        foreach ($data as $r) {
            $relation = $Relations->find()
                ->where(['name' => Hash::get($r, 'name')])
                ->contain(['LeftObjectTypes', 'RightObjectTypes'])
                ->firstOrFail();
            static::updateTypes($relation, $r, $options);
            foreach ($r as $k => $v) {
                $relation->set($k, $v);
            }
            $result[] = $Relations->saveOrFail($relation);
        }

        return $result;
    }

    /**
     * Update relation types of relation
     *
     * @param EntityInterace $relation Relation entity
     * @param array $data Relation data
     * @param array $options Table locator options
     * @return void
     */
    protected static function updateTypes(EntityInterface $relation, array $data, array $options): void
    {
        $id = $relation->get('id');
        foreach (['left', 'right'] as $side) {
            $newTypes = (array)Hash::get($data, $side);
            if (!empty($newTypes)) {
                $currTypes = (array)Hash::extract($relation, sprintf('%s_object_types.{n}.name', $side));
                static::removeTypes($id, array_diff($currTypes, $newTypes), $side, $options);
                static::addTypes($id, array_diff($newTypes, $currTypes), $side, $options);
            }
        }
        $relation->unsetProperty(['left_object_types', 'right_object_types']);
    }

    /**
     * Validate relations before creation or removal
     *
     * @param array $data Relations data
     * @return void
     * @throws BadRequestException
     */
    protected static function validate(array $data): void
    {
        if (
            empty($data['left']) || !is_array($data['left']) ||
            empty($data['right']) || !is_array($data['right'])
        ) {
            throw new BadRequestException(
                __d('bedita', 'Missing left/right relation types')
            );
        }
    }
}

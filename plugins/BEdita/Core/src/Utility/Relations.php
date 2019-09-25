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

use Cake\ORM\TableRegistry;
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
    public static function create(array $relations, array $options = []) : void
    {
        $Relations = TableRegistry::getTableLocator()->get('Relations', $options);
        foreach ($relations as $data) {
            $relation = $Relations->newEntity($data);
            $relation = $Relations->saveOrFail($relation);

            static::addTypes($relation->get('id'), $data['left'], 'left', $options);
            static::addTypes($relation->get('id'), $data['right'], 'right', $options);
        }
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
    protected static function addTypes($relationId, array $types, string $side, array $options = []) : void
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
    public static function remove(array $relations, array $options = []) : void
    {
        $Relations = TableRegistry::getTableLocator()->get('Relations', $options);
        foreach ($relations as $r) {
            $relation = $Relations->find()
                ->where(['name' => $r['name']])
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
    protected static function removeTypes($relationId, array $types, string $side, array $options = []) : void
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
}

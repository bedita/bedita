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

namespace BEdita\Core\Model\Action;

use BEdita\Core\Model\Entity\ObjectEntity;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;
use Cake\Utility\Hash;

/**
 * Action to count related objects of passed entities.
 *
 * @since 4.4.0
 */
class CountRelatedObjectsAction extends BaseAction
{
    use LocatorAwareTrait;

    /**
     * Define the string used to get all relations's count.
     *
     * @var string
     */
    public const COUNT_ALL = 'all';

    /**
     * Default configuration.
     *
     * - `hydrate` if the count results should be hydrated in the entities
     *
     * @var array
     */
    protected $_defaultConfig = [
        'hydrate' => true,
    ];

    /**
     * The RelationsTable instance.
     *
     * @var \BEdita\Core\Model\Table\RelationsTable
     */
    protected $Relations = null;

    /**
     * The ObjectRelationTable instance
     *
     * @var \BEdita\Core\Model\Table\ObjectRelationsTable
     */
    protected $ObjectRelations = null;

    /**
     * The relations list described as
     *
     * ```
     * [
     *     'direct_name' => 'inverse_name',
     * ]
     * ```
     *
     * @var array
     */
    protected $relationsList = null;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function initialize(array $config): void
    {
        $this->Relations = $this->getTableLocator()->get('Relations');
        $this->ObjectRelations = $this->getTableLocator()->get('ObjectRelations');
    }

    /**
     * {@inheritDoc}
     *
     * `$data can contain:
     * - `entities` an array/collection of entity representing objects
     * - `count` a comma separated list of relations you want to count.
     *   Set to 'all' to count all relations.
     *
     * If the action is configured to hydrate entities (default) then the `_countData` property
     * of every entity involved will be populated.
     *
     * The returned result will be an array with items compsed by id and count by relations.
     *
     * ```
     * [
     *     [
     *         'id' => 5,
     *         'count' => [
     *             'relation_name_one' => 2,
     *             'relation_name_two' => 8,
     *         ],
     *     ],
     *     [
     *         'id' => 9,
     *         'count' => [
     *             'relation_name_one' => 0,
     *             'relation_name_two' => 3,
     *         ],
     *     ],
     * ]
     * ```
     */
    public function execute(array $data = []): array
    {
        [$directCount, $inverseCount] = $this->filterCount(Hash::get($data, 'count'));
        $count = array_merge($directCount, $inverseCount);
        /** @var \BEdita\Core\Model\Entity\ObjectEntity[] $entities*/
        $entities = (array)Hash::get($data, 'entities');
        if (empty($entities) || empty($count)) {
            return [];
        }

        $allRelations = $this->getRelationsList();
        $allRelations = array_merge(array_keys($allRelations), array_values($allRelations));

        // extract all entity ids
        $ids = $this->extractIds($entities, $allRelations);
        if (empty($ids)) {
            return [];
        }

        $directResult = $inverseResult = [];
        if (!empty($directCount)) {
            $directResult = $this->countRelations($directCount, $ids)->toArray();
        }

        if (!empty($inverseCount)) {
            $inverseResult = $this->countRelations($inverseCount, $ids, true)->toArray();
        }

        // the merge order here is important
        // it ensures that $directResult wins in case of duplication
        // for example if direct and inverse relation have the same name
        $result = $this->groupResultCountById($ids, array_merge($inverseResult, $directResult), $count);

        if ($this->getConfig('hydrate') === true) {
            $this->hydrateCount($entities, $result, $allRelations);
        }

        return $result;
    }

    /**
     * Extract ids from a list of entities.
     * The related properties ids are extracted too.
     *
     * @param array $entities The list of entities
     * @param array $properties Properties on which search on
     * @return array
     */
    protected function extractIds(array $entities, array $properties): array
    {
        $ids = [];
        foreach ($entities as $entity) {
            if (!$entity instanceof ObjectEntity) {
                continue;
            }

            $data = array_filter($entity->extract($properties));
            $ids = array_merge($ids, [$entity->id], (array)Hash::extract($data, '{s}.{n}.id'));
        }

        return array_unique($ids);
    }

    /**
     * Get list of relations as an array with names as keys
     * and inverse names as values.
     *
     * @return array
     */
    protected function getRelationsList(): array
    {
        if ($this->relationsList !== null) {
            return $this->relationsList;
        }

        $this->relationsList = $this->Relations->find('list', [
            'keyField' => 'name',
            'valueField' => 'inverse_name',
        ])->toArray();

        return $this->relationsList;
    }

    /**
     * Filter away from `$count` the strings that aren't object relations.
     * If $count === static::COUNT_ALL then get all relations.
     *
     * Return an array with first item contains a list of direct relations
     * and the second item contains a list of inverse relations.
     *
     * @param array|string $count The count
     * @return array
     */
    protected function filterCount($count): array
    {
        if (empty($count)) {
            return [[], []];
        }

        $relations = $this->getRelationsList();

        if ($count === static::COUNT_ALL) {
            return [array_keys($relations), array_values($relations)];
        }

        if (is_string($count)) {
            $count = explode(',', $count);
        }

        $direct = array_intersect($count, array_flip($relations));
        $inverse = array_intersect($count, $relations);

        return [$direct, $inverse];
    }

    /**
     * Return query with counted relations for object ids passed.
     *
     * @param array|null $relations The list of direct relations
     * @param array $ids A list of object ids
     * @param bool $inverse If you want to count inverse relation
     * @return \Cake\ORM\Query
     */
    protected function countRelations(array $relations, array $ids, bool $inverse = false): Query
    {
        $objectId = 'left_id';
        $relatedObjectId = 'right_id';
        $relatedObjectsTable = 'RightObjects';
        $relationField = 'name';
        if ($inverse) {
            $objectId = 'right_id';
            $relatedObjectId = 'left_id';
            $relatedObjectsTable = 'LeftObjects';
            $relationField = 'inverse_name';
        }

        $objectId = $this->ObjectRelations->aliasField($objectId);
        $relationField = sprintf('Relations.%s', $relationField);

        $query = $this->ObjectRelations->find();

        return $query
            ->enableHydration(false)
            ->select([
                'id' => $objectId,
                'relation_name' => $relationField,
                'count' => $query->func()->count($this->ObjectRelations->aliasField($relatedObjectId)),
            ])
            ->innerJoinWith($relatedObjectsTable, function (Query $q) {
                return $q->find('available');
            })
            ->innerJoinWith('Relations', function (Query $q) use ($relations, $relationField) {
                return $q->where([
                    sprintf('%s IN', $relationField) => $relations,
                ]);
            })
            ->where([sprintf('%s IN', $objectId) => $ids])
            ->group([$objectId, $relationField]);
    }

    /**
     * Hydrate count result into entities.
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity[] $entities The collection of entities
     * @param array $countData The count data
     * @param array $properties A list of properties on which search
     * @return void
     */
    protected function hydrateCount(array $entities, array $countData, array $properties = []): void
    {
        foreach ($countData as $data) {
            if (!array_key_exists('id', $data) || !array_key_exists('count', $data)) {
                continue;
            }

            /** @var \BEdita\Core\Model\Entity\ObjectEntity $object */
            $objects = $this->searchEntitiesById($data['id'], $entities, $properties);
            if (empty($objects)) {
                continue;
            }

            foreach ($objects as $object) {
                $object->set('_countData', $data['count']);
            }
        }
    }

    /**
     * Group count result by id and relation.
     * All relations that missing from `$countData` will be filled with zero.
     *
     * For example the count data as
     *
     * ```
     * [
     *     [
     *         'id' => 15,
     *         'relation_name' => 'relation_one',
     *         'count' => 2,
     *     ],
     *     [
     *         'id' => 15,
     *         'relation_name' => 'relation_two',
     *         'count' => 6,
     *     ],
     * ]
     * ```
     *
     * will be returned as
     *
     * ```
     * [
     *     [
     *         'id' => 15,
     *         'count' => [
     *             'relation_one' => 2,
     *             'relation_two' => 6,
     *             'relation_three' => 0, // present in $relations but missing from count data
     *         ],
     *     ],
     * ]
     * ```
     *
     * @param array $ids An array of ids that needs count
     * @param array $countData The count data
     * @param array $relations List of relations that need to be counted
     * @return array
     */
    protected function groupResultCountById(array $ids, array $countData, array $relations): array
    {
        $countById = Hash::combine($countData, '{n}.relation_name', '{n}.count', '{n}.id');
        $baseCount = array_fill_keys($relations, 0);
        $countResult = [];
        foreach ($ids as $id) {
            if (!array_key_exists($id, $countById)) {
                $countById[$id] = [];
            }

            $countResult[] = [
                'id' => $id,
                'count' => $countById[$id] + $baseCount,
            ];
        }

        return $countResult;
    }

    /**
     * Search entities by `$id` from a collection of entities.
     * The search is done in the passed `$properties` too.
     *
     * Return all entities with searched id.
     *
     * @param int $id The entity id to look for
     * @param \Cake\Datasource\EntityInterface[] $entities The enitites on which search
     * @param array $properties A list of properties
     * @return array
     */
    protected function searchEntitiesById($id, $entities, array $properties): array
    {
        $entitiesFound = [];
        foreach ($entities as $entity) {
            if ($entity->id === $id) {
                $entitiesFound[] = $entity;

                continue;
            }

            $found = $this->searchEntitiesInProperties($id, $entity, $properties);
            $entitiesFound = array_merge($entitiesFound, $found);
        }

        return $entitiesFound;
    }

    /**
     * Search entities by id looking in passed `$entity` properties.
     *
     * @param int $id The id to search
     * @param \Cake\Datasource\EntityInterface $entity The starting entity
     * @param array $properties A list of properties to look in
     * @return array
     */
    protected function searchEntitiesInProperties($id, EntityInterface $entity, array $properties): array
    {
        return collection(array_filter($entity->extract($properties)))
            ->unfold()
            ->filter(function ($item) use ($id) {
                if (!$item instanceof EntityInterface) {
                    return false;
                }

                return $item->id === $id;
            })
            ->toList();
    }
}

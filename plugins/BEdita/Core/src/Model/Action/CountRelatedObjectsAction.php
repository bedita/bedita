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

use BEdita\Core\Model\Action\BaseAction;
use Cake\Database\Expression\QueryExpression;
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
     * {@inheritDoc}
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
     */
    public function execute(array $data = []): array
    {
        list($directCount, $inverseCount) = $this->filterCount(Hash::get($data, 'count'));
        $count = array_merge($directCount, $inverseCount);
        /** @var \BEdita\Core\Model\Entity\ObjectEntity[] $entities*/
        $entities = (array)$data['entities'];
        if (empty($entities) || empty($count)) {
            return $entities;
        }

        // extract all objects' ids
        $ids = (array)Hash::extract($entities, '{n}.id');
        foreach ($count as $relation) {
            $ids = array_merge($ids, (array)Hash::extract($entities, "{n}.$relation.{n}.id"));
        }
        $ids = array_unique($ids);

        $directResult = $inverseResult = [];
        if (!empty($directCount)) {
            $directResult = $this->countRelations($directCount, $ids)->toArray();
        }

        if (!empty($inverseCount)) {
            $inverseResult = $this->countRelations($inverseCount, $ids, true)->toArray();
        }

        $result = array_merge($directResult, $inverseResult);

        if ($this->getConfig('hydrate') === true) {
            $this->hydrateCount($entities, $result);
        }

        return $result;
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
        $query = $this->Relations->find('list', [
            'keyField' => 'name',
            'valueField' => 'inverse_name',
        ]);

        if ($count === static::COUNT_ALL) {
            $relations = $query->toArray();

            return [array_keys($relations), array_values($relations)];
        }

        if (is_string($count)) {
            $count = explode(',', $count);
        }
        if (empty($count)) {
            return [];
        }

        $relations = $query
            ->where(function (QueryExpression $exp) use ($count) {
                return $exp
                    ->or($exp->in($this->Relations->aliasField('name'), $count))
                    ->in($this->Relations->aliasField('inverse_name'), $count);
            })
            ->toArray();

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
     * @return Query
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
     * @param \BEdita\Core\Model\Entity\ObjectEntity $entities The collection of entities
     * @param array $countResult The count result
     * @return void
     */
    protected function hydrateCount($entities, array $countResult): void
    {
        $relations = array_unique(Hash::extract($countResult, '{n}.relation_name'));
        $groupById = Hash::combine($countResult, '{n}.relation_name', '{n}.count', '{n}.id');

        foreach ($groupById as $id => $count) {
            /** @var \BEdita\Core\Model\Entity\ObjectEntity $object */
            $object = $this->searchEntityById($id, $entities, $relations);
            if (!$object) {
                continue;
            }

            $object->set('_countData', $count);
        }
    }

    /**
     * Search an entity by `$id` from a collection of entities.
     * The search is done in the passed `$properties` too.
     *
     * @param int $id The entity id to look for
     * @param \Cake\Datasource\EntityInterface[] $entities The enitites on which search
     * @param array $properties A list of properties
     * @return \Cake\Datasource\EntityInterface|null
     */
    protected function searchEntityById($id, $entities, array $properties): ?EntityInterface
    {
        foreach ($entities as $entity) {
            if ($entity->id === $id) {
                return $entity;
            }

            $found = $this->searchEntityInProperties($id, $entity, $properties);
            if ($found !== null) {
                return $found;
            }
        }
    }

    /**
     * Search an entity by id looking in passed `$entity` properties.
     *
     * @param int $id The id to search
     * @param EntityInterface $entity The starting entity
     * @param array $properties A list of properties to look in
     * @return EntityInterface|null
     */
    protected function searchEntityInProperties($id, EntityInterface $entity, array $properties): ?EntityInterface
    {
        foreach ($properties as $prop) {
            if (!$entity->has($prop)) {
                continue;
            }

            $found = collection($entity->get($prop))
                ->filter(function ($item) use ($id) {
                    if (!$item instanceof EntityInterface) {
                        return false;
                    }

                    return $item->id === $id;
                })
                ->first();

            if (!empty($found)) {
                return $found;
            }
        }

        return null;
    }
}

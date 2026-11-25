<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Association;
use Cake\ORM\Behavior;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Swaggest\JsonSchema\Schema;

/**
 * Relations behavior
 */
class RelationsBehavior extends Behavior
{
    /**
     * @inheritDoc
     */
    protected array $_defaultConfig = [
        'implementedMethods' => [
            'setupRelations' => 'setupRelations',
            'getRelations' => 'getRelations',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $table = $this->table();
        if (!$table->hasBehavior('ObjectType')) {
            $table->addBehavior('BEdita/Core.ObjectType');
        }

        $this->setupRelations();
    }

    /**
     * Getter for object type.
     *
     * @param \BEdita\Core\Model\Entity\ObjectType|string|int|null $type Method arguments.
     * @return \BEdita\Core\Model\Entity\ObjectType
     */
    protected function objectType(ObjectType|string|int|null $type = null): ObjectType
    {
        /** @var \BEdita\Core\Model\Behavior\ObjectTypeBehavior $objectTypeBehavior */
        $objectTypeBehavior = $this->table()->getBehavior('ObjectType');

        return $objectTypeBehavior->objectType($type);
    }

    /**
     * Creates a new RelatedTo association between this table and a target
     * table. A "belongs to many" association is a M-N relationship.
     *
     * Target table can be inferred by its name, which is provided in the
     * first argument, or you can either pass the class name to be instantiated or
     * an instance of it directly.
     *
     * The options array accept the same keys as {@see \Cake\ORM\Table::belongsToMany()}.
     *
     * This method will return the association object that was built.
     *
     * @param string $associated The alias for the target table. This is used to
     *      uniquely identify the association.
     * @param array $options List of options to configure the association definition.
     * @return \Cake\ORM\Association
     */
    protected function relatedTo(string $associated, array $options = []): Association
    {
        $options += ['sourceTable' => $this->table()];
        $association = new RelatedTo($associated, $options);

        return $this->table()->associations()->add($association->getName(), $association);
    }

    /**
     * Set up relations for the current table.
     *
     * @param \BEdita\Core\Model\Entity\ObjectType|string|int|null $objectType Object type name or ID.
     * @return void
     */
    public function setupRelations(ObjectType|string|int|null $objectType = null): void
    {
        if ($objectType === null) {
            $objectType = $this->table()->getAlias();
        }

        try {
            $objectType = $this->objectType($objectType);
        } catch (RecordNotFoundException $e) {
            return;
        }

        // Add relations to the left side.
        foreach ($objectType->getRelations('left') as $relation) {
            if ($this->table()->hasAssociation($relation->alias) === true) {
                continue;
            }

            $through = TableRegistry::getTableLocator()->get(
                $relation->alias . 'ObjectRelations',
                ['className' => 'ObjectRelations'],
            );
            $through->getValidator()->setProvider(
                'jsonSchema',
                Schema::import(!empty($relation->get('params')) ? $relation->params : true),
            );
            $targetObjectType = ObjectType::getClosestCommonAncestor(...(array)$relation->right_object_types);

            $this->relatedTo($relation->alias, [
                'className' => $targetObjectType !== null ? $targetObjectType->table : 'BEdita/Core.Objects',
                'through' => $through,
                'foreignKey' => 'left_id',
                'targetForeignKey' => 'right_id',
                'conditions' => [
                    $through->aliasField('relation_id') => $relation->id,
                ],
                'finder' => 'available',
                'sort' => [
                    $through->aliasField('priority') => 'asc',
                ],
                'objectType' => $targetObjectType,
                'relation' => $relation,
            ]);
        }

        // Add relations to the right side.
        foreach ($objectType->getRelations('right') as $relation) {
            if ($this->table()->hasAssociation($relation->inverse_alias) === true) {
                continue;
            }

            $through = TableRegistry::getTableLocator()->get(
                $relation->inverse_alias . 'ObjectRelations',
                ['className' => 'ObjectRelations'],
            );
            $through->getValidator()->setProvider(
                'jsonSchema',
                Schema::import(!empty($relation->get('params')) ? $relation->params : true),
            );
            $targetObjectType = ObjectType::getClosestCommonAncestor(...(array)$relation->left_object_types);

            $this->relatedTo($relation->inverse_alias, [
                'className' => $targetObjectType !== null ? $targetObjectType->table : 'BEdita/Core.Objects',
                'through' => $through,
                'foreignKey' => 'right_id',
                'targetForeignKey' => 'left_id',
                'conditions' => [
                    $through->aliasField('relation_id') => $relation->id,
                ],
                'finder' => 'available',
                'sort' => [
                    $through->aliasField('inv_priority') => 'asc',
                ],
                'objectType' => $targetObjectType,
                'relation' => $relation,
            ]);
        }
    }

    /**
     * Get a list of all available relations indexed by their name with regards of side.
     *
     * @return array<\BEdita\Core\Model\Entity\Relation>
     * @deprecated Use `ObjectType::getRelations()` instead.
     * @codeCoverageIgnore
     */
    public function getRelations(): array
    {
        return $this->objectType()->getRelations();
    }

    /**
     * Finder for objects having/not-having specific relations with other objects.
     *
     * The `$args` parameter can be an array of relations or a single relation value
     * that says whether the related objects should exist or not. If `value` is present it will be used.
     * The allowed values are all values that can be interpreted as boolean using `filter_var`.
     *
     * The following calls are equivalent:
     * - `find('hasRelation', value: ['relation_name' => true, 'other_relation' => false])`
     * - `find('hasRelation', relation_name: true, other_relation: false)`
     * - `find('hasRelation', relation_name: 1, other_relation: 0)`
     * - `find('hasRelation', relation_name: 'true', other_relation: 'false')`
     * - `find('hasRelation', relation_name: '1', other_relation: '0')`
     * - `find('hasRelation', relation_name: false, other_relation: true)`
     * - `find('hasRelation', relation_name: 0, other_relation: 1)`
     * - `find('hasRelation', relation_name: 'false', other_relation: 'true')`
     * - `find('hasRelation', relation_name: '0', other_relation: '1')`
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array|bool|string|int $args Relations to check for. If `value` is present it will be used.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findHasRelation(SelectQuery $query, array|bool|int|string ...$args): SelectQuery
    {
        $origRelations = $args['value'] ?? $args;
        // convert all values to boolean, removing first any non-boolean values
        $relations = array_map(
            fn($value) => filter_var($value, FILTER_VALIDATE_BOOL),
            array_filter((array)$origRelations, fn($value, $key) => !is_numeric($key) && filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) !== null, ARRAY_FILTER_USE_BOTH),
        );

        if (empty($relations) || count($relations) !== count($origRelations)) {
            throw new BadFilterException(__d('bedita', 'Invalid value for "relation" parameter using hasRelation finder'));
        }

        foreach ($relations as $relation => $value) {
            $associationName = Inflector::camelize($relation);
            $subquery = $this->table()
                ->find('type', value: $this->table()->getAlias())
                ->find('available')
                ->select([$this->table()->aliasField('id')])
                ->innerJoinWith($associationName, fn(SelectQuery $q) => $q->find('available'));
            match ($value) {
                true => $query->where(fn(QueryExpression $exp) => $exp->in($this->table()->aliasField('id'), $subquery)),
                false => $query->where(fn(QueryExpression $exp) => $exp->notIn($this->table()->aliasField('id'), $subquery)),
            };
        }

        return $query;
    }
}

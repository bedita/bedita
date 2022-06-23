<?php
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

use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Swaggest\JsonSchema\Schema;

/**
 * Relations behavior
 */
class RelationsBehavior extends Behavior
{
    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
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

        $table = $this->getTable();
        if (!$table->hasBehavior('ObjectType')) {
            $table->addBehavior('BEdita/Core.ObjectType');
        }

        $this->setupRelations();
    }

    /**
     * Getter for object type.
     *
     * @param array $args Method arguments.
     * @return \BEdita\Core\Model\Entity\ObjectType
     */
    protected function objectType(...$args)
    {
        return $this->getTable()->behaviors()->call('objectType', $args);
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
    protected function relatedTo($associated, array $options = [])
    {
        $options += ['sourceTable' => $this->getTable()];
        $association = new RelatedTo($associated, $options);

        return $this->getTable()->associations()->add($association->getName(), $association);
    }

    /**
     * Set up relations for the current table.
     *
     * @param string|int|null $objectType Object type name or ID.
     * @return void
     */
    public function setupRelations($objectType = null)
    {
        if ($objectType === null) {
            $objectType = $this->getTable()->getAlias();
        }

        try {
            $objectType = $this->objectType($objectType);
        } catch (RecordNotFoundException $e) {
            return;
        }

        // Add relations to the left side.
        foreach ($objectType->getRelations('left') as $relation) {
            if ($this->getTable()->hasAssociation($relation->alias) === true) {
                continue;
            }

            $through = TableRegistry::getTableLocator()->get(
                $relation->alias . 'ObjectRelations',
                ['className' => 'ObjectRelations']
            );
            $through->getValidator()->setProvider(
                'jsonSchema',
                Schema::import($relation->has('params') ? $relation->params : true)
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
            ]);
        }

        // Add relations to the right side.
        foreach ($objectType->getRelations('right') as $relation) {
            if ($this->getTable()->hasAssociation($relation->inverse_alias) === true) {
                continue;
            }

            $through = TableRegistry::getTableLocator()->get(
                $relation->inverse_alias . 'ObjectRelations',
                ['className' => 'ObjectRelations']
            );
            $through->getValidator()->setProvider(
                'jsonSchema',
                Schema::import($relation->has('params') ? $relation->params : true)
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
            ]);
        }
    }

    /**
     * Get a list of all available relations indexed by their name with regards of side.
     *
     * @return \BEdita\Core\Model\Entity\Relation[]
     * @deprecated Use `ObjectType::getRelations()` instead.
     */
    public function getRelations()
    {
        return $this->objectType()->getRelations();
    }
}

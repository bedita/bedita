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
use League\JsonReference\Dereferencer;

/**
 * Relations behavior
 */
class RelationsBehavior extends Behavior
{

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'objectType' => null,
        'implementedMethods' => [
            'objectType' => 'objectType',
            'setupRelations' => 'setupRelations',
            'getRelations' => 'getRelations',
        ],
    ];

    /**
     * Object type instance.
     *
     * @var \BEdita\Core\Model\Entity\ObjectType
     */
    protected $objectType;

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setupRelations();
    }

    /**
     * Getter/setter for object type.
     *
     * @param \BEdita\Core\Model\Entity\ObjectType|string|int|null $objectType Object type entity, name or ID.
     * @return \BEdita\Core\Model\Entity\ObjectType|null
     */
    public function objectType($objectType = null)
    {
        if ($objectType === null) {
            return $this->objectType;
        }

        $table = TableRegistry::get('ObjectTypes');
        if (!($objectType instanceof ObjectType)) {
            $objectType = $table->get($objectType);
        }

        $this->objectType = $objectType;

        return $this->objectType;
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
            $objectType = $this->getConfig('objectType') ?: $this->getTable()->getAlias();
        }

        try {
            $objectType = $this->objectType($objectType);
        } catch (RecordNotFoundException $e) {
            return;
        }

        // Add relations to the left side.
        foreach ($objectType->left_relations as $relation) {
            if ($this->getTable()->association($relation->alias) !== null) {
                continue;
            }

            $className = 'BEdita/Core.Objects';
            if (count($relation->right_object_types) === 1) {
                $className = $relation->right_object_types[0]->table;
            }

            $through = TableRegistry::get(
                $relation->alias . 'ObjectRelations',
                ['className' => 'ObjectRelations']
            );
            $through->validator()->setProvider(
                'jsonSchema',
                Dereferencer::draft4()->dereference(json_decode(json_encode($relation->params)))
            );

            $this->relatedTo($relation->alias, [
                'className' => $className,
                'through' => $through->getRegistryAlias(),
                'foreignKey' => 'left_id',
                'targetForeignKey' => 'right_id',
                'conditions' => [
                    $through->aliasField('relation_id') => $relation->id,
                ],
                'sort' => [
                    $through->aliasField('priority') => 'asc',
                ],
            ]);
        }

        // Add relations to the right side.
        foreach ($objectType->right_relations as $relation) {
            if ($this->getTable()->association($relation->inverse_alias) !== null) {
                continue;
            }

            $className = 'BEdita/Core.Objects';
            if (count($relation->left_object_types) === 1) {
                $className = $relation->left_object_types[0]->table;
            }

            $through = TableRegistry::get(
                $relation->inverse_alias . 'ObjectRelations',
                ['className' => 'ObjectRelations']
            );
            $through->validator()->setProvider(
                'jsonSchema',
                Dereferencer::draft4()->dereference(json_decode(json_encode($relation->params)))
            );

            $this->relatedTo($relation->inverse_alias, [
                'className' => $className,
                'through' => $through->getRegistryAlias(),
                'foreignKey' => 'right_id',
                'targetForeignKey' => 'left_id',
                'conditions' => [
                    $through->aliasField('relation_id') => $relation->id,
                ],
                'sort' => [
                    $through->aliasField('inv_priority') => 'asc',
                ],
            ]);
        }
    }

    /**
     * Get a list of all available relations indexed by their name with regards of side.
     *
     * @return \BEdita\Core\Model\Entity\Relation[]
     */
    public function getRelations()
    {
        $relations = collection($this->objectType->left_relations)
            ->indexBy('name')
            ->append(
                collection($this->objectType->right_relations)
                    ->indexBy('inverse_name')
            );

        return $relations->toArray();
    }
}

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

use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;

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

        $ObjectTypes = TableRegistry::get('ObjectTypes');

        try {
            $this->objectType = $ObjectTypes->get(
                $this->getConfig('objectType') ?: $this->getTable()->getAlias()
            );
        } catch (RecordNotFoundException $e) {
            return;
        }

        $this->objectType = $ObjectTypes->loadInto(
            $this->objectType,
            ['LeftRelations', 'RightRelations']
        );

        // Add relations to the left side.
        foreach ($this->objectType->left_relations as $relation) {
            $this->relatedTo($relation->alias, [
                'className' => 'Objects',
                'through' => 'ObjectRelations',
                'foreignKey' => 'left_id',
                'targetForeignKey' => 'right_id',
                'conditions' => [
                    'ObjectRelations.relation_id' => $relation->id,
                ],
                'sort' => [
                    'ObjectRelations.priority' => 'asc',
                ],
            ]);
        }

        // Add relations to the right side.
        foreach ($this->objectType->right_relations as $relation) {
            $this->relatedTo($relation->inverse_alias, [
                'className' => 'Objects',
                'through' => 'ObjectRelations',
                'foreignKey' => 'right_id',
                'targetForeignKey' => 'left_id',
                'conditions' => [
                    'ObjectRelations.relation_id' => $relation->id,
                ],
                'sort' => [
                    'ObjectRelations.inv_priority' => 'asc',
                ],
            ]);
        }
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

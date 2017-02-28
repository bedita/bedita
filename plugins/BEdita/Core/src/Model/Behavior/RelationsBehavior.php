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

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

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
            $this->getTable()->belongsToMany(Inflector::camelize($relation->name), [
                'className' => 'Objects',
                'through' => 'ObjectRelations',
                'foreignKey' => 'left_id',
                'targetForeignKey' => 'right_id',
                'conditions' => [
                    'ObjectRelations.relation_id' => $relation->id,
                ],
            ]);
        }

        // Add relations to the right side.
        foreach ($this->objectType->right_relations as $relation) {
            $this->getTable()->belongsToMany(Inflector::camelize($relation->inverse_name), [
                'className' => 'Objects',
                'through' => 'ObjectRelations',
                'foreignKey' => 'right_id',
                'targetForeignKey' => 'left_id',
                'conditions' => [
                    'ObjectRelations.relation_id' => $relation->id,
                ],
            ]);
        }
    }
}

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

namespace BEdita\Core\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Properties Model
 *
 * @property \Cake\ORM\Association\BelongsTo $PropertyTypes
 * @property \Cake\ORM\Association\BelongsTo $ObjectTypes
 *
 * @since 4.0.0
 */
class PropertiesTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->displayField('name');

        $this->addBehavior('Timestamp');

        $this->belongsTo('PropertyTypes', [
            'foreignKey' => 'property_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.PropertyTypes'
        ]);

        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.ObjectTypes'
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->naturalNumber('id')
            ->allowEmpty('id', 'create')

            ->requirePresence('name')
            ->notEmpty('name')

            ->allowEmpty('description')

            ->boolean('enabled')
            ->notEmpty('enabled')

            ->boolean('multiple')
            ->notEmpty('multiple');


        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['name', 'object_type_id']));
        $rules->add($rules->existsIn(['object_type_id'], 'ObjectTypes'));
        $rules->add($rules->existsIn(['property_type_id'], 'PropertyTypes'));

        return $rules;
    }
}

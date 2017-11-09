<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
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
 * Endpoints Model
 *
 * @method \BEdita\Core\Model\Entity\Endpoint get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint findOrCreate($search, callable $callback = null, $options = [])
 *
 * @property \Cake\ORM\Association\BelongsTo $ObjectTypes
 * @property \Cake\ORM\Association\HasMany $EndpointPermissions
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 *
 * @since 4.0.0
 */
class EndpointsTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setDisplayField('name');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ObjectTypes');
        $this->hasMany('EndpointPermissions', [
            'dependent' => true,
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
            ->integer('id')
            ->allowEmpty('id', 'create')

            ->requirePresence('name', 'create')
            ->notEmpty('name')
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])

            ->allowEmpty('description')

            ->boolean('enabled')
            ->notEmpty('enabled');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['name']));
        $rules->add($rules->existsIn(['object_type_id'], 'ObjectTypes'));

        return $rules;
    }
}

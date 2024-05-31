<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
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
 * ObjectProperties Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Properties
 * @property \Cake\ORM\Association\BelongsTo $Objects
 * @method \BEdita\Core\Model\Entity\ObjectProperty get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty findOrCreate($search, callable $callback = null, $options = [])
 */
class ObjectPropertiesTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('object_properties');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Properties', [
            'foreignKey' => 'property_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Properties',
        ]);
        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects',
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->requirePresence('property_value', 'create')
            ->notEmptyString('property_value');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['property_id'], 'Properties'));
        $rules->add($rules->existsIn(['object_id'], 'Objects'));

        return $rules;
    }
}

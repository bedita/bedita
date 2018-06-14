<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use Cake\Database\Schema\TableSchema;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Translations Model
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable|\Cake\ORM\Association\BelongsTo $Objects
 * @property \BEdita\Core\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $CreatedByUsers
 * @property \BEdita\Core\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $ModifiedByUsers
 *
 * @method \BEdita\Core\Model\Entity\Translation get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Translation newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Translation[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Translation|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Translation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Translation[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Translation findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \BEdita\Core\Model\Behavior\UserModifiedBehavior
 */
class TranslationsTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('translations');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.UserModified');

        $this->belongsTo('Objects', [
            'className' => 'Objects',
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CreatedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'created_by',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ModifiedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'modified_by',
            'joinType' => 'INNER',
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
            ->allowEmpty('id', 'create');

        $validator
            ->integer('object_id')
            ->requirePresence('object_id', 'create')
            ->notEmpty('object_id');

        $validator
            ->scalar('lang')
            ->maxLength('lang', 64)
            ->requirePresence('lang', 'create')
            ->notEmpty('lang');

        $validator
            ->scalar('status')
            ->requirePresence('status', 'create')
            ->inList('status', ['on', 'off', 'draft'])
            ->notEmpty('status');

        $validator
            ->isArray('fields')
            ->allowEmpty('fields');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['object_id', 'lang']));
        $rules->add($rules->existsIn(['object_id'], 'Objects'));
        $rules->add($rules->existsIn(['created_by'], 'CreatedByUsers'));
        $rules->add($rules->existsIn(['modified_by'], 'ModifiedByUsers'));

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('fields', 'json');

        return $schema;
    }
}

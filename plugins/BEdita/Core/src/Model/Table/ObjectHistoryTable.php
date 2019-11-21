<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Model\Table;

use BEdita\Core\Exception\BadFilterException;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ObjectHistory Model
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable&\Cake\ORM\Association\BelongsTo $Objects
 * @property \BEdita\Core\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \BEdita\Core\Model\Table\ApplicationsTable&\Cake\ORM\Association\BelongsTo $Applications
 *
 * @method \BEdita\Core\Model\Entity\ObjectHistory get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectHistory newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectHistory[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectHistory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectHistory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectHistory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectHistory[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectHistory findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ObjectHistoryTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('object_history');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Users'
        ]);
        $this->belongsTo('Applications', [
            'foreignKey' => 'application_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Applications'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('user_action')
            ->allowEmptyString('user_action');

        $validator
            ->allowEmptyString('changed');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('changed', 'json');

        return $schema;
    }

    /**
     * Find history event data of a single object.
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Additional options. The first element containing object `id`.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException When missing required parameters.
     */
    protected function findHistory(Query $query, array $options): Query
    {
        if (empty($options[0]) || (!is_int($options[0]) && !is_string($options[0]))) {
            throw new BadFilterException(__d('bedita', 'Missing or malformed required parameter "{0}"', 'id'));
        }

        return $query->where([$this->aliasField('object_id') => $options[0]])
                ->order([$this->aliasField('created') => 'ASC']);
    }

    /**
     * Find history activity data of a user.
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Additional options. The first element containing user `id`.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException When missing required parameters.
     */
    protected function findActivity(Query $query, array $options): Query
    {
        if (empty($options[0]) || (!is_int($options[0]) && !is_string($options[0]))) {
            throw new BadFilterException(__d('bedita', 'Missing or malformed required parameter "{0}"', 'id'));
        }

        return $query->where([$this->aliasField('user_id') => $options[0]])
                ->order([$this->aliasField('created') => 'ASC']);
    }
}

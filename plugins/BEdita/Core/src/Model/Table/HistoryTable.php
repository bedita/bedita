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

use Cake\Database\Schema\TableSchemaInterface;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * History Model
 *
 * @property \BEdita\Core\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \BEdita\Core\Model\Table\ApplicationsTable&\Cake\ORM\Association\BelongsTo $Applications
 *
 * @method \BEdita\Core\Model\Entity\History get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\History newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\History[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\History|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\History saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\History patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\History[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\History findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class HistoryTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('history');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

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
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('user_action')
            ->allowEmptyString('user_action');

        $validator
            ->requirePresence('resource_id')
            ->notEmptyString('resource_id');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        $schema->setColumnType('changed', 'json');

        return $schema;
    }
}

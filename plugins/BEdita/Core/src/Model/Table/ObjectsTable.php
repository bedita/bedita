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

use Cake\Database\Schema\Table as Schema;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Objects Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ObjectTypes
 * @property \Cake\ORM\Association\BelongsTo $CreatedByUser
 * @property \Cake\ORM\Association\BelongsTo $ModifiedByUser
 *
 * @since 4.0.0
 */
class ObjectsTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('objects');
        $this->primaryKey('id');
        $this->displayField('title');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.ObjectTypes'
        ]);

        $this->belongsTo('CreatedByUser', [
            'foreignKey' => 'created_by',
            'className' => 'BEdita/Core.Users'
        ]);

        $this->belongsTo('ModifiedByUser', [
            'foreignKey' => 'modified_by',
            'className' => 'BEdita/Core.Users'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->naturalNumber('id')
            ->allowEmpty('id', 'create')

            ->requirePresence('status', 'create')
            ->notEmpty('status')

            ->requirePresence('uname', 'create')
            ->notEmpty('uname')
            ->add('uname', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])

            ->boolean('locked')
            ->notEmpty('locked')

            ->dateTime('published')
            ->allowEmpty('published')

            ->allowEmpty('title')

            ->allowEmpty('description')

            ->allowEmpty('body')

            ->allowEmpty('extra')

            ->requirePresence('lang', 'create')
            ->notEmpty('lang')

            ->naturalNumber('created_by')
            ->requirePresence('created_by', 'create')
            ->notEmpty('created_by')

            ->naturalNumber('modified_by')
            ->requirePresence('modified_by')
            ->notEmpty('modified_by')

            ->dateTime('publish_start')
            ->allowEmpty('publish_start')

            ->dateTime('publish_end')
            ->allowEmpty('publish_end');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['uname']));
        $rules->add($rules->existsIn(['object_type_id'], 'ObjectTypes'));
        return $rules;
    }

    /**
     * {@inheritDoc}
     */
    protected function _initializeSchema(Schema $schema)
    {
        $schema->columnType('extra', 'json');
        return $schema;
    }

    /**
     * Find by object type.
     *
     * You can pass a list of allowed object types to this finder:
     *
     * ```
     * $table->find('type', [1, 'document', 'profiles', 1004]);
     * ```
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable object types.
     * @return \Cake\ORM\Query
     */
    public function findType(Query $query, array $options)
    {
        $ObjectTypes = TableRegistry::get('BEdita/Core.ObjectTypes');
        foreach ($options as &$type) {
            $type = $ObjectTypes->get($type)->id;
        }
        unset($type);

        $query->where([$this->alias() . '.object_type_id IN' => $options]);

        return $query;
    }
}

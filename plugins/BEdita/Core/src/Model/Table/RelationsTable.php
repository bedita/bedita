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

use BEdita\Core\ORM\Rule\IsUniqueAmongst;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Relations Model
 *
 * @property \Cake\ORM\Association\HasMany $ObjectRelations
 * @property \Cake\ORM\Association\HasMany $RelationTypes
 *
 * @method \BEdita\Core\Model\Entity\Relation get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Relation newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Relation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Relation findOrCreate($search, callable $callback = null, $options = [])
 */
class RelationsTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('relations');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('ObjectRelations');
        $this->belongsToMany('LeftObjectTypes', [
            'className' => 'ObjectTypes',
            'through' => 'RelationTypes',
            'foreignKey' => 'relation_id',
            'targetForeignKey' => 'object_type_id',
            'conditions' => [
                'RelationTypes.side' => 'left',
            ],
        ]);
        $this->belongsToMany('RightObjectTypes', [
            'className' => 'ObjectTypes',
            'through' => 'RelationTypes',
            'foreignKey' => 'relation_id',
            'targetForeignKey' => 'object_type_id',
            'conditions' => [
                'RelationTypes.side' => 'right',
            ],
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
            ->requirePresence('name', 'create')
            ->notEmpty('name')
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('label', 'create')
            ->notEmpty('label');

        $validator
            ->requirePresence('inverse_name', 'create')
            ->notEmpty('inverse_name')
            ->add('inverse_name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('inverse_label', 'create')
            ->notEmpty('inverse_label');

        $validator
            ->allowEmpty('description');

        $validator
            ->allowEmpty('params');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules
            ->add(new IsUniqueAmongst(['name' => ['name', 'inverse_name']]), '_isUniqueAmongst', [
                'errorField' => 'name',
                'message' => __d('cake', 'This value is already in use'),
            ])
            ->add(new IsUniqueAmongst(['inverse_name' => ['name', 'inverse_name']]), '_isUniqueAmongst', [
                'errorField' => 'inverse_name',
                'message' => __d('cake', 'This value is already in use'),
            ]);

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->columnType('params', 'json');

        return $schema;
    }

    /**
     * Find a relation by its name or inverse name.
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Additional options. The `name` key is required.
     * @return \Cake\ORM\Query
     */
    protected function findByName(Query $query, array $options = [])
    {
        if (empty($options['name'])) {
            throw new \LogicException(__d('bedita', 'Missing required parameter "{0}"', 'name'));
        }
        $name = $options['name'];

        return $query->where(function (QueryExpression $exp) use ($name) {
            return $exp->or_(function (QueryExpression $exp) use ($name) {
                return $exp
                    ->eq($this->aliasField('name'), $name)
                    ->eq($this->aliasField('inverse_name'), $name);
            });
        });
    }
}

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

use BEdita\Core\Exception\BadFilterException;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Translations Model
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable|\Cake\ORM\Association\BelongsTo $Objects
 * @property \BEdita\Core\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $CreatedByUsers
 * @property \BEdita\Core\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $ModifiedByUsers
 * @method \BEdita\Core\Model\Entity\Translation get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Translation newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Translation[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Translation|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Translation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Translation[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Translation findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \BEdita\Core\Model\Behavior\UserModifiedBehavior
 * @mixin \BEdita\Core\Model\Behavior\StatusBehavior
 */
class TranslationsTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('translations');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.UserModified');
        $this->addBehavior('BEdita/Core.Searchable', [
            'fields' => [
                'translated_fields' => 10,
            ],
            'columnTypes' => [
                'json',
                'text',
            ],
        ]);
        $this->addBehavior('BEdita/Core.Status');

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
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('object_id')
            ->requirePresence('object_id', 'create')
            ->notEmptyString('object_id');

        $validator
            ->add('lang', 'scalar', ['rule' => 'isScalar', 'last' => true])
            ->maxLength('lang', 64)
            ->requirePresence('lang', 'create')
            ->notEmptyString('lang');

        $validator
            ->add('status', 'scalar', ['rule' => 'isScalar', 'last' => true])
            ->inList('status', ['on', 'off', 'draft'])
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        $validator
            ->isArray('translated_fields')
            ->allowEmptyArray('translated_fields');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
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
    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        $schema->setColumnType('translated_fields', 'json');

        return $schema;
    }

    /**
     * Find translations by object type
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options array.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function findType(Query $query, array $options): Query
    {
        if (empty($options)) {
            throw new BadFilterException(__d('bedita', 'Missing required parameter "type"'));
        }
        $typeIds = array_map([$this, 'typeId'], $options);

        return $query->innerJoinWith('Objects', function (Query $query) use ($typeIds) {
            return $query->where(function (QueryExpression $exp) use ($typeIds) {
                return $exp->in('object_type_id', $typeIds);
            });
        });
    }

    /**
     * Retrieve object type ID from options string
     *
     * @param string $option Finder option
     * @return int
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function typeId(string $option): int
    {
        try {
            /** @var \BEdita\Core\Model\Entity\ObjectType $objectType */
            $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get($option);
        } catch (RecordNotFoundException $ex) {
            throw new BadFilterException(__d('bedita', 'Invalid type parameter "{0}"', $option));
        }

        return $objectType->id;
    }
}

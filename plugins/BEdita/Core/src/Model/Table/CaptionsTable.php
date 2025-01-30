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
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * Captions Model
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable&\Cake\ORM\Association\BelongsTo $Objects
 * @method \BEdita\Core\Model\Entity\Caption newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\Caption newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Caption[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Caption get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\Caption findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Caption patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Caption[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Caption|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Caption saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Caption[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Caption>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Caption[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Caption> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Caption[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Caption>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Caption[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Caption> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CaptionsTable extends Table
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

        $this->setTable('captions');
        $this->setDisplayField('label');
        $this->setPrimaryKey('id');
        $this->getSchema()->setColumnType('params', 'json');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->naturalNumber('id')
            ->allowEmptyString('id', null, 'create')

            ->inList('status', ['on', 'off', 'draft'])
            ->notEmptyString('status')

            ->allowEmptyString('label')

            ->allowEmptyString('format')

            ->scalar('lang')
            ->maxLength('lang', 64)
            ->allowEmptyString('lang')

            ->allowEmptyString('caption_text')
            ->maxLengthBytes('caption_text', MysqlAdapter::TEXT_MEDIUM)

            ->allowEmptyArray('params');
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['object_id'], 'Objects'));

        return $rules;
    }
}

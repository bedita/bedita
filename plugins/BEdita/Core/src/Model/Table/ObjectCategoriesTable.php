<?php
declare(strict_types=1);

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
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ObjectCategories Model
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable&\Cake\ORM\Association\BelongsTo $Objects
 * @property \BEdita\Core\Model\Table\CategoriesTable&\Cake\ORM\Association\BelongsTo $Categories
 * @method \BEdita\Core\Model\Entity\ObjectCategory get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\ObjectCategory newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\ObjectCategory[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ObjectCategory>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ObjectCategory> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ObjectCategory>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ObjectCategory> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ObjectCategoriesTable extends Table
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

        $this->setTable('object_categories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects',
        ]);
        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Categories',
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
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create')
            ->allowEmptyString('params')

            ->integer('object_id')
            ->requirePresence('object_id', 'create')
            ->notEmptyString('object_id')

            ->integer('category_id')
            ->requirePresence('category_id', 'create')
            ->notEmptyString('category_id');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getSchema(): TableSchemaInterface
    {
        return parent::getSchema()->setColumnType('params', 'json');
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
        return $rules
            ->add($rules->existsIn(['object_id'], 'Objects'), null, ['errorField' => 'object_id'])
            ->add($rules->existsIn(['category_id'], 'Categories'), null, ['errorField' => 'category_id']);
    }
}

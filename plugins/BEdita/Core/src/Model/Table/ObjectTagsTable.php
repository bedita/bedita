<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
 * ObjectTags Model
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable&\Cake\ORM\Association\BelongsTo $Objects
 * @property \BEdita\Core\Model\Table\TagsTable&\Cake\ORM\Association\BelongsTo $Tags
 * @method \BEdita\Core\Model\Entity\ObjectTag get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\ObjectTag newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectTag[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectTag|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectTag saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectTag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectTag[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectTag findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectTag newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\ObjectTag[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ObjectTag>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectTag[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ObjectTag> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectTag[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ObjectTag>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectTag[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ObjectTag> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ObjectTagsTable extends Table
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

        $this->setTable('object_tags');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects',
        ]);
        $this->belongsTo('Tags', [
            'foreignKey' => 'tag_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Tags',
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

            ->integer('object_id')
            ->requirePresence('object_id', 'create')
            ->notEmptyString('object_id')

            ->integer('tag_id')
            ->requirePresence('tag_id', 'create')
            ->notEmptyString('tag_id');
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
            ->add($rules->existsIn(['tag_id'], 'Tags'), null, ['errorField' => 'tag_id']);
    }
}

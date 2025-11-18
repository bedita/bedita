<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Entity\Relation;
use BEdita\Core\Model\Entity\RelationType;
use BEdita\Core\Model\Enum\RelationTypeSide;
use Cake\Cache\Cache;
use Cake\Database\Type\EnumType;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RelationTypes Model
 *
 * @property \Cake\ORM\Table&\Cake\ORM\Association\BelongsTo $Relations
 * @property \Cake\ORM\Table&\Cake\ORM\Association\BelongsTo $ObjectTypes
 * @method \BEdita\Core\Model\Entity\RelationType get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\RelationType newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\RelationType saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\RelationType>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\RelationType> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\RelationType>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\RelationType> deleteManyOrFail(iterable $entities, array $options = [])
 */
class RelationTypesTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('relation_types');
        $this->setDisplayField('relation_id');
        $this->setPrimaryKey(['relation_id', 'object_type_id', 'side']);
        $this->getSchema()
            ->setColumnType('side', EnumType::from(RelationTypeSide::class));

        $this->belongsTo('Relations', [
            'foreignKey' => 'relation_id',
            'joinType' => 'INNER',
            'className' => 'Relations',
        ]);
        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'joinType' => 'INNER',
            'className' => 'ObjectTypes',
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
            ->inList('side', RelationTypeSide::values())
            ->notEmptyString('side')
            ->requirePresence('side', 'create');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['relation_id', 'object_type_id', 'side']));
        $rules->add($rules->existsIn(['relation_id'], 'Relations'));
        $rules->add($rules->existsIn(['object_type_id'], 'ObjectTypes'));

        return $rules;
    }

    /**
     * Invalidate object types cache after updating a relation's object type.
     *
     * @return void
     */
    public function afterSave(): void
    {
        Cache::clear(ObjectTypesTable::CACHE_CONFIG);
    }

    /**
     * Invalidate object types cache after deleting a relation's object type.
     *
     * @return void
     */
    public function afterDelete(): void
    {
        Cache::clear(ObjectTypesTable::CACHE_CONFIG);
    }
}

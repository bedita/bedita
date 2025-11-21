<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Model\Table;

use BEdita\Core\State\CurrentApplication;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\Validation\Validator;

/**
 * App Config Model - used to handle app configuration data in DB
 * Handle `config` resources that have `context` matching 'app'
 * and `application_id` matching current application id.
 *
 * @since 5.0.0
 * @property \Cake\ORM\Table&\Cake\ORM\Association\BelongsTo $Applications
 * @method \BEdita\Core\Model\Entity\AppConfig newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\AppConfig newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AppConfig[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AppConfig get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\AppConfig findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\AppConfig patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AppConfig[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AppConfig|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\AppConfig saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\AppConfig[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\AppConfig>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\AppConfig[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\AppConfig> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\AppConfig[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\AppConfig>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\AppConfig[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\AppConfig> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \BEdita\Core\Model\Behavior\ResourceNameBehavior
 */
class AppConfigTable extends ConfigTable
{
    public const DEFAULT_CONTEXT = 'app';

    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BEdita/Core.ResourceName');
    }

    /**
     * @inheritDoc
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->alphaNumeric('name')

            ->requirePresence('content', 'create')
            ->notEmptyString('content');

        return $validator;
    }

    /**
     * @inheritDoc
     */
    public function findAll(SelectQuery $query): SelectQuery
    {
        return $query->where(function (QueryExpression $exp) {
            return $exp->and([
                $exp->eq($this->aliasField('application_id'), CurrentApplication::getApplicationId()),
                $exp->eq($this->aliasField('context'), static::DEFAULT_CONTEXT),
                $exp->isNotNull($this->aliasField('application_id')),
            ]);
        });
    }

    /**
     * @inheritDoc
     */
    public function newEmptyEntity(): EntityInterface
    {
        $entity = parent::newEmptyEntity();
        $entity->set('context', static::DEFAULT_CONTEXT);
        $entity->set('application_id', CurrentApplication::getApplicationId());

        return $entity;
    }
}

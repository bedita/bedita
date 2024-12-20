<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Validation\Validation;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * Endpoints Model
 *
 * @method \BEdita\Core\Model\Entity\Endpoint get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\Endpoint newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \Cake\ORM\Query queryCache(\Cake\ORM\Query $query, string $key)
 * @property \Cake\ORM\Table&\Cake\ORM\Association\BelongsTo $ObjectTypes
 * @property \Cake\ORM\Table&\Cake\ORM\Association\HasMany $EndpointPermissions
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \BEdita\Core\Model\Entity\Endpoint newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\Endpoint saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Endpoint>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Endpoint> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Endpoint>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Endpoint> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \BEdita\Core\Model\Behavior\QueryCacheBehavior
 * @mixin \BEdita\Core\Model\Behavior\ResourceNameBehavior
 * @since 4.0.0
 */
class EndpointsTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setDisplayField('name');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.QueryCache');

        $this->belongsTo('ObjectTypes');
        $this->hasMany('EndpointPermissions', [
            'dependent' => true,
        ]);
        $this->addBehavior('BEdita/Core.ResourceName');
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
            ->allowEmptyString('id', null, 'create')

            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->regex('name', Validation::RESOURCE_NAME_REGEX)
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])

            ->allowEmptyString('description')

            ->boolean('enabled')
            ->notEmptyString('enabled');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['name']));
        $rules->add($rules->existsIn(['object_type_id'], 'ObjectTypes'));

        return $rules;
    }

    /**
     * Fetch endpoint id from path using cache.
     *
     * @param string $path The path.
     * @return int|null
     * @throws \Cake\Http\Exception\NotFoundException
     */
    public function fetchId(string $path): ?int
    {
        // endpoint name is the first part of URL path
        $path = array_values(array_filter(explode('/', $path)));
        $name = Hash::get($path, '0', '');

        $query = $this->find()
            ->select(['id', 'enabled'])
            ->disableHydration()
            ->where([$this->aliasField('name') => $name]);

        $endpoint = (array)$this->queryCache($query, sprintf('enpoint_%s', $name))
            ->first();

        if (isset($endpoint['enabled']) && $endpoint['enabled'] === false) {
            throw new NotFoundException(__d('bedita', 'Resource not found.'));
        }

        return Hash::get($endpoint, 'id');
    }
}

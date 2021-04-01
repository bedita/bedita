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

use Cake\Http\Exception\NotFoundException;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * Endpoints Model
 *
 * @method \BEdita\Core\Model\Entity\Endpoint get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Endpoint findOrCreate($search, callable $callback = null, $options = [])
 *
 * @property \Cake\ORM\Association\BelongsTo $ObjectTypes
 * @property \Cake\ORM\Association\HasMany $EndpointPermissions
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 *
 * @since 4.0.0
 */
class EndpointsTable extends Table
{
    /**
     * Cache configuration name.
     *
     * @var string
     */
    const CACHE_CONFIG = '_bedita_core_';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setDisplayField('name');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ObjectTypes');
        $this->hasMany('EndpointPermissions', [
            'dependent' => true,
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
            ->allowEmptyString('id', null, 'create')

            ->requirePresence('name', 'create')
            ->notEmptyString('name')
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
    public function buildRules(RulesChecker $rules)
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

        $endpoint = (array)$this->find()
            ->select(['id', 'enabled'])
            ->disableHydration()
            ->where([$this->aliasField('name') => $name])
            ->cache(sprintf('enpoint_%s', $name), self::CACHE_CONFIG)
            ->first();

        if (isset($endpoint['enabled']) && $endpoint['enabled'] === false) {
            throw new NotFoundException(__d('bedita', 'Resource not found.'));
        }

        return Hash::get($endpoint, 'id');
    }
}

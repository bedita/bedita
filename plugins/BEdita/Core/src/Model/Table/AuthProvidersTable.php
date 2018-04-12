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

use Cake\Collection\Collection;
use Cake\Core\App;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AuthProviders Model
 *
 * @property \Cake\ORM\Association\HasMany $ExternalAuth
 *
 * @method \BEdita\Core\Model\Entity\AuthProvider get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\AuthProvider newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\AuthProvider[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AuthProvider|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\AuthProvider patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AuthProvider[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\AuthProvider findOrCreate($search, callable $callback = null, $options = [])
 *
 * @since 4.0.0
 */
class AuthProvidersTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('auth_providers');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        $this->addBehavior('Timestamp');

        $this->hasMany('ExternalAuth', [
            'foreignKey' => 'auth_provider_id',
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
            ->naturalNumber('id')
            ->allowEmpty('id', 'create')

            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])
            ->requirePresence('name', 'create')
            ->notEmpty('name')

            ->url('url')
            ->allowEmpty('url', 'create')

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
        $rules->add($rules->isUnique(['name']));

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('params', 'json');

        return $schema;
    }

    /**
     * Finder to find all enabled providers or by name
     *
     * @param \Cake\ORM\Query $query Query object.
     * @return \Cake\ORM\Query
     */
    protected function findEnabled(Query $query)
    {
        return $query->where([
            $this->aliasField('enabled') => true,
        ]);
    }

    /**
     * Finder to format results for `AuthComponent` configuration.
     *
     * @param \Cake\ORM\Query $query Query object.
     * @return \Cake\ORM\Query
     */
    protected function findAuthenticate(Query $query)
    {
        $query = $query->where([$this->aliasField('enabled') => true]);

        return $query->formatResults(function (ResultSetInterface $results) {
            return $results
                ->filter(function (EntityInterface $entity) {
                    $class = $entity->get('auth_class');
                    $exists = (App::className($class, 'Auth', 'Authenticate') !== false);
                    if (!$exists) {
                        Log::warning(sprintf('Authentication class "%s" not found', $class));
                    }

                    return $exists;
                })
                ->groupBy('auth_class')
                ->map(function (array $entities) {
                    return [
                        'authProviders' => collection($entities)->indexBy('name')->toArray()
                    ];
                });
        });
    }
}

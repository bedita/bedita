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

use BEdita\Core\Exception\ImmutableResourceException;
use BEdita\Core\State\CurrentApplication;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Utility\Security;
use Cake\Utility\Text;
use Cake\Validation\Validator;

/**
 * Applications Model
 *
 * @method \BEdita\Core\Model\Entity\Application get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Application newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Application[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Application|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Application patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Application[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Application findOrCreate($search, callable $callback = null, $options = [])
 * @method \Cake\ORM\Query queryCache(\Cake\ORM\Query $query, string $key)
 *
 * @property \Cake\ORM\Association\HasMany $EndpointPermissions
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 *
 * @since 4.0.0
 */
class ApplicationsTable extends Table
{
    /**
     * Default application id
     *
     * @var int
     */
    const DEFAULT_APPLICATION = 1;

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
        $this->addBehavior('BEdita/Core.Searchable', [
            'fields' => [
                'name' => 10,
                'description' => 5,
            ],
        ]);
        $this->addBehavior('BEdita/Core.QueryCache');

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

            ->notEmptyString('api_key')
            ->add('api_key', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])

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
        $rules->add($rules->isUnique(['api_key']));

        return $rules;
    }

    /**
     * Generate the api key on application creation.
     *
     * If applications is DEFAULT_APPLICATION or current invoking application and `enabled` is `false`
     * raise an ImmutableResourceException
     *
     * @param \Cake\Event\Event $event The event dispatched
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @return void
     * @throws \BEdita\Core\Exception\ImmutableResourceException if entity is not disableable
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        if (
            !$entity->isNew() && $entity->get('enabled') == false &&
            in_array($entity->id, [static::DEFAULT_APPLICATION, CurrentApplication::getApplicationId()])
        ) {
            throw new ImmutableResourceException(__d('bedita', 'Could not disable "Application" {0}', $entity->id));
        }

        if ($entity->isNew() && !$entity->has('api_key')) {
            $entity->set('api_key', static::generateApiKey());
        }
    }

    /**
     * Generate a unique api key
     *
     * @return string
     */
    public static function generateApiKey()
    {
        return Security::hash(Text::uuid(), 'sha1');
    }

    /**
     * Find an active application by its API key.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options array. It requires an `apiKey` key.
     * @return \Cake\ORM\Query
     */
    protected function findApiKey(Query $query, array $options): Query
    {
        if (empty($options['apiKey']) || !is_string($options['apiKey'])) {
            throw new \BadMethodCallException('Required option "apiKey" must be a not empty string');
        }

        $query = $query->where([
            $this->aliasField('api_key') => $options['apiKey'],
            $this->aliasField('enabled') => true,
        ]);

        return $this->queryCache($query, sprintf('app_%s', $options['apiKey']));
    }

    /**
     * Find an active application by ID using cache.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options array. It requires an `apiKey` key.
     * @return \Cake\ORM\Query
     */
    protected function findActive(Query $query, array $options): Query
    {
        if (empty($options['id']) || !is_string($options['id'])) {
            throw new \BadMethodCallException('Required option "id" must be a not empty string');
        }

        $query = $query->where([
            $this->aliasField('id') => $options['id'],
            $this->aliasField('enabled') => true,
        ]);

        return $this->queryCache($query, sprintf('app_id_%s', $options['id']));
    }

    /**
     * Find an active application by client_id and client_secret.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options array. It requires an `apiKey` key.
     * @return \Cake\ORM\Query
     */
    protected function findCredentials(Query $query, array $options): Query
    {
        if (empty($options['client_id'])) {
            throw new \BadMethodCallException('Required option "client_id" must be a not empty string');
        }

        return $query->where([
            $this->aliasField('api_key') => $options['client_id'],
            $this->aliasField('client_secret') . ' IS' => Hash::get($options, 'client_secret'),
        ]);
    }

    /**
     * Before delete checks: if applications is DEFAULT_APPLICATION or current raise a ImmutableResourceException
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity the entity that is going to be saved
     * @return void
     * @throws \BEdita\Core\Exception\ImmutableResourceException if entity is not deletable
     */
    public function beforeDelete(Event $event, EntityInterface $entity)
    {
        if (in_array($entity->id, [static::DEFAULT_APPLICATION, CurrentApplication::getApplicationId()])) {
            throw new ImmutableResourceException(__d('bedita', 'Could not delete "Application" {0}', $entity->id));
        }
    }
}

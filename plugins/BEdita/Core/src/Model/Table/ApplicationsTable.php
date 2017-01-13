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

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Security;
use Cake\Utility\Text;
use Cake\Validation\Validator;

/**
 * Applications Model
 *
 * @property \Cake\ORM\Association\HasMany $EndpointPermissions
 *
 * @since 4.0.0
 */
class ApplicationsTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->displayField('name');
        $this->addBehavior('Timestamp');
        $this->hasMany('EndpointPermissions');
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
            ->allowEmpty('id', 'create')

            ->notEmpty('api_key')
            ->add('api_key', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])

            ->requirePresence('name', 'create')
            ->notEmpty('name')
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])

            ->allowEmpty('description')

            ->boolean('enabled')
            ->notEmpty('enabled');

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
     * Generate the api key on application creation
     *
     * @param \Cake\Event\Event $event The event dispatched
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @param \ArrayObject $options The save options
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        if (!$entity->isNew() || $entity->has('api_key')) {
            return;
        }

        $entity->set('api_key', $this->generateApiKey());
    }

    /**
     * Generate a unique api key
     *
     * @return string
     */
    public function generateApiKey()
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
    public function findApiKey(Query $query, array $options)
    {
        return $query
            ->where([
                $this->aliasField('api_key') => $options['apiKey'],
                $this->aliasField('enabled') => true,
            ]);
    }
}

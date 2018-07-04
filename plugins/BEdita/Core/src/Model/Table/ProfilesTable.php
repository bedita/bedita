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

use BEdita\Core\Model\Validation\ProfilesValidator;
use BEdita\Core\ORM\Inheritance\Table;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use Cake\Utility\Hash;

/**
 * Profiles Model
 *
 * @method \BEdita\Core\Model\Entity\Profile get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Profile newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Profile[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Profile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Profile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Profile[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Profile findOrCreate($search, callable $callback = null, $options = [])
 *
 * @since 4.0.0
 */
class ProfilesTable extends Table
{

    /**
     * {@inheritDoc}
     */
    protected $_validatorClass = ProfilesValidator::class;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('profiles');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        $this->addBehavior('BEdita/Core.Relations');

        $this->addBehavior('BEdita/Core.CustomProperties');

        $this->addBehavior('BEdita/Core.DataCleanup');

        $this->extensionOf('Objects');

        $this->addBehavior('BEdita/Core.UniqueName', [
            'sourceField' => 'title',
            'prefix' => 'profile-'
        ]);

        $this->addBehavior('BEdita/Core.Searchable', [
            'fields' => [
                'title' => 10,
                'description' => 7,
                'body' => 5,
                'name' => 10,
                'surname' => 10,
                'email' => 7,
                'company_name' => 10,
                'street_address' => 1,
                'city' => 2,
                'country' => 2,
                'state_name' => 2,
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }

    /**
     * Before save actions:
     *  - if `email` is empty set it to NULL to avoid unique constraint errors
     *  - on empty `title` use `name` `surname` as default
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity the entity that is going to be saved
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        if (empty($entity->get('email'))) {
            $entity->set('email', null);
        }
        if (empty($entity->get('title')) && (!empty($entity->get('name')) || !empty($entity->get('surname')))) {
            $title = sprintf('%s %s', (string)Hash::get($entity, 'name', ''), (string)Hash::get($entity, 'surname', ''));
            $entity->set('title', trim($title));
        }
    }
}

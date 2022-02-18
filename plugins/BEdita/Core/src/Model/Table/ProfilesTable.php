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

use BEdita\Core\Model\Table\ObjectsBaseTable as Table;
use BEdita\Core\Model\Validation\ProfilesValidator;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
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
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('profiles');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        $this->extensionOf('Objects');

        $this->getBehavior('UniqueName')->setConfig([
            'sourceField' => 'title',
            'prefix' => 'profile-'
        ]);

        $this->getBehavior('Searchable')->setConfig([
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
                'pseudonym' => 10,
            ],
        ]);
    }

    /**
     * Before save actions:
     *  - if `email` is empty set it to NULL to avoid unique constraint errors
     *  - if `title` was not modified and one of `name`, `surname` or `company_name` was
     *      update `title` accordingly
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

        if (
            $entity->isDirty('title') ||
            (!$entity->isDirty('name') && !$entity->isDirty('surname') && !$entity->isDirty('company_name'))
        ) {
            return;
        }
        $entity->set('title', $this->titleValue($entity));
    }

    /**
     * Create profile title from `name` and `surname` or `company_name`
     *
     * @param EntityInterface $entity Saved entity
     * @return string
     */
    protected function titleValue(EntityInterface $entity): string
    {
        $title = trim(sprintf(
            '%s %s',
            (string)Hash::get($entity, 'name'),
            (string)Hash::get($entity, 'surname')
        ));
        if (empty($title) && !empty($entity->get('company_name'))) {
            $title = (string)$entity->get('company_name');
        }

        return $title;
    }
}

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

use Cake\Cache\Cache;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ObjectTypes Model
 *
 * @property \Cake\ORM\Association\HasMany $Objects
 * @property \Cake\ORM\Association\HasMany $Properties
 * @property \Cake\ORM\Association\HasMany $RelationTypes
 */
class ObjectTypesTable extends Table
{
    /**
     * Cache config name for object types.
     *
     * @var string
     */
    const CACHE_CONFIG = '_bedita_object_types_';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('object_types');
        $this->primaryKey('id');
        $this->displayField('name');

        $this->hasMany('Objects', [
            'foreignKey' => 'object_type_id',
            'className' => 'BEdita/Core.Objects',
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
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name')
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->allowEmpty('pluralized')
            ->add('pluralized', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->allowEmpty('description');

        $validator
            ->requirePresence('plugin', 'create')
            ->notEmpty('plugin');

        $validator
            ->requirePresence('model', 'create')
            ->notEmpty('model');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules
            ->add($rules->isUnique(['name']))
            ->add($rules->isUnique(['pluralized']));

        return $rules;
    }

    /**
     * {@inheritDoc}
     */
    public function get($primaryKey, $options = [])
    {
        if (is_string($primaryKey) && !is_numeric($primaryKey)) {
            $allTypes = array_flip(
                $this->find('list')
                    ->cache('map', self::CACHE_CONFIG)
                    ->toArray()
            );
            $allTypes += array_flip(
                $this->find('list', ['valueField' => 'pluralized'])
                    ->cache('map_pluralized', self::CACHE_CONFIG)
                    ->toArray()
            );

            if (!isset($allTypes[$primaryKey])) {
                throw new RecordNotFoundException(sprintf(
                    'Record not found in table "%s"',
                    $this->table()
                ));
            }

            $primaryKey = $allTypes[$primaryKey];
        }

        $options += [
            'key' => 'id_' . $primaryKey,
            'cache' => self::CACHE_CONFIG,
        ];

        return parent::get($primaryKey, $options);
    }

    /**
     * Invalidate cache after saving an object type.
     *
     * @param \Cake\Event\Event $event Triggered event.
     * @param \Cake\Datasource\EntityInterface $entity Subject entity.
     * @param \ArrayObject $options Additional options.
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        Cache::delete('id_' . $entity->id, self::CACHE_CONFIG);
        if ($entity->dirty('name')) {
            Cache::delete('map', self::CACHE_CONFIG);
        }
        if ($entity->dirty('pluralized')) {
            Cache::delete('map_pluralized', self::CACHE_CONFIG);
        }
    }

    /**
     * Invalidate cache after deleting an object type.
     *
     * @param \Cake\Event\Event $event Triggered event.
     * @param \Cake\Datasource\EntityInterface $entity Subject entity.
     * @param \ArrayObject $options Additional options.
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        Cache::delete('id_' . $entity->id, self::CACHE_CONFIG);
        Cache::delete('map', self::CACHE_CONFIG);
        Cache::delete('map_pluralized', self::CACHE_CONFIG);
    }
}

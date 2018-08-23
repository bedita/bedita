<?php
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

use BEdita\Core\Model\Validation\Validation;
use Cake\Cache\Cache;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Network\Exception\ForbiddenException;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Property Types - available property types
 *
 * @method \BEdita\Core\Model\Entity\PropertyType newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\PropertyType[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\PropertyType|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\PropertyType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\PropertyType[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\PropertyType findOrCreate($search, callable $callback = null, $options = [])
 *
 * @property \Cake\ORM\Association\HasMany $Properties
 *
 * @since 4.0.0
 */
class PropertyTypesTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('property_types');
        $this->setPrimaryKey('id');

        $this->hasMany('Properties');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->setProvider('bedita', Validation::class)

            ->requirePresence('name', 'create')
            ->notEmpty('name')
            ->alphaNumeric('name')

            ->allowEmpty('params')
            ->add('params', 'valid', [
                'rule' => ['jsonSchema', 'http://json-schema.org/draft-06/schema#'],
                'provider' => 'bedita',
            ]);

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('params', 'jsonobject');

        return $schema;
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
     * Invalidate object types cache after updating a property type.
     *
     * @return void
     */
    public function afterSave()
    {
        Cache::clear(false, ObjectTypesTable::CACHE_CONFIG);
    }

    /**
     * Check that no properties exist linked to the property type before deleting it.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity being deleted.
     * @return void
     * @throws \Cake\Network\Exception\ForbiddenException Throws an exception if one or more properties exist
     *      with the property type being deleted.
     */
    public function beforeDelete(Event $event, EntityInterface $entity)
    {
        if ($this->Properties->exists([$this->Properties->getForeignKey() => $entity->get($this->Properties->getBindingKey())])) {
            throw new ForbiddenException(__d('bedita', 'Property type with existing properties'));
        }
    }

    /**
     * Invalidate object types cache after deleting a property type.
     *
     * @return void
     */
    public function afterDelete()
    {
        Cache::clear(false, ObjectTypesTable::CACHE_CONFIG);
    }

    /**
     * Detect most appropriate property type for a column.
     *
     * @param string $name Column name.
     * @param \Cake\ORM\Table $table Table object.
     * @return \BEdita\Core\Model\Entity\PropertyType
     */
    public function detect($name, Table $table)
    {
        /* @var \BEdita\Core\Model\Entity\PropertyType[] $propertyTypes */
        $propertyTypes = Cache::remember(
            'property_types',
            function () {
                return $this->find()
                    ->indexBy('name')
                    ->toArray();
            },
            ObjectTypesTable::CACHE_CONFIG
        );

        // Check if there is a property type whose name matches column name.
        if (isset($propertyTypes[$name])) {
            return $propertyTypes[$name];
        }

        // Check if there is a property type whose name matches an applicable validation rule's name.
        $rules = array_keys($table->getValidator()->field($name)->rules());
        foreach ($rules as $ruleName) {
            if (isset($propertyTypes[$ruleName])) {
                return $propertyTypes[$ruleName];
            }
        }

        // Try to infer a generic type from column definition.
        $type = $table->getSchema()->getColumnType($name);
        if (isset($propertyTypes[$type])) {
            return $propertyTypes[$type];
        }
        switch ($type) {
            // Try to convert specific types to more generic ones.
            case 'float':
                $type = 'number';
                break;
            case 'timestamp':
                $type = 'datetime';
                break;
        }
        if (isset($propertyTypes[$type])) {
            return $propertyTypes[$type];
        }

        // Fallback on string type.
        return $propertyTypes['string'];
    }
}

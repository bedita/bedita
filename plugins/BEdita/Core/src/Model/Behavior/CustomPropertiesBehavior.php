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

namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Model\Validation\Validation;
use Cake\Collection\CollectionInterface;
use Cake\Database\Driver\Mysql;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * CustomProperties behavior
 *
 * @since 4.0.0
 */
class CustomPropertiesBehavior extends Behavior
{
    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'field' => 'custom_props',
        'filter' => [
            'number' => FILTER_VALIDATE_FLOAT,
            'integer' => FILTER_VALIDATE_INT,
            'boolean' => FILTER_VALIDATE_BOOLEAN,
        ],
        'implementedFinders' => [
            'customProp' => 'findCustomProp',
        ],
        'implementedMethods' => [
            'getCustomPropsAvailable' => 'getAvailable',
            'getCustomPropsDefaultValues' => 'getDefaultValues',
        ],
    ];

    /**
     * The custom properties available.
     * It is an array with properties name as key and Property entity as value
     *
     * @var array
     */
    protected $available = null;

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $table = $this->getTable();
        if (!$table->hasBehavior('ObjectType')) {
            $table->addBehavior('BEdita/Core.ObjectType');
        }
    }

    /**
     * Getter for object type.
     *
     * @param array $args Method arguments.
     * @return \BEdita\Core\Model\Entity\ObjectType
     */
    protected function objectType(...$args)
    {
        return $this->getTable()->behaviors()->call('objectType', $args);
    }

    /**
     * Get available properties for object type
     *
     * @return \BEdita\Core\Model\Entity\Property[]
     */
    public function getAvailable()
    {
        if ($this->available !== null) {
            return $this->available;
        }

        $objectType = $this->objectType();
        if ($objectType === null) {
            return [];
        }

        $this->available = TableRegistry::getTableLocator()->get('Properties')
            ->find('type', ['dynamic'])
            ->find('objectType', [$objectType->id])
            ->where(['enabled' => true, 'is_static' => false])
            ->indexBy('name')
            ->toArray();

        return $this->available;
    }

    /**
     * Return the default values of available properties
     *
     * @return array
     */
    public function getDefaultValues()
    {
        return array_fill_keys(array_keys($this->getAvailable()), null);
    }

    /**
     * Set custom properties keys as main properties
     *
     * @param \Cake\Event\Event $event Fired event.
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    public function beforeFind(Event $event, Query $query): Query
    {
        return $query->formatResults(
            function (CollectionInterface $results) {
                return $results->map(function ($row) {
                    return $this->promoteProperties($row);
                });
            },
            Query::PREPEND
        );
    }

    /**
     * Set custom properties in their dedicated field.
     *
     * @param \Cake\Event\Event $event Fired event.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @return false|void
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        $this->demoteProperties($entity);
        if ($entity->hasErrors()) {
            return false;
        }
    }

    /**
     * Promote the properties in configured `field` to first-class citizen properties.
     * Missing properties in `$entity` but available will be filled with default values.
     *
     * @param \Cake\Datasource\EntityInterface|array $entity The entity or the array to work on
     * @return \Cake\Datasource\EntityInterface|array
     */
    protected function promoteProperties($entity)
    {
        $field = $this->getConfig('field');
        if ((!is_array($entity) && !($entity instanceof EntityInterface)) || !$this->isFieldSet($entity, $field)) {
            return $entity;
        }

        if (empty($entity[$field]) || !is_array($entity[$field])) {
            $entity[$field] = [];
        }
        $entity[$field] = $entity[$field] + $this->getDefaultValues();

        $customProps = $entity[$field] ?? [];
        if ($entity instanceof EntityInterface) {
            $entity->setHidden([$field], true);
        } else {
            unset($entity[$field]);
        }

        if (is_array($entity)) {
            return array_merge($entity, $customProps);
        }

        $entity->set($customProps, ['guard' => false])->clean();

        return $entity;
    }

    /**
     * Send custom properties back to where they came from.
     * Value formatting and JSON Schema validation is performed.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved.
     * @return void
     */
    protected function demoteProperties(EntityInterface $entity): void
    {
        $field = $this->getConfig('field');
        $value = (array)$entity->get($field);

        $dirty = false;
        $available = $this->getAvailable();
        foreach ($available as $property) {
            /** @var \BEdita\Core\Model\Entity\Property $property */
            $name = $property->name;
            if (
                (!$this->isFieldSet($entity, $name) || !$entity->isDirty($name)) &&
                !($entity->isNew() && !$property->is_nullable)
            ) {
                continue;
            }

            $dirty = true;
            $schema = (array)$property->property_type->params;
            $propValue = $this->formatValue($entity->get($name), $schema);
            $result = Validation::jsonSchema($propValue, $schema);
            if (($propValue !== null || !$property->is_nullable) && is_string($result)) {
                $entity->setError($name, $result);
            }
            $value[$name] = $propValue;
        }

        if ($dirty) {
            $entity->set($field, $value);
        }
    }

    /**
     * Format property value to be saved.
     * A simple formatting is performed, only for few basic types
     *
     * @param mixed $value Custom property value
     * @param array $schema Property JSON Schema
     * @return mixed
     */
    protected function formatValue($value, array $schema)
    {
        if ($value === null) {
            return null;
        }
        $type = (string)Hash::get($schema, 'type');
        // apply `filter_var` to some primitive types
        $filter = $this->getConfig(sprintf('filter.%s', $type));
        if ($filter) {
            return filter_var($value, $filter, FILTER_NULL_ON_FAILURE);
        }
        // set `null` on empty strings in other cases
        if (in_array($type, ['array', 'object']) && $value === '') {
            return null;
        }
        $format = (string)Hash::get($schema, 'format');
        if ($type === 'string' && (in_array($format, ['email', 'uri', 'date', 'date-time']) || !empty(Hash::get($schema, 'enum')))) {
            $value = trim($value);
            if ($value === '') {
                return null;
            }
        }

        return $value;
    }

    /**
     * Check if configured field containing custom properties is set in `$entity`.
     *
     * A field is considered "set" if it is present in `$entity` with any value, including `NULL`.
     *
     * @param \Cake\Datasource\EntityInterface|array $entity The entity or the array to check.
     * @param string $field The field being looked for.
     * @return bool
     */
    protected function isFieldSet($entity, $field): bool
    {
        if ($entity instanceof ObjectEntity) {
            return $entity->hasProperty($field);
        }

        return array_key_exists($field, (array)$entity);
    }

    /**
     * Finder for custom property.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options.
     * @return \Cake\ORM\Query
     * @throws \Cake\Http\Exception\BadRequestException When
     */
    public function findCustomProp(Query $query, array $options): Query
    {
        // for now we handle just MySQL
        if (!($query->getConnection()->getDriver() instanceof Mysql)) {
            throw new BadFilterException(__d('bedita', 'customProp finder isn\'t supported for datasource'));
        }

        $available = $this->getAvailable();
        $options = array_intersect_key($options, $available);
        if (empty($options)) {
            // Bad filter options.
            throw new BadFilterException(__d('bedita', 'Invalid data'));
        }

        foreach ($options as $key => &$value) {
            /** @var \BEdita\Core\Model\Entity\Property $property */
            $property = Hash::get($available, $key);
            $value = $this->formatValue($value, $property->property_type->params);
        }
        unset($value);

        return $query->where(function (QueryExpression $exp, Query $query) use ($options) {
            $field = $this->getTable()->aliasField($this->getConfig('field'));

            return $exp->and(array_map(
                function ($key, $value) use ($field, $query) {
                    return $query->newExpr()->eq(
                        new FunctionExpression(
                            'JSON_UNQUOTE',
                            [
                                new FunctionExpression(
                                    'JSON_EXTRACT',
                                    [$field => 'identifier', sprintf('$.%s', $key)]
                                ),
                            ]
                        ),
                        new FunctionExpression('JSON_UNQUOTE', [json_encode($value)]) // trick to normalize values compared
                    );
                },
                array_keys($options),
                $options
            ));
        });
    }
}

<?php
declare(strict_types=1);

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
use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\Model\Validation\Validation;
use BEdita\Core\ORM\QueryFilterTrait;
use Cake\Collection\CollectionInterface;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * CustomProperties behavior
 *
 * @since 4.0.0
 */
class CustomPropertiesBehavior extends Behavior
{
    use QueryFilterTrait;

    /**
     * @inheritDoc
     */
    protected array $_defaultConfig = [
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
     * @var array|null
     */
    protected ?array $available = null;

    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $table = $this->table();
        if (!$table->hasBehavior('ObjectType')) {
            $table->addBehavior('BEdita/Core.ObjectType');
        }
    }

    /**
     * Getter for object type.
     *
     * @param array $args Method arguments.
     * @return \BEdita\Core\Model\Entity\ObjectType|null
     */
    protected function objectType(array ...$args): ?ObjectType
    {
        return $this->table()->behaviors()->call('objectType', $args);
    }

    /**
     * Get available properties for object type
     *
     * @return array<\BEdita\Core\Model\Entity\Property>
     */
    public function getAvailable(): array
    {
        if ($this->available !== null) {
            return $this->available;
        }

        $objectType = $this->objectType();
        if ($objectType === null) {
            return [];
        }

        $this->available = TableRegistry::getTableLocator()->get('Properties')
            ->find('type', propType: 'dynamic')
            ->find('objectType', for: $objectType->id)
            ->where(['enabled' => true, 'is_static' => false])
            ->all()
            ->indexBy('name')
            ->toArray();

        return $this->available;
    }

    /**
     * Return the default values of available properties
     *
     * @return array
     */
    public function getDefaultValues(): array
    {
        return array_fill_keys(array_keys($this->getAvailable()), null);
    }

    /**
     * Set custom properties keys as main properties
     *
     * @param \Cake\Event\EventInterface $event Fired event.
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @return void
     */
    public function beforeFind(EventInterface $event, SelectQuery $query): void
    {
        $event->setResult(
            $query->formatResults(
                function (CollectionInterface $results) {
                    return $results->map(function ($row) {
                        if (!is_array($row) && !$row instanceof EntityInterface) {
                            return $row;
                        }

                        return $this->promoteProperties($row);
                    });
                },
                SelectQuery::PREPEND,
            ),
        );
    }

    /**
     * Set custom properties in their dedicated field.
     *
     * @param \Cake\Event\EventInterface $event Fired event.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity): void
    {
        $this->demoteProperties($entity);
        if ($entity->hasErrors()) {
            $event->setResult(false);
        }
    }

    /**
     * Promote the properties in configured `field` to first-class citizen properties.
     * Missing properties in `$entity` but available will be filled with default values.
     *
     * @param \Cake\Datasource\EntityInterface|array $entity The entity or the array to work on
     * @return \Cake\Datasource\EntityInterface|array
     */
    protected function promoteProperties(EntityInterface|array $entity): EntityInterface|array
    {
        $field = $this->getConfig('field');
        if ((!is_array($entity) && !($entity instanceof EntityInterface)) || !$this->isFieldSet($entity, $field)) {
            return $entity;
        }

        if (empty($entity[$field]) || !is_array($entity[$field])) {
            $entity[$field] = [];
        }
        $entity[$field] = $entity[$field] + $this->getDefaultValues();

        $customProps = (array)$entity[$field] ?? [];
        if ($entity instanceof EntityInterface) {
            $entity->setHidden([$field], true);
        } else {
            unset($entity[$field]);
        }

        return $this->setupCustomProps($entity, $customProps);
    }

    /**
     * Setup custom properties from array input.
     *
     * @param \Cake\Datasource\EntityInterface|array $entity The entity or the array to work on
     * @param array $customProps Custom properties array
     * @return \Cake\Datasource\EntityInterface|array
     */
    protected function setupCustomProps(EntityInterface|array $entity, array $customProps): EntityInterface|array
    {
        if (is_array($entity)) {
            return array_merge($entity, $customProps);
        }

        /** @var \Cake\ORM\Entity $entity */
        $entity->patch($customProps, ['guard' => false])->clean();
        $readOnlyProps = array_filter(Hash::combine((array)$this->available, '{s}.name', '{s}.read_only'));
        $entity->setAccess(array_keys($readOnlyProps), false);

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
    protected function formatValue(mixed $value, array $schema): mixed
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
    protected function isFieldSet(EntityInterface|array $entity, string $field): bool
    {
        if ($entity instanceof ObjectEntity) {
            return $entity->hasProperty($field);
        }

        return array_key_exists($field, (array)$entity);
    }

    /**
     * Finder for custom property.
     *
     * The following are equivalent:
     * ```
     * $table->find('customProp', value: ['prop_name' => 'prop_value']);
     * $table->find('customProp', ...['prop_name' => 'prop_value']);
     * $table->find('customProp', prop_name: 'prop_value');
     * ```
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param mixed $args Named arguments. If `value` is present it will be used.
     * @return \Cake\ORM\Query\SelectQuery
     * @throws \Cake\Http\Exception\BadRequestException When
     */
    public function findCustomProp(SelectQuery $query, mixed ...$args): SelectQuery
    {
        $driver = $query->getConnection()->getDriver();
        // Check if driver is supported
        if (!($driver instanceof Mysql) && !($driver instanceof Postgres)) {
            throw new BadFilterException(__d('bedita', 'customProp finder isn\'t supported for this datasource'));
        }

        $options = $args['value'] ?? $args;
        $available = $this->getAvailable();
        $options = array_intersect_key($options, $available);
        if (empty($options)) {
            // Bad filter options.
            throw new BadFilterException(__d('bedita', 'Invalid data'));
        }

        $field = $this->table()->aliasField($this->getConfig('field'));
        $conditions = $this->setupConditions($options, $field, $driver);

        return $query->where(function (QueryExpression $exp) use ($conditions, $query) {

            return $exp->and(array_map(function ($key) use ($conditions, $query) {
                $operation = $conditions[$key];
                $operator = key($operation);
                $value = $operation[$operator];

                return $this->operatorExpression($query->newExpr(), $operator, $key, $value);
            }, array_keys($conditions)));
        });
    }

    /**
     * Setup conditions for custom properties filtering.
     * An array of conditions is returned with this structure:
     *
     * ```
     * [
     *     <expressionProperty1> => [<operator> => <expressionValue>],
     *     <expressionProperty2> => [<operator> => <expressionValue>],
     * ]
     * ...
     *
     * Where `<expressionPropertyN>` and `<expressionValueN>` are database driver specific expressions.
     *
     * @param array $options Filter options.
     * @param string $field Field name.
     * @param object $driver Database driver.
     * @return array
     */
    protected function setupConditions(array $options, string $field, object $driver): array
    {
        $conditions = [];
        $available = $this->getAvailable();
        foreach ($options as $prop => $value) {
            $key = $this->expressionField($field, $prop, $driver);
            /** @var \BEdita\Core\Model\Entity\Property $property */
            $property = Hash::get($available, $prop);
            $schema = [];
            if ($property && $property->property_type) {
                $schema = (array)$property->property_type->params;
            }
            if ($value === null) {
                $conditions[$key] = ['null' => null];
                continue;
            }
            $in = [];

            if (!is_array($value)) {
                if (is_string($value) && strpos($value, ',') !== false) {
                    $value = explode(',', $value);
                } else {
                    $conditions[$key] = ['eq' => $this->expressionValue($value, $schema, $driver)];
                    continue;
                }
            }
            foreach ($value as $operator => $v) {
                if (is_numeric($operator)) {
                    $in[] = $this->expressionValue($v, $schema, $driver);
                    continue;
                }

                if ($operator === 'in' || $operator === 'notin' || $operator === 'nin') {
                    $v = is_array($v) ? $v : [$v];
                    $expValue = array_map(fn($i) => $this->expressionValue($i, $schema, $driver), $v);
                } else {
                    $expValue = $this->expressionValue($v, $schema, $driver);
                }
                $conditions[$key] = [$operator => $expValue];
            }

            if (!empty($in)) {
                $conditions[$key] = ['in' => $in];
            }
        }

        return $conditions;
    }

    /**
     * Get expression for a property field.
     *
     * @param string $field Field name.
     * @param string $key Property name.
     * @param object $driver Database driver.
     * @return mixed
     */
    protected function expressionField(string $field, string $key, object $driver): mixed
    {
        if ($driver instanceof Mysql) {
            return new FunctionExpression(
                'JSON_UNQUOTE',
                [
                    new FunctionExpression(
                        'JSON_EXTRACT',
                        [$field => 'identifier', sprintf('$.%s', $key)],
                    ),
                ],
            );
        }

        // Postgres field
        return sprintf('%s->>%s', $field, $driver->quote($key));
    }

    /**
     * Get expression value for a property value.
     *
     * @param mixed $value Property value.
     * @param array $schema Property JSON Schema.
     * @param object $driver Database driver.
     * @return mixed
     */
    protected function expressionValue(mixed $value, array $schema, object $driver): mixed
    {
        $value = $this->formatValue($value, $schema);
        if ($driver instanceof Mysql) {
            return new FunctionExpression('JSON_UNQUOTE', [json_encode($value)]);
        }

        return is_string($value) ? $value : json_encode($value);
    }
}

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
namespace BEdita\Core\Model\Table;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Entity\Property;
use BEdita\Core\Model\Entity\StaticProperty;
use BEdita\Core\Model\Validation\Validation;
use BEdita\Core\Search\SimpleSearchTrait;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Query\SelectQuery as DatabaseSelectQuery;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validation as CakeValidation;
use Cake\Validation\Validator;

/**
 * Properties Model
 *
 * @property \BEdita\Core\Model\Table\PropertyTypesTable&\Cake\ORM\Association\BelongsTo $PropertyTypes
 * @property \BEdita\Core\Model\Table\ObjectTypesTable&\Cake\ORM\Association\BelongsTo $ObjectTypes
 * @method \BEdita\Core\Model\Entity\Property get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\Property newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\Property saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Property>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Property> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Property>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Property> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \BEdita\Core\Model\Behavior\SearchableBehavior
 * @since 4.0.0
 */
class PropertiesTable extends Table
{
    use SimpleSearchTrait;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setDisplayField('name');
        $this->getSchema()->setColumnType('id', 'string');

        $this->addBehavior('Timestamp');

        $this->belongsTo('PropertyTypes', [
            'foreignKey' => 'property_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.PropertyTypes',
        ]);

        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.ObjectTypes',
        ]);

        $this->addBehavior('BEdita/Core.Searchable', ['scopes' => (array)$this->getTable()]);

        $this->setupSimpleSearch(['fields' => ['name', 'description']]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->naturalNumber('id')
            ->allowEmptyString('id', null, 'create')

            ->requirePresence('name')
            ->notEmptyString('name')
            ->regex('name', Validation::RESOURCE_NAME_REGEX)

            ->allowEmptyString('description')

            ->boolean('enabled')
            ->notEmptyString('enabled')

            ->boolean('multiple')
            ->notEmptyString('multiple');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['name', 'object_type_id']));
        $rules->add($rules->existsIn(['object_type_id'], 'ObjectTypes'));
        $rules->add($rules->existsIn(['property_type_id'], 'PropertyTypes'));

        return $rules;
    }

    /**
     * Find both static and dynamic properties by default.
     *
     * @param \Cake\Event\EventInterface $event Dispatched event.
     * @param \Cake\ORM\Query\SelectQuery $query Query object.
     * @return void
     */
    public function beforeFind(EventInterface $event, SelectQuery $query): void
    {
        $from = $query->clause('from');
        if (empty($from)) {
            $query->find('type', propType: 'both');
        }
    }

    /**
     * Return properties for an object type, considering inheritance.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param string|int $for Object type name or id.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findObjectType(SelectQuery $query, string|int $for): SelectQuery
    {
        return $query
            ->where(function (QueryExpression $exp) use ($for) {
                return $exp->in(
                    $this->aliasField($this->ObjectTypes->getForeignKey()),
                    $this->ObjectTypes->find('path', for: $for)
                        ->select([$this->ObjectTypes->aliasField($this->ObjectTypes->getBindingKey())]),
                );
            });
    }

    /**
     * Find property resource by name and object type.
     * `$object_type_name` or `$object` must be provided.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param string $name The property name
     * @param string|null $object_type_name The object type name
     * @param string|null $object The object type name (alternative to `object_type_name`)
     * @return \Cake\ORM\Query\SelectQuery
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function findResource(
        SelectQuery $query,
        string $name,
        ?string $object_type_name = null,
        ?string $object = null,
    ): SelectQuery {
        $object = $object_type_name ?? $object;
        if (empty($object)) {
            throw new BadFilterException(__d('bedita', 'Missing required parameter "{0}"', 'object_type_name'));
        }

        return $query->find('objectType', for: $object)
            ->where([$this->aliasField('name') => $name]);
    }

    /**
     * Find properties by their type (either `'static'`, `'dynamic'` or `'both'`).
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param string $propType The property type between `'static'`, `'dynamic'` or `'both'`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findType(SelectQuery $query, string $propType): SelectQuery
    {
        if (!in_array($propType, ['static', 'dynamic', 'both'])) {
            throw new BadFilterException(__d('bedita', 'Invalid options for finder "{0}"', 'type'));
        }

        switch ($propType) {
            case 'static':
                $table = TableRegistry::getTableLocator()->get('StaticProperties')
                    ->setAlias($this->getAlias());
                $from = $table->getTable();
                break;

            case 'dynamic':
                $from = $this->getTable();
                break;

            case 'both':
            default:
                $table = TableRegistry::getTableLocator()->get('StaticProperties')
                    ->setAlias($this->getAlias());

                // Build CTE sub-query.
                $select = array_combine( // Use column name as column alias (`SELECT status AS status, title AS title, ...`).
                    $this->getSchema()->columns(),
                    $this->getSchema()->columns(),
                );
                $select[$this->getPrimaryKey()] = $query->func()->concat([
                    '',
                    $this->getPrimaryKey() => 'identifier',
                ]); // Use implicit type conversion, or PostgreSQL will complain about mixing integers and UUIDs.
                $from = (new DatabaseSelectQuery($this->getConnection()))
                    ->select($select)
                    ->from($this->getTable())
                    ->unionAll(
                        (new DatabaseSelectQuery($this->getConnection()))
                            ->select($select)
                            ->from($table->getTable()),
                    );
        }

        return $query
            ->from([$this->getAlias() => $from], true)
            ->formatResults(function (ResultSetInterface $results) {
                return $results->map(function ($row) {
                    if (!($row instanceof Property) || empty($row->id) || !CakeValidation::uuid($row->id)) {
                        return $row;
                    }

                    return StaticProperty::fromProperty($row);
                });
            });
    }
}

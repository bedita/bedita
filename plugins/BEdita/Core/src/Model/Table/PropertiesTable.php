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

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Entity\Property;
use BEdita\Core\Model\Entity\StaticProperty;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Query as DatabaseQuery;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validation;
use Cake\Validation\Validator;

/**
 * Properties Model
 *
 * @property \Cake\ORM\Association\BelongsTo $PropertyTypes
 * @property \Cake\ORM\Association\BelongsTo $ObjectTypes
 *
 * @method \BEdita\Core\Model\Entity\Property get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Property newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Property patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Property findOrCreate($search, callable $callback = null, $options = [])
 *
 * @since 4.0.0
 */
class PropertiesTable extends Table
{

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

        $this->belongsTo('PropertyTypes', [
            'foreignKey' => 'property_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.PropertyTypes'
        ]);

        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.ObjectTypes',
        ]);

        $this->addBehavior('BEdita/Core.Searchable', [
            'fields' => [
                'name' => 10,
                'description' => 5,
            ],
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

            ->requirePresence('name')
            ->notEmpty('name')

            ->allowEmpty('description')

            ->boolean('enabled')
            ->notEmpty('enabled')

            ->boolean('multiple')
            ->notEmpty('multiple');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['name', 'object_type_id']));
        $rules->add($rules->existsIn(['object_type_id'], 'ObjectTypes'));
        $rules->add($rules->existsIn(['property_type_id'], 'PropertyTypes'));

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        return parent::_initializeSchema($schema)
            ->setColumnType('id', 'string');
    }

    /**
     * Find both static and dynamic properties by default.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\ORM\Query $query Query object.
     * @return void
     */
    public function beforeFind(Event $event, Query $query)
    {
        $from = $query->from();
        if (empty($from)) {
            $query->find('type', ['both']);
        }
    }

    /**
     * Return properties for an object type, considering inheritance.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Filter options.
     * @return \Cake\ORM\Query
     */
    protected function findObjectType(Query $query, array $options = [])
    {
        $options = array_filter($options);
        if (count($options) !== 1) {
            throw new BadFilterException(__d('bedita', 'Missing object type to get properties for'));
        }
        $for = reset($options);

        return $query
            ->where(function (QueryExpression $exp) use ($for) {
                return $exp->in(
                    $this->aliasField($this->ObjectTypes->getForeignKey()),
                    $this->ObjectTypes->find('path', compact('for'))
                        ->select([$this->ObjectTypes->aliasField($this->ObjectTypes->getBindingKey())])
                );
            });
    }

    /**
     * Find properties by their type (either `'static'`, `'dynamic'` or `'both'`).
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     */
    protected function findType(Query $query, array $options)
    {
        if (empty($options[0]) || !in_array($options[0], ['static', 'dynamic', 'both'])) {
            throw new BadFilterException(__d('bedita', 'Invalid options for finder "{0}"', 'type'));
        }

        switch ($options[0]) {
            case 'static':
                $table = TableRegistry::get('StaticProperties')
                    ->setAlias($this->getAlias());
                $from = $table->getTable();
                break;

            case 'dynamic':
                $from = $this->getTable();
                break;

            case 'both':
            default:
                $table = TableRegistry::get('StaticProperties')
                    ->setAlias($this->getAlias());

                // Build CTE sub-query.
                $select = array_combine( // Use column name as column alias (`SELECT status AS status, title AS title, ...`).
                    $this->getSchema()->columns(),
                    $this->getSchema()->columns()
                );
                $select[$this->getPrimaryKey()] = $query->func()->concat([
                    '',
                    $this->getPrimaryKey() => 'identifier',
                ]); // Use implicit type conversion, or PostgreSQL will complain about mixing integers and UUIDs.
                $from = (new DatabaseQuery($this->getConnection()))
                    ->select($select)
                    ->from($this->getTable())
                    ->unionAll(
                        (new DatabaseQuery($this->getConnection()))
                            ->select($select)
                            ->from($table->getTable())
                    );
        }

        return $query
            ->from([$this->getAlias() => $from], true)
            ->formatResults(function (ResultSetInterface $results) {
                return $results->map(function ($row) {
                    if (!($row instanceof Property) || empty($row->id) || !Validation::uuid($row->id)) {
                        return $row;
                    }

                    return StaticProperty::fromProperty($row);
                });
            });
    }
}

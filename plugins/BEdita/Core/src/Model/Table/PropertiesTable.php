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
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Query as DatabaseQuery;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Properties Model
 *
 * @property \Cake\ORM\Association\BelongsTo $PropertyTypes
 * @property \Cake\ORM\Association\BelongsTo $ObjectTypes
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
     * Return properties for an object type, considering inheritance.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Filter options.
     * @return \Cake\ORM\Query
     */
    public function findObjectType(Query $query, array $options = [])
    {
        $options = array_filter($options);
        if (count($options) !== 1) {
            throw new BadFilterException(__d('bedita', 'Missing object type to get properties for'));
        }
        $for = reset($options);

        // Build CTE sub-query.
        $from = (new DatabaseQuery($this->getConnection()))
            ->select(['*'])
            ->from($this->getTable())
            ->unionAll(
                (new DatabaseQuery($this->getConnection()))
                    ->select(['*'])
                    ->from(TableRegistry::get('StaticProperties')->getTable())
            );

        // Ugly workaround to make static properties UUIDs work.
        // Without this they would be cast to integers with funny results.
        $query
            ->getTypeMap()
            ->setDefaults([
                $this->getAlias() . '__id' => 'uuid',
                $this->aliasField('id') => 'uuid',
                'id' => 'uuid',
            ]);
        $query->addDefaultTypes($this);

        return $query
            ->from([$this->getAlias() => $from])
            ->where(function (QueryExpression $exp) use ($for) {
                return $exp->in(
                    $this->aliasField($this->ObjectTypes->getForeignKey()),
                    $this->ObjectTypes->find('path', compact('for'))
                        ->select([$this->ObjectTypes->aliasField($this->ObjectTypes->getBindingKey())])
                );
            });
    }
}

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

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DateRanges Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Objects
 *
 * @method \BEdita\Core\Model\Entity\DateRange get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange findOrCreate($search, callable $callback = null, $options = [])
 */
class DateRangesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('date_ranges');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->dateTime('start_date')
            ->allowEmpty('start_date');

        $validator
            ->dateTime('end_date')
            ->allowEmpty('end_date');

        $validator
            ->allowEmpty('params');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['object_id'], 'Objects'));

        return $rules;
    }

    /**
     * Find objects by date range.
     *
     * Create a query to filter objects using start and end date conditions.
     * Accepted options are:
     *   - 'startAfter' or 'startBefore' to find objects having `date_ranges.start_date` after or before param passed
     *   - 'endAfter' or 'endBefore' to find objects having `date_ranges.end_date` after or before param passed
     *
     * ```
     * $table->find('date', ['startAfter' => '2017-03-01'], 'Events');
     * ```
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable date range conditions.
     * @param string $objectType Name of object type to filter like 'Events'.
     * @return \Cake\ORM\Query
     */
    public function findDate(Query $query, array $options, $objectType = 'Objects')
    {
        $accepted = [
            'startAfter' => 'start_date > ',
            'startBefore' => 'start_date < ',
            'endAfter' => 'end_date > ',
            'endBefore' => 'end_date < ',
        ];
        $options = array_intersect_key($options, $accepted);
        if ($options) {
            $subquery = $this->find()
                ->select(['id'])
                ->where($this->alias() . '.object_id = ' . $objectType . '.id');
            foreach ($options as $key => $value) {
                $subquery = $subquery->andWhere($this->alias() . '.' . $accepted[$key] . "'" . $value . "'");
            }
            $query = $query->andWhere(function ($exp, $q) use ($subquery) {
                    return $exp->exists($subquery);
            });
        }

        return $query;
    }
}

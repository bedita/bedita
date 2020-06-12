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
use BEdita\Core\Model\Validation\Validation;
use BEdita\Core\ORM\QueryFilterTrait;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
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
    use QueryFilterTrait;

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
            ->add('start_date', 'dateTime', ['rule' => [Validation::class, 'dateTime']])
            ->allowEmpty('start_date');

        $validator
            ->add('end_date', 'dateTime', ['rule' => [Validation::class, 'dateTime']])
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
     *   - 'start_date' or 'end_date'
     *   - mandatory sub-option must be one of 'gt' (greather than), 'lt' (less than),
     *          'ge' (greater or equal), 'le' (less or equal) with a date
     *
     * Examples
     * ```
     * // find events with a start date after '2017-03-01'
     * $table->find('dateRanges', ['start_date' => ['gt' => '2017-03-01']]);
     *
     * // find events with an ending date before '2017-05-01 22:00:00'
     * $table->find('dateRanges', ['end_date' => ['lt' => '2017-05-01 22:00:00']]);
     * ```
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable date range conditions.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function findDateRanges(Query $query, array $options)
    {
        $options = array_intersect_key($options, array_flip(['start_date', 'end_date', 'date']));
        if (empty($options)) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'start_date or end_date or date parameter missing',
            ]);
        }

        $query = $this->customDateFilter($query, $options);
        unset($options['date']);

        // create filter with `start_date` and `end_date` if present in options
        $options = array_combine(
            array_map([$this, 'aliasField'], array_keys($options)),
            array_values($options)
        );

        return $this->fieldsFilter($query, $options);
    }

    protected function customDateFilter(Query $query, array $options): Query
    {
        $custom = (string)Hash::get($options, 'date');
        if (empty($custom)) {
            return $query;
        }
        $method = sprintf('%sCustomFilter', $custom);
        if (!method_exists($this, $method)) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => __d('bedita', 'Custom date filter "{0}" not available', $custom)
            ]);
        }

        return $this->{$method}($query);
    }

    protected function todayCustomFilter(Query $query): Query
    {
        $today = date('Y-m-d');

        return $query->where([$this->aliasField('start_date') => $today])
            ->orWhere([
                $this->aliasField('start_date') . ' <' => $today,
                $this->aliasField('end_date') . ' >=' => $today,
            ]);
    }

    protected function futureCustomFilter(Query $query): Query
    {
        $today = date('Y-m-d');

        return $query->where([$this->aliasField('start_date') . ' >=' => $today])
            ->orWhere([$this->aliasField('end_date') . ' >=' => $today]);
    }
}

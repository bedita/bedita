<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
use Cake\Database\Expression\QueryExpression;
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
     *   - 'start_date', 'end_date', 'from_date' and 'to_date'
     *   - when using 'from_date' or 'to_date' dates must be provided
     *   - when using 'start_date' or 'end_date' mandatory sub-option
     *          must be one of 'gt' (greather than), 'lt' (less than),
     *          'ge' (greater or equal), 'le' (less or equal) with a date
     *
     * Examples
     * ```
     * // find objects with a start date after '2017-03-01'
     * $table->find('dateRanges', ['start_date' => ['gt' => '2017-03-01']]);
     *
     * // find objects with an ending date before '2017-05-01 22:00:00'
     * $table->find('dateRanges', ['end_date' => ['lt' => '2017-05-01 22:00:00']]);
     *
     * // find objects with valid date ranges from '2018-05-01 22:00:00' onwards
     * $table->find('dateRanges', ['from_date' => '2018-05-01 22:00:00']);
     *
     * // find objects with valid date ranges until '2018-05-01 22:00:00'
     * $table->find('dateRanges', [to_date' => '2018-05-01 22:00:00']);
     * ```
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable date range conditions.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function findDateRanges(Query $query, array $options)
    {
        $allowed = array_flip([
            'start_date',
            'end_date',
            'from_date',
            'to_date'
        ]);
        $options = array_intersect_key($options, $allowed);
        if (empty($options)) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'start_date or end_date or date parameter missing',
            ]);
        }

        $query = $this->fromToDateFilter($query, $options);
        unset($options['from_date'], $options['to_date']);

        // filter on `start_date` and `end_date` fields
        $options = array_combine(
            array_map([$this, 'aliasField'], array_keys($options)),
            array_values($options)
        );

        return $this->fieldsFilter($query, $options);
    }

    /**
     * Modify query object with `from_date`and `to_date` params
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of date conditions.
     * @return \Cake\ORM\Query
     */
    protected function fromToDateFilter(Query $query, array $options): Query
    {
        $from = (string)Hash::get($options, 'from_date');
        $to = (string)Hash::get($options, 'to_date');
        if (empty($from) && empty($to)) {
            return $query;
        }

        if (empty($to)) {
            return $this->fromDateFilter($query, $from);
        }
        if (empty($from)) {
            return $this->toDateFilter($query, $to);
        }

        return $this->betweenDatesFilter($query, $from, $to);
    }

    /**
     * Add `from_date` query condition
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param string $from From date.
     * @return \Cake\ORM\Query
     */
    protected function fromDateFilter(Query $query, string $from): Query
    {
        return $query->where(function (QueryExpression $exp, Query $q) use ($from) {
                return $exp->or_([
                    $q->newExpr()
                        ->gte($this->aliasField('start_date'), $from)
                        ->isNull($this->aliasField('end_date')),
                    $q->newExpr()
                        ->gte($this->aliasField('end_date'), $from),
                ]);
            });
    }

    /**
     * Add `to_date` query condition
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param string $to To date.
     * @return \Cake\ORM\Query
     */
    protected function toDateFilter(Query $query, string $to): Query
    {
        return $query->where(function (QueryExpression $exp, Query $q) use ($to) {
                return $exp->or_([
                    $q->newExpr()
                        ->lte($this->aliasField('start_date'), $to)
                        ->isNull($this->aliasField('end_date')),
                    $q->newExpr()
                        ->lte($this->aliasField('end_date'), $to),
                ]);
            });
    }

    /**
     * Add `from_date`/`to_date` query condition
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param string $from From date.
     * @param string $to To date.
     * @return \Cake\ORM\Query
     */
    protected function betweenDatesFilter(Query $query, string $from, string $to): Query
    {
        return $query->where(function (QueryExpression $exp, Query $q) use ($from, $to) {
            return $exp->or_([
                $q->newExpr()
                    ->gte($this->aliasField('start_date'), $from)
                    ->lte($this->aliasField('start_date'), $to),
                $q->newExpr()
                    ->lte($this->aliasField('start_date'), $to)
                    ->gte($this->aliasField('end_date'), $from),
            ]);
        });
    }
}

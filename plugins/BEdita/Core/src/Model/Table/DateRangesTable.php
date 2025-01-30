<?php
declare(strict_types=1);

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
use Cake\I18n\DateTime;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use DateTimeInterface;
use Exception;

/**
 * DateRanges Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Objects
 * @method \BEdita\Core\Model\Entity\DateRange get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\DateRange newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\DateRange saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\DateRange>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\DateRange> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\DateRange>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\DateRange[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\DateRange> deleteManyOrFail(iterable $entities, array $options = [])
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
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('date_ranges');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->getSchema()->setColumnType('params', 'json');

        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->add('start_date', 'dateTime', ['rule' => [Validation::class, 'dateTime']])
            ->notEmptyDateTime('start_date');

        $validator
            ->add('end_date', 'dateTime', ['rule' => [Validation::class, 'dateTime']])
            ->allowEmptyDateTime('end_date');

        $validator
            ->allowEmptyArray('params');

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
    public function buildRules(RulesChecker $rules): RulesChecker
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
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array $options Array of acceptable date range conditions.
     * @return \Cake\ORM\Query\SelectQuery
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function findDateRanges(SelectQuery $query, array $options): SelectQuery
    {
        $allowed = array_flip([
            'start_date',
            'end_date',
            'from_date',
            'to_date',
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

        return $this->fieldsFilter($query, (array)$options);
    }

    /**
     * Create Time object from $time string
     *
     * @param \DateTimeInterface|string|null $time Input time.
     * @return \DateTimeInterface|null
     */
    protected function getTime(DateTimeInterface|string|null $time): ?DateTimeInterface
    {
        if (empty($time)) {
            return null;
        }

        try {
            return new DateTime($time);
        } catch (Exception $e) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => __d('bedita', 'Wrong date time format "{0}"', $time),
            ]);
        }
    }

    /**
     * Modify query object with `from_date`and `to_date` params
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array $options Array of date conditions.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function fromToDateFilter(SelectQuery $query, array $options): SelectQuery
    {
        $from = $this->getTime(Hash::get($options, 'from_date'));
        $to = $this->getTime(Hash::get($options, 'to_date'));
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
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param \DateTimeInterface $from From date.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function fromDateFilter(SelectQuery $query, DateTimeInterface $from): SelectQuery
    {
        return $query->where(function (QueryExpression $exp, SelectQuery $q) use ($from) {
            return $exp->gte(
                $q->func()->coalesce([
                    $this->aliasField('end_date') => 'identifier',
                    $this->aliasField('start_date') => 'identifier',
                ]),
                $from,
                'datetime'
            );
        });
    }

    /**
     * Add `to_date` query condition
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param \DateTimeInterface $to To date.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function toDateFilter(SelectQuery $query, DateTimeInterface $to): SelectQuery
    {
        return $query->where(function (QueryExpression $exp, SelectQuery $q) use ($to) {
            return $exp->lte(
                $q->func()->coalesce([
                    $this->aliasField('end_date') => 'identifier',
                    $this->aliasField('start_date') => 'identifier',
                ]),
                $to,
                'datetime'
            );
        });
    }

    /**
     * Add `from_date`/`to_date` query condition
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param \DateTimeInterface $from From date.
     * @param \DateTimeInterface $to To date.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function betweenDatesFilter(SelectQuery $query, DateTimeInterface $from, DateTimeInterface $to): SelectQuery
    {
        return $query->where(function (QueryExpression $exp, SelectQuery $q) use ($from, $to) {
            return $exp
                ->lte($this->aliasField('start_date'), $to, 'datetime')
                ->gte(
                    $q->func()->coalesce([
                        $this->aliasField('end_date') => 'identifier',
                        $this->aliasField('start_date') => 'identifier',
                    ]),
                    $from,
                    'datetime'
                );
        });
    }
}

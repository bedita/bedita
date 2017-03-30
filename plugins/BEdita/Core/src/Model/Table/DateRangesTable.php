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
            ->allowEmpty('start_date');

        $validator
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
     * @throws \BEdita\Core\Exception\BadRequestException
     */
    public function findDateRanges(Query $query, array $options)
    {
        $options = array_intersect_key($options, array_flip(['start_date', 'end_date']));

        if (empty($options)) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'start_date or end_date parameter missing',
            ]);
        }

        return $query->where(function (QueryExpression $exp) use ($options) {
            foreach ($options as $field => $conditions) {
                $field = $this->aliasField($field);

                if (!is_array($conditions)) {
                    $exp = $exp->eq($field, $conditions);

                    continue;
                }

                foreach ($conditions as $operator => $value) {
                    switch ($operator) {
                        case 'eq':
                        case '=':
                            $exp = $exp->eq($field, $value);
                            break;

                        case 'neq':
                        case 'ne':
                        case '!=':
                        case '<>':
                            $exp = $exp->notEq($field, $value);
                            break;

                        case 'lt':
                        case '<':
                            $exp = $exp->lt($field, $value);
                            break;

                        case 'lte':
                        case 'le':
                        case '<=':
                            $exp = $exp->lte($field, $value);
                            break;

                        case 'gt':
                        case '>':
                            $exp = $exp->gt($field, $value);
                            break;

                        case 'gte':
                        case 'ge':
                        case '>=':
                            $exp = $exp->gte($field, $value);
                    }
                }
            }

            return $exp;
        });
    }
}

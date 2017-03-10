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
     * $table->find('dateRanges', ['start_date' => ['gt' => '2017-03-01']], 'Events');
     *
     * // find events with an ending date before '2017-05-01 22:00:00'
     * $table->find('dateRanges', ['end_date' => ['lt' => '2017-05-01 22:00:00']], 'Events');
     * ```
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable date range conditions.
     * @param string $objectAlias Name of object type to filter like 'Events'.
     * @return \Cake\ORM\Query
     */
    public function findDateRanges(Query $query, array $options, $objectAlias = 'Objects')
    {
        $options = array_intersect_key($options, ['start_date' => 0, 'end_date' => 0]);
        if (!empty($options)) {
            $conditions = [];
            $subopts = ['gt' => '>', 'lt' => '<', 'ge' => '>=', 'le' => '<='];
            foreach ($options as $key => $value) {
                if (is_array($value) && ($k = key($value)) && !empty($subopts[$k])) {
                    $conditions[] = sprintf("%s.%s %s '%s'", $this->getAlias(), $key, $subopts[$k], $value[$k]);
                }
            }

            if (!empty($conditions)) {
                $subquery = $this->find()->select(['id'])
                            ->where(sprintf('%s.object_id = %s.id', $this->getAlias(), $objectAlias))
                            ->andWhere($conditions);
                $query = $query->where(function ($exp, $q) use ($subquery) {
                        return $exp->exists($subquery);
                });
            }
        }

        return $query;
    }
}

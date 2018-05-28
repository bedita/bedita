<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\State\CurrentApplication;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Config Model - used to handle configuration data in DB
 *
 * @since 4.0.0
 */
class ConfigTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('config');
        $this->setPrimaryKey('name');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always',
                ]
            ],
        ]);

        $this->belongsTo('Applications');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['application_id'], 'Applications'));

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name')
            ->alphaNumeric('name')

            ->requirePresence('context', 'create')
            ->notEmpty('context')

            ->requirePresence('content', 'create')
            ->notEmpty('content');

        return $validator;
    }

    /**
     * Finder for my config. This only returns configuration for the current application.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findMine(Query $query)
    {
        $query->where(function (QueryExpression $exp) {
            return $exp->isNull($this->aliasField('application_id'));
        });

        $id = CurrentApplication::getApplicationId();
        if ($id !== null) {
            $query->orWhere(function (QueryExpression $exp) use ($id) {
                return $exp->eq($this->aliasField('application_id'), $id);
            });
        };

        return  $query;
    }
}

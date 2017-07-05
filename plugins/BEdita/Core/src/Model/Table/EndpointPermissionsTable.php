<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use Cake\Database\Expression\Comparison;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * EndpointPermissions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Endpoints
 * @property \Cake\ORM\Association\BelongsTo $Applications
 * @property \Cake\ORM\Association\BelongsTo $Roles
 *
 * @since 4.0.0
 */
class EndpointPermissionsTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('endpoint_permissions');
        $this->setDisplayField('id');

        $this->belongsTo('Endpoints', [
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Applications');
        $this->belongsTo('Roles');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('permission')
            ->notEmpty('permission');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['endpoint_id'], 'Endpoints'));
        $rules->add($rules->existsIn(['application_id'], 'Applications'));
        $rules->add($rules->existsIn(['role_id'], 'Roles'));

        return $rules;
    }

    /**
     * Find permissions by endpoint.
     *
     * This finder accepts two options:
     *  - `endpointIds`: an array of Endpoint IDs to filter endpoint permissions by.
     *  - `strict`: enable strict mode to exclude endpoint permissions applied to all endpoints
     *      (filter out endpoint permissions with `endpoint_id = NULL`).
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     */
    protected function findByEndpoint(Query $query, array $options)
    {
        $field = $this->aliasField($this->Endpoints->getForeignKey());
        $ids = array_filter((array)Hash::get($options, 'endpointIds', []));
        $strict = Hash::get($options, 'strict', false);

        return $query->where(function (QueryExpression $expr) use ($ids, $field, $strict) {
            return $expr->or_(function (QueryExpression $expr) use ($ids, $field, $strict) {
                if (!empty($ids)) {
                    $expr = $expr->in($field, $ids);
                }
                if (empty($strict)) {
                    $expr = $expr->isNull($field);
                }
                if ($expr->count() === 0) {
                    // If no conditions have been applied so far, it means that `$ids` was empty
                    // and nulls are not allowed. So, no results must be returned. :)
                    $expr = $expr->add(new Comparison('0', '0', 'integer', '!='));
                }

                return $expr;
            });
        });
    }

    /**
     * Find permissions by application.
     *
     * This finder accepts two options:
     *  - `applicationId`: an Application ID to filter endpoint permissions by.
     *  - `strict`: enable strict mode to exclude endpoint permissions applied to all applications
     *      (filter out endpoint permissions with `application_id = NULL`).
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     */
    protected function findByApplication(Query $query, array $options)
    {
        $field = $this->aliasField($this->Applications->getForeignKey());
        $id = Hash::get($options, 'applicationId');
        $strict = Hash::get($options, 'strict', false);

        return $query->where(function (QueryExpression $expr) use ($id, $field, $strict) {
            return $expr->or_(function (QueryExpression $expr) use ($id, $field, $strict) {
                if (!empty($id)) {
                    $expr = $expr->eq($field, $id);
                }
                if (empty($strict)) {
                    $expr = $expr->isNull($field);
                }
                if ($expr->count() === 0) {
                    // If no conditions have been applied so far, it means that `$id` was empty
                    // and nulls are not allowed. So, no results must be returned. :)
                    $expr = $expr->add(new Comparison('0', '0', 'integer', '!='));
                }

                return $expr;
            });
        });
    }

    /**
     * Find permissions by role.
     *
     * This finder accepts two options:
     *  - `roleIds`: an array of Role IDs to filter endpoint permissions by.
     *  - `strict`: enable strict mode to exclude endpoint permissions applied to all roles
     *      (filter out endpoint permissions with `role_id = NULL`).
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     */
    protected function findByRole(Query $query, array $options)
    {
        $field = $this->aliasField($this->Roles->getForeignKey());
        $ids = array_filter((array)Hash::get($options, 'roleIds', []));
        $strict = Hash::get($options, 'strict', false);

        return $query->where(function (QueryExpression $expr) use ($ids, $field, $strict) {
            return $expr->or_(function (QueryExpression $expr) use ($ids, $field, $strict) {
                if (!empty($ids)) {
                    $expr = $expr->in($field, $ids);
                }
                if (empty($strict)) {
                    $expr = $expr->isNull($field);
                }
                if ($expr->count() === 0) {
                    // If no conditions have been applied so far, it means that `$ids` was empty
                    // and nulls are not allowed. So, no results must be returned. :)
                    $expr = $expr->add(new Comparison('0', '0', 'integer', '!='));
                }

                return $expr;
            });
        });
    }
}

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
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     */
    public function findByEndpoint(Query $query, array $options)
    {
        $ids = array_filter((array)Hash::get($options, 'endpointIds', []));
        if (empty($ids)) {
            return $query->where([
                $this->aliasField('endpoint_id') . ' IS' => null,
            ]);
        }

        return $query->where(function (QueryExpression $expr) use ($ids) {
            return $expr->or_(function (QueryExpression $expr) use ($ids) {
                $field = $this->aliasField('endpoint_id');

                return $expr
                    ->in($field, $ids)
                    ->isNull($field);
            });
        });
    }

    /**
     * Find permissions by application.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     */
    public function findByApplication(Query $query, array $options)
    {
        $id = Hash::get($options, 'applicationId');
        if (empty($id)) {
            return $query->where([
                $this->aliasField('application_id') . ' IS' => null,
            ]);
        }

        return $query->where(function (QueryExpression $expr) use ($id) {
            return $expr->or_(function (QueryExpression $expr) use ($id) {
                $field = $this->aliasField('application_id');

                return $expr
                    ->eq($field, $id)
                    ->isNull($field);
            });
        });
    }

    /**
     * Find permissions by role.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     */
    public function findByRole(Query $query, array $options)
    {
        $ids = array_filter((array)Hash::get($options, 'roleIds', []));
        if (empty($ids)) {
            return $query->where([
                $this->aliasField('role_id') . ' IS' => null,
            ]);
        }

        return $query->where(function (QueryExpression $expr) use ($ids) {
            return $expr->or_(function (QueryExpression $expr) use ($ids) {
                $field = $this->aliasField('role_id');

                return $expr
                    ->in($field, $ids)
                    ->isNull($field);
            });
        });
    }
}

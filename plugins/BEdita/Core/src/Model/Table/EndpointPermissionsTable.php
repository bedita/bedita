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

use BEdita\Core\State\CurrentApplication;
use Cake\Cache\Cache;
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
     * Cache configuration name.
     *
     * @var string
     */
    const CACHE_CONFIG = '_bedita_core_';

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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('permission')
            ->notEmptyString('permission');

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
     * Invalidate cache after saving an entity.
     *
     * @return void
     */
    public function afterSave(): void
    {
        Cache::clear(false, self::CACHE_CONFIG);
    }

    /**
     * Invalidate cache after deleting an entity.
     *
     * @return void
     */
    public function afterDelete(): void
    {
        Cache::clear(false, self::CACHE_CONFIG);
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
    protected function findByEndpoint(Query $query, array $options): Query
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
    protected function findByApplication(Query $query, array $options): Query
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
    protected function findByRole(Query $query, array $options): Query
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

    /**
     * Find permissions by role, application and endpoint name.
     *
     * This finder accepts three options:
     * - `endpoint_name``: the endpoint name
     * - `role_name`: the role name
     * - `application_name`: the application name
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     */
    protected function findResource(Query $query, array $options): Query
    {
        $endpoint = Hash::get($options, 'endpoint_name');
        $role = Hash::get($options, 'role_name');
        $application = Hash::get($options, 'application_name');

        if ($endpoint === null) {
            $query = $query->whereNull('endpoint_id');
        } else {
            $query = $query->innerJoinWith('Endpoints', function (Query $query) use ($endpoint) {
                return $query->where(['Endpoints.name' => $endpoint]);
            });
        }

        if ($role === null) {
            $query = $query->whereNull('role_id');
        } else {
            $query = $query->innerJoinWith('Roles', function (Query $query) use ($role) {
                return $query->where(['Roles.name' => $role]);
            });
        }

        if ($application === null) {
            $query = $query->whereNull('application_id');
        } else {
            $query = $query->innerJoinWith('Applications', function (Query $query) use ($application) {
                return $query->where(['Applications.name' => $application]);
            });
        }

        return $query;
    }

    /**
     * Fetch endpoint permissions count using cache.
     *
     * @param int|null $endpointId Endpoint id.
     * @return int
     */
    public function fetchCount(?int $endpointId): int
    {
        $applicationId = CurrentApplication::getApplicationId();
        $endpointIds = array_filter([$endpointId]);
        $key = sprintf('perms_count_%s_%s', $applicationId ?: '*', $endpointId ?: '*');

        return $this->find('byApplication', compact('applicationId'))
            ->find('byEndpoint', compact('endpointIds'))
            ->cache($key, self::CACHE_CONFIG)
            ->count();
    }

    /**
     * Fetch endpoint permissions using cache.
     *
     * @param int|null $endpointId Endpoint id.
     * @param array|null $roleIds Role ids.
     * @param bool $strict Strict check.
     * @return array
     */
    public function fetchPermissions(?int $endpointId, ?array $roleIds, bool $strict): array
    {
        $applicationId = CurrentApplication::getApplicationId();
        $endpointIds = array_filter([$endpointId]);
        //$cacheKey = sprintf('perms_%d_%d_%s_%s', (int)$strict, $applicationId, $endpointId, $roleKey);

        $query = $this->find('byApplication', compact('applicationId', 'strict'))
            ->find('byEndpoint', compact('endpointIds', 'strict'));

        if ($roleIds !== null) {
            $query = $query
                ->find('byRole', compact('roleIds'));
        }

        return $query->toArray();
    }
}

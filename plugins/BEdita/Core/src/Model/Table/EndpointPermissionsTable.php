<?php
declare(strict_types=1);

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

use ArrayAccess;
use BEdita\Core\State\CurrentApplication;
use Cake\Database\Expression\ComparisonExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * EndpointPermissions Model
 *
 * @property \Cake\ORM\Table&\Cake\ORM\Association\BelongsTo $Endpoints
 * @property \Cake\ORM\Table&\Cake\ORM\Association\BelongsTo $Applications
 * @property \Cake\ORM\Table&\Cake\ORM\Association\BelongsTo $Roles
 * @method \Cake\ORM\Query queryCache(\Cake\ORM\Query $query, string $key)
 * @method \BEdita\Core\Model\Entity\EndpointPermission newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\EndpointPermission newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\EndpointPermission[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\EndpointPermission get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\EndpointPermission findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\EndpointPermission patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\EndpointPermission[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\EndpointPermission|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\EndpointPermission saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\EndpointPermission[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\EndpointPermission>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\EndpointPermission[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\EndpointPermission> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\EndpointPermission[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\EndpointPermission>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\EndpointPermission[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\EndpointPermission> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \BEdita\Core\Model\Behavior\QueryCacheBehavior
 * @since 4.0.0
 */
class EndpointPermissionsTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('endpoint_permissions');
        $this->setDisplayField('id');

        $this->addBehavior('BEdita/Core.QueryCache');

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
    public function validationDefault(Validator $validator): Validator
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
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['endpoint_id'], 'Endpoints'));
        $rules->add($rules->existsIn(['application_id'], 'Applications'));
        $rules->add($rules->existsIn(['role_id'], 'Roles'));

        return $rules;
    }

    /**
     * Find permissions by endpoint.
     *
     * This finder accepts two params:
     *  - `$endpointIds`: an array of Endpoint IDs to filter endpoint permissions by.
     *  - `$strict`: enable strict mode to exclude endpoint permissions applied to all endpoints
     *      (filter out endpoint permissions with `endpoint_id = NULL`).
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array|int $endpointIds Array of endpoint IDs.
     * @param bool $strict Enable strict mode
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findByEndpoint(SelectQuery $query, array|int $endpointIds = [], bool $strict = false): SelectQuery
    {
        $field = $this->aliasField($this->Endpoints->getForeignKey());

        return $query->where(function (QueryExpression $expr) use ($endpointIds, $field, $strict) {
            return $expr->or(function (QueryExpression $expr) use ($endpointIds, $field, $strict) {
                if (!empty($endpointIds)) {
                    $expr = $expr->in($field, (array)$endpointIds);
                }
                if (empty($strict)) {
                    $expr = $expr->isNull($field);
                }
                if ($expr->count() === 0) {
                    // If no conditions have been applied so far, it means that `$ids` was empty
                    // and nulls are not allowed. So, no results must be returned. :)
                    $expr = $expr->add(new ComparisonExpression('0', '0', 'integer', '!='));
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
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param int|null $applicationId The application id.
     * @param bool $strict Enable strict mode.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findByApplication(SelectQuery $query, ?int $applicationId = null, bool $strict = false): SelectQuery
    {
        $field = $this->aliasField($this->Applications->getForeignKey());

        return $query->where(function (QueryExpression $expr) use ($applicationId, $field, $strict) {
            return $expr->or(function (QueryExpression $expr) use ($applicationId, $field, $strict) {
                if (!empty($applicationId)) {
                    $expr = $expr->eq($field, $applicationId);
                }
                if (empty($strict)) {
                    $expr = $expr->isNull($field);
                }
                if ($expr->count() === 0) {
                    // If no conditions have been applied so far, it means that `$id` was empty
                    // and nulls are not allowed. So, no results must be returned. :)
                    $expr = $expr->add(new ComparisonExpression('0', '0', 'integer', '!='));
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
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array|int $roleIds The role ids.
     * @param bool $strict Enable strict mode.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findByRole(SelectQuery $query, array|int $roleIds = [], bool $strict = false): SelectQuery
    {
        $field = $this->aliasField($this->Roles->getForeignKey());

        return $query->where(function (QueryExpression $expr) use ($roleIds, $field, $strict) {
            return $expr->or(function (QueryExpression $expr) use ($roleIds, $field, $strict) {
                if (!empty($roleIds)) {
                    $expr = $expr->in($field, (array)$roleIds);
                }
                if (empty($strict)) {
                    $expr = $expr->isNull($field);
                }
                if ($expr->count() === 0) {
                    // If no conditions have been applied so far, it means that `$ids` was empty
                    // and nulls are not allowed. So, no results must be returned. :)
                    $expr = $expr->add(new ComparisonExpression('0', '0', 'integer', '!='));
                }

                return $expr;
            });
        });
    }

    /**
     * Find permissions by role, application and endpoint name.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param string|null $endpoint_name The endpoint name.
     * @param string|null $role_name The role name.
     * @param string|null $application_name The application name.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findResource(
        SelectQuery $query,
        ?string $endpoint_name = null,
        ?string $role_name = null,
        ?string $application_name = null
    ): SelectQuery {
        if ($endpoint_name === null) {
            $query = $query->whereNull('endpoint_id');
        } else {
            $query = $query->innerJoinWith('Endpoints', function (SelectQuery $query) use ($endpoint_name) {
                return $query->where(['Endpoints.name' => $endpoint_name]);
            });
        }

        if ($role_name === null) {
            $query = $query->whereNull('role_id');
        } else {
            $query = $query->innerJoinWith('Roles', function (SelectQuery $query) use ($role_name) {
                return $query->where(['Roles.name' => $role_name]);
            });
        }

        if ($application_name === null) {
            $query = $query->whereNull('application_id');
        } else {
            $query = $query->innerJoinWith('Applications', function (SelectQuery $query) use ($application_name) {
                return $query->where(['Applications.name' => $application_name]);
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
        $key = sprintf('perms_count_%s_%s', $applicationId ?: 'any', $endpointId ?: 'any');

        $query = $this->find('byApplication', applicationId: $applicationId)
            ->find('byEndpoint', endpointIds: $endpointIds);

        return $this->queryCache($query, $key)
            ->count();
    }

    /**
     * Fetch endpoint permissions using cache.
     *
     * @param int|null $endpointId Endpoint id.
     * @param \ArrayAccess|array|null $user User data. Is null if user is unlogged and contains `roles` array if logged.
     * @param bool $strict Strict check.
     * @return array
     */
    public function fetchPermissions(?int $endpointId, array|ArrayAccess|null $user, bool $strict): array
    {
        $applicationId = CurrentApplication::getApplicationId();
        $endpointIds = array_filter([$endpointId]);
        $key = sprintf('perms_%d_%s_%s', (int)$strict, $applicationId ?: 'any', $endpointId ?: 'any');

        // anonymous user
        if ($user === null) {
            $query = $this->find('byApplication', applicationId: $applicationId, strict: $strict)
                ->find('byEndpoint', endpointIds: $endpointIds, strict: $strict);

            return $this->queryCache($query, $key)
                ->toArray();
        }

        $roleIds = (array)Hash::extract($user, 'roles.{n}.id');
        sort($roleIds);
        $key .= sprintf('_%s', implode('.', $roleIds));

        $query = $this->find('byApplication', applicationId: $applicationId, strict: $strict)
            ->find('byEndpoint', endpointIds: $endpointIds, strict: $strict)
            ->find('byRole', roleIds: $roleIds);

        return $this->queryCache($query, $key)
            ->toArray();
    }
}

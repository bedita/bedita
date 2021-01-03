<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Auth;

use BEdita\Core\Model\Entity\EndpointPermission;
use BEdita\Core\Model\Table\RolesTable;
use BEdita\Core\State\CurrentApplication;
use Cake\Auth\BaseAuthorize;
use Cake\Cache\Cache;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Provide authorization on a per-endpoint basis.
 *
 * @since 4.0.0
 */
class EndpointAuthorize extends BaseAuthorize
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
     * If 'blockAnonymousUsers' is true no access will be granted
     * to unauthenticated users otherwise authorization check is performed
     * If 'defaultAuthorized' is set current request is authorized
     * unless a specific permission is set.
     */
    protected $_defaultConfig = [
        'blockAnonymousUsers' => true,
        'defaultAuthorized' => false,
    ];

    /**
     * Request object instance.
     *
     * @var \Cake\Http\ServerRequest
     */
    protected $request;

    /**
     * Cache result of `authorized()` method call.
     *
     * This is required for controller to know whether authorization was granted on all contents,
     * or only on those that belong to the current user. Whatever that means, it is controller's
     * responsibility to interpret, as it may vary. Some controller may also decide to ignore this
     * fine-grained authorization level.
     *
     * @var bool|string
     */
    protected $authorized;

    /**
     * {@inheritDoc}
     */
    public function authorize($user, ServerRequest $request)
    {
        $this->request = $request;

        // if 'blockAnonymousUsers' configuration is true and user unlogged authorization is denied
        if (
            !$this->getConfig('defaultAuthorized') &&
            $this->isAnonymous($user) &&
            $this->getConfig('blockAnonymousUsers')
        ) {
            $this->unauthenticated();
        }

        // For anonymous users performing write operations, use strict mode.
        $strict = ($this->isAnonymous($user) && !$this->request->is(['get', 'head']));

        $path = array_values(array_filter(explode('/', $this->request->getPath())));
        $endpoint = Hash::get($path, '0', '');
        $this->checkDisabled($endpoint);

        $permsCount = $this->permissionsCount($endpoint);

        // If request si authorized and no permission is set on it then it is authorized for anyone
        if ($this->getConfig('defaultAuthorized') && ($permsCount === 0)) {
            return $this->authorized = true;
        }

        $permissions = $this->loadPermissions($user, $endpoint, $strict);

        $this->authorized = $this->checkPermissions($permissions);

        if (empty($permissions) && ($permsCount === 0)) {
            // If no permissions are set for an endpoint, assume the least restrictive permissions possible.
            // This does not apply to write operations for anonymous users: those **MUST** be explicitly allowed.
            $this->authorized = !$strict;
        }

        // if 'administratorOnly' configuration is true logged user must have administrator role
        if ($this->authorized && $this->getConfig('administratorOnly')) {
            $this->authorized = in_array(RolesTable::ADMIN_ROLE, Hash::extract($user, 'roles.{n}.id'));
        }

        if ($this->isAnonymous($user) && $this->authorized !== true) {
            // Anonymous user should not get a 403. Thus, we invoke authentication provider's
            // `unauthenticated()` method. Furthermore, for anonymous users, `mine` doesn't make any sense,
            // so we treat that as a non-authorized request.
            $this->unauthenticated();
        }

        // Authorization is granted for both `true` and `'mine'` values.
        return !empty($this->authorized);
    }

    /**
     * Perform user unauthentication to return 401 Unauthorized
     * instead of 403 Forbidden
     *
     * @return void
     */
    protected function unauthenticated()
    {
        $controller = $this->_registry->getController();
        $controller
            ->Auth->getAuthenticate('BEdita/API.Jwt')
            ->unauthenticated($controller->request, $controller->response);
    }

    /**
     * Check if user is anonymous.
     *
     * @param array|\ArrayAccess $user User data.
     * @return bool
     */
    public function isAnonymous($user)
    {
        return !empty($user['_anonymous']);
    }

    /**
     * Check if endpoint is disabled.
     *
     * @param string $name Endpoint name.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException If endpoint is disabled
     */
    protected function checkDisabled(string $name): void
    {
        $disabled = (array)Cache::remember(
            'disabled_endpoints',
            function () {
                return TableRegistry::getTableLocator()->get('Endpoints')
                    ->find('list', ['valueField' => 'name'])
                    ->where(['enabled' => false])
                    ->toList();
            },
            self::CACHE_CONFIG
        );

        if (in_array($name, $disabled)) {
            throw new NotFoundException(__d('bedita', 'Resource not found.'));
        }
    }

    protected function permissionsCount(string $endpoint)
    {
        $applicationId = CurrentApplication::getApplicationId();

        return (int)Cache::remember(
            'perms_count_' . $applicationId . '_' . $endpoint,
            function () use ($applicationId, $endpoint) {
                $query = TableRegistry::getTableLocator()->get('EndpointPermissions')
                    ->find('byApplication', compact('applicationId'));
                $query = $query->innerJoinWith('Endpoints', function (Query $query) use ($endpoint) {
                    return $query->where(['Endpoints.name' => $endpoint]);
                });

                return $query->count();
            },
            self::CACHE_CONFIG
        );
    }

    /**
     * Load endpoint permissions usgin cache
     *
     * @param mixed $user Logged user.
     * @param string $endpoint Endpoint name.
     * @param bool $strict Strict check.
     * @return array
     */
    protected function loadPermissions($user, string $endpoint, bool $strict = false): array
    {
        $applicationId = CurrentApplication::getApplicationId();
        $roleIds = null;
        $roleKey = 'anonymous';
        if (!$this->isAnonymous($user)) {
            $roleIds = Hash::extract($user, 'roles.{n}.id');
            sort($roleIds);
            $roleKey = implode('-', $roleIds);
        }
        $cacheKey = sprintf('perms_%d_%d_%s_%s', (int)$strict, $applicationId, $endpoint, $roleKey);

        return (array)Cache::remember(
            $cacheKey,
            function () use ($applicationId, $endpoint, $roleIds, $strict) {

                $entity = TableRegistry::getTableLocator()->get('Endpoints')
                    ->find()
                    ->select(['id'])
                    ->where(['Endpoints.name' => $endpoint])
                    ->first();
                $endpointIds = [];
                if (!empty($entity)) {
                    $endpointIds = [$entity->get('id')];
                }

                $query = TableRegistry::getTableLocator()->get('EndpointPermissions')
                    ->find('byApplication', compact('applicationId', 'strict'))
                    ->find('byEndpoint', compact('endpointIds', 'strict'));
                if ($roleIds != null) {
                    $query = $query->find('byRole', compact('roleIds'));
                }

                return $query->toArray();
            },
            self::CACHE_CONFIG
        );
    }

    /**
     * Checks if request can be authorized basing on a set of applicable permissions.
     *
     * @param \BEdita\Core\Model\Entity\EndpointPermission[] $permissions Set of applicable permissions.
     * @return bool|string
     */
    protected function checkPermissions(array $permissions)
    {
        $shift = EndpointPermission::PERM_READ;
        if (!$this->request->is(['get', 'head'])) {
            $shift = EndpointPermission::PERM_WRITE;
        }

        $result = EndpointPermission::PERM_NO;
        foreach ($permissions as $permission) {
            $permission = $permission->permission >> $shift & EndpointPermission::PERM_YES;
            $result = $result | $permission;

            if ($permission === EndpointPermission::PERM_BLOCK) {
                $result = EndpointPermission::PERM_NO;

                break;
            }
        }

        return EndpointPermission::decode($result);
    }
}

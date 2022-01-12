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
use Cake\Auth\BaseAuthorize;
use Cake\Datasource\ModelAwareTrait;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;

/**
 * Provide authorization on a per-endpoint basis.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\EndpointsTable $Endpoints
 * @property \BEdita\Core\Model\Table\EndpointPermissionsTable $EndpointPermissions
 */
class EndpointAuthorize extends BaseAuthorize
{
    use ModelAwareTrait;

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
    public function authorize($user, ServerRequest $request): bool
    {
        // if 'blockAnonymousUsers' configuration is true and user unlogged authorization is denied
        if (
            !$this->getConfig('defaultAuthorized') &&
            $this->isAnonymous($user) &&
            $this->getConfig('blockAnonymousUsers')
        ) {
            $this->unauthenticated();
        }
        $this->loadModel('Endpoints');
        $this->loadModel('EndpointPermissions');

        // For anonymous users performing write operations, use strict mode.
        $readRequest = $request->is(['get', 'head']);
        $strict = ($this->isAnonymous($user) && !$readRequest);

        $endpointId = $this->Endpoints->fetchId($request->getPath());
        $permsCount = $this->EndpointPermissions->fetchCount($endpointId);

        // If request si authorized and no permission is set on it then it is authorized for anyone
        if ($this->getConfig('defaultAuthorized') && ($endpointId === null || $permsCount === 0)) {
            return $this->authorized = true;
        }

        $permissions = $this->EndpointPermissions->fetchPermissions($endpointId, $user, $strict);
        $this->authorized = $this->checkPermissions($permissions, $readRequest);

        if (empty($permissions) && ($endpointId === null || $permsCount === 0)) {
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
     * Checks if request can be authorized basing on a set of applicable permissions.
     *
     * @param \BEdita\Core\Model\Entity\EndpointPermission[] $permissions Set of applicable permissions.
     * @param bool $readRequest Read request flag.
     * @return bool|string
     */
    protected function checkPermissions(array $permissions, bool $readRequest)
    {
        $shift = EndpointPermission::PERM_READ;
        if (!$readRequest) {
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

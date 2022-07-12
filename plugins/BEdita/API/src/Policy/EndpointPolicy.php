<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Policy;

use Authorization\IdentityInterface;
use Authorization\Policy\RequestPolicyInterface;
use BEdita\Core\Model\Entity\Application;
use BEdita\Core\Model\Entity\EndpointPermission;
use BEdita\Core\Model\Entity\User;
use BEdita\Core\Model\Table\RolesTable;
use Cake\Core\InstanceConfigTrait;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\ServerRequest;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;

/**
 * Endpoint policy.
 */
class EndpointPolicy implements RequestPolicyInterface
{
    use InstanceConfigTrait;
    use LocatorAwareTrait;

    /**
     * @inheritDoc
     */
    public function canAccess(?IdentityInterface $identity, ServerRequest $request)
    {
        $user = $this->getUser($identity);

        /** @var \BEdita\Core\Model\Table\EndpointsTable $Endpoints  */
        $Endpoints = $this->fetchTable('Endpoints');
        /** @var \BEdita\Core\Model\Table\EndpointPermissionsTable $EndpointPermissions */
        $EndpointPermissions = $this->fetchTable('EndpointPermissions');

        // For anonymous users performing write operations, use strict mode.
        $readRequest = $request->is(['get', 'head']);
        $strict = ($identity === null && !$readRequest);

        $endpointId = $Endpoints->fetchId($request->getPath());
        $permsCount = $EndpointPermissions->fetchCount($endpointId);

        // If request si authorized and no permission is set on it then it is authorized for anyone
        if ($request->getAttribute('EndpointDefaultAuthorized') && ($endpointId === null || $permsCount === 0)) {
            return $this->authorized = true;
        }

        $permissions = $EndpointPermissions->fetchPermissions($endpointId, $user, $strict);
        $this->authorized = $this->checkPermissions($permissions, $readRequest);

        if (empty($permissions) && ($endpointId === null || $permsCount === 0)) {
            // If no permissions are set for an endpoint, assume the least restrictive permissions possible.
            // This does not apply to write operations for anonymous users: those **MUST** be explicitly allowed.
            $this->authorized = !$strict;
        }

        // if 'administratorOnly' configuration is true logged user must have administrator role
        if ($this->authorized && $request->getAttribute('EndpointAdministratorOnly')) {
            $this->authorized = in_array(RolesTable::ADMIN_ROLE, Hash::extract((array)$user, 'roles.{n}.id'));
        }

        if ($identity === null && $this->authorized !== true) {
            // Anonymous user should not get a 403 but 401.
            // Furthermore, for anonymous users, `mine` doesn't make any sense,
            // so we treat that as a non-authorized request.
            throw new UnauthorizedException();
        }

        return $this->isAuthorized();
    }

    /**
     * Extract user from identity.
     *
     * @param \Authorization\IdentityInterface|null $identity The identity
     * @return array|null
     */
    protected function getUser(?IdentityInterface $identity): ?array
    {
        if ($identity === null || $identity->getOriginalData() instanceof Application) {
            return null;
        }

        $user = $identity->getOriginalData();
        if ($user instanceof User) {
            return $user->toArray();
        }

        if (!is_array($user) && !$user instanceof \ArrayObject) {
            return null; // throw error?
        }

        return (array)$user;
    }

    /**
     * Is endpoint authorized?
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        // Authorization is granted for both `true` and `'mine'` values.
        return !empty($this->authorized);
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

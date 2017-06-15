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

namespace BEdita\API\Auth;

use BEdita\Core\Model\Entity\Application;
use BEdita\Core\Model\Entity\Endpoint;
use BEdita\Core\Model\Entity\EndpointPermission;
use BEdita\Core\State\CurrentApplication;
use Cake\Auth\BaseAuthorize;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
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
     * {@inheritDoc}
     *
     * If 'blockAnonymousUsers' is true no access will be granted
     * to unauthenticated users otherwise authorization check is performed
     * If 'defaultAuthorized' is set current request is authorized
     * unless a specific permission is set.
     */
    protected $_defaultConfig = [
        'blockAnonymousApps' => false,
        'blockAnonymousUsers' => true,
        'apiKeyHeaderName' => 'X-Api-Key',
        'defaultAuthorized' => false,
    ];

    /**
     * Current endpoint entity.
     *
     * @var \BEdita\Core\Model\Entity\Endpoint|null
     */
    protected $endpoint = null;

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
        if (!$this->getConfig('defaultAuthorized') &&
            $this->isAnonymous($user) &&
            $this->getConfig('blockAnonymousUsers')) {
            $this->unauthenticate();
        }

        // For anonymous users performing write operations, use strict mode.
        $strict = ($this->isAnonymous($user) && !$this->request->is(['get', 'head']));

        $application = $this->getApplication();
        $endpoint = $this->getEndpoint();
        $permissions = $this->getPermissions($user, $application, $endpoint, $strict)->toArray();
        $allPermissions = $this->getPermissions(false, $application, $endpoint);

        // If request si authorized and no permission is set on it then it is authorized for anyone
        if ($this->getConfig('defaultAuthorized') && ($endpoint->isNew() || $allPermissions->count() === 0)) {
            return $this->authorized = true;
        }

        $this->authorized = $this->checkPermissions($permissions);
        if (empty($permissions) && ($endpoint->isNew() || $allPermissions->count() === 0)) {
            // If no permissions are set for an endpoint, assume the least restrictive permissions possible.
            // This does not apply to write operations for anonymous users: those **MUST** be explicitly allowed.
            $this->authorized = !$strict;
        }

        if ($this->isAnonymous($user) && $this->authorized !== true) {
            // Anonymous user should not get a 403. Thus, we invoke authentication provider's
            // `unauthenticated()` method. Furthermore, for anonymous users, `mine` doesn't make any sense,
            // so we treat that as a non-authorized request.
            $this->unauthenticate();
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
    protected function unauthenticate()
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
     * Get application for request.
     *
     * @return \BEdita\Core\Model\Entity\Application|null
     * @throws \Cake\Network\Exception\ForbiddenException Throws an exception if API key is missing or invalid.
     */
    protected function getApplication()
    {
        $application = CurrentApplication::getApplication();
        if ($application === null) {
            $header = $this->request->getHeaderLine($this->_config['apiKeyHeaderName']);
            if (empty($header) && empty($this->_config['blockAnonymousApps'])) {
                return null;
            }

            try {
                CurrentApplication::setFromApiKey($header);
            } catch (\BadMethodCallException $e) {
                throw new ForbiddenException(__d('bedita', 'Missing API key'));
            } catch (RecordNotFoundException $e) {
                throw new ForbiddenException(__d('bedita', 'Invalid API key'));
            }

            $application = CurrentApplication::getApplication();
        }

        return $application;
    }

    /**
     * Get endpoint for request.
     *
     * @return \BEdita\Core\Model\Entity\Endpoint
     * @throws \Cake\Network\Exception\NotFoundException If endpoint is disabled
     */
    protected function getEndpoint()
    {
        if (!empty($this->endpoint)) {
            return $this->endpoint;
        }

        $endpointName = $this->request->url;
        if (($slashPos = strpos($endpointName, '/')) !== false) {
            $endpointName = substr($endpointName, 0, $slashPos);
        }

        $Endpoints = TableRegistry::get('Endpoints');
        $this->endpoint = $Endpoints->find()
            ->where([
                'Endpoints.name' => $endpointName,
            ])
            ->first();

        if (!$this->endpoint) {
            $this->endpoint = $Endpoints->newEntity(
                [
                    'name' => $endpointName,
                    'enabled' => true,
                ],
                ['validate' => false]
            );
        }

        if (!$this->endpoint->enabled) {
            throw new NotFoundException(__d('bedita', 'Resource not found.'));
        }

        return $this->endpoint;
    }

    /**
     * Get list of applicable permissions.
     *
     * @param array|\ArrayAccess|false $user Authenticated (or anonymous) user.
     * @param \BEdita\Core\Model\Entity\Application|null $application Current application.
     * @param \BEdita\Core\Model\Entity\Endpoint|null $endpoint Current endpoint.
     * @param bool $strict Use strict mode. Do not consider permissions set on all applications/endpoints.
     * @return \Cake\ORM\Query
     * @todo Future optimization: Permissions that are `0` on the two bits that are interesting for the current request can be excluded...
     */
    protected function getPermissions($user, Application $application = null, Endpoint $endpoint = null, $strict = false)
    {
        $applicationId = $application ? $application->id : null;
        $endpointIds = $endpoint && !$endpoint->isNew() ? [$endpoint->id] : [];

        $query = TableRegistry::get('EndpointPermissions')
            ->find('byApplication', compact('applicationId', 'strict'))
            ->find('byEndpoint', compact('endpointIds', 'strict'));

        if ($user !== false && !$this->isAnonymous($user)) {
            $roleIds = Hash::extract($user, 'roles.{n}.id');
            $query = $query
                ->find('byRole', compact('roleIds'));
        }

        return $query;
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

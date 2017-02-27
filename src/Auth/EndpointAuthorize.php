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
     */
    protected $_defaultConfig = [
        'disallowAnonymousApplications' => false,
        'apiKeyHeaderName' => 'X-Api-Key',
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

        $application = $this->getApplication();
        $endpoint = $this->getEndpoint();
        $permissions = $this->getPermissions($user, $application, $endpoint)->toArray();

        $this->authorized = $this->checkPermissions($permissions);

        if (!empty($user['_anonymous']) && $this->authorized !== true) {
            // Anonymous user should not get a 403. Thus, we invoke authentication provider's
            // `unauthenticated()` method. Furthermore, for anonymous users, `mine` doesn't make any sense,
            // so we treat that as a non-authorized request.
            $controller = $this->_registry->getController();

            $controller
                ->Auth->getAuthenticate('BEdita/API.Jwt')
                ->unauthenticated($controller->request, $controller->response);
        }

        // Authorization is granted for both `true` and `'mine'` values.
        return !empty($this->authorized);
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
            if (empty($header) && empty($this->_config['disallowAnonymousApplications'])) {
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
     * @return \BEdita\Core\Model\Entity\Endpoint|null
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

        if (empty($endpointName)) {
            return null;
        }

        $this->endpoint = TableRegistry::get('Endpoints')->find()
            ->where([
                'Endpoints.name' => $endpointName,
                'Endpoints.enabled' => true,
            ])
            ->first();

        return $this->endpoint;
    }

    /**
     * Get list of applicable permissions.
     *
     * @param mixed $user Authenticated (or anonymous) user.
     * @param \BEdita\Core\Model\Entity\Application|null $application Current application.
     * @param \BEdita\Core\Model\Entity\Endpoint|null $endpoint Current endpoint.
     * @return \Cake\ORM\Query
     * @todo Future optimization: Permissions that are `0` on the two bits that are interesting for the current request can be excluded...
     */
    protected function getPermissions($user, Application $application = null, Endpoint $endpoint = null)
    {
        $roleIds = Hash::extract($user, 'roles.{n}.id');
        $applicationId = $application ? $application->id : null;
        $endpointIds = $endpoint ? [$endpoint->id] : [];

        return TableRegistry::get('EndpointPermissions')
            ->find('byRole', compact('roleIds'))
            ->find('byApplication', compact('applicationId'))
            ->find('byEndpoint', compact('endpointIds'));
    }

    /**
     * Checks if request can be authorized basing on a set of applicable permissions.
     *
     * @param \BEdita\Core\Model\Entity\EndpointPermission[] $permissions Set of applicable permissions.
     * @return bool|string
     */
    protected function checkPermissions(array $permissions)
    {
        $result = EndpointPermission::PERM_NO;
        if (empty($permissions)) {
            $count = TableRegistry::get('EndpointPermissions')->find()->count();
            if ($count === 0) {
                $result = EndpointPermission::PERM_YES;
            }

            return EndpointPermission::decode($result);
        }

        $shift = EndpointPermission::PERM_READ;
        if (!$this->request->is(['get', 'head'])) {
            $shift = EndpointPermission::PERM_WRITE;
        }

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

<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Controller;

use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Zend\Diactoros\Uri;

/**
 * Controller for `/home` endpoint.
 *
 * @since 4.0.0
 */
class HomeController extends AppController
{

    /**
     * Default endpoints with supported methods
     * 'ALL' means 'GET', 'POST', 'PATCH' and 'DELETE' are supported
     *
     * @var array
     */
    protected $defaultEndpoints = [
        '/auth' => ['GET', 'POST'],
        '/admin' => 'ALL',
        '/model' => 'ALL',
        '/roles' => 'ALL',
        '/signup' => ['POST'],
        '/status' => ['GET'],
        '/trash' => 'ALL',
    ];

    /**
     * Default allowed methods for unlogged users
     * '/*' means: all other endpoints
     *
     * @var array
     */
    protected $defaultAllowUnlogged = [
        '/auth' => ['POST'],
        '/signup' => ['POST'],
        '/*' => ['GET'],
    ];

    /**
     * List API available endpoints
     *
     * @return void
     */
    public function index()
    {
        $this->request->allowMethod(['get', 'head']);

        $objectTypesEndpoints = $this->objectTypesEndpoints();
        $endPoints = array_merge($objectTypesEndpoints, $this->defaultEndpoints);
        foreach ($endPoints as $e => $methods) {
            if ($methods === 'ALL') {
                $methods = ['GET', 'POST', 'PATCH', 'DELETE'];
            }
            $allow = [];
            foreach ($methods as $method) {
                if ($this->checkAuthorization($e, $method)) {
                    $allow[] = $method;
                }
            }
            $resources[$e] = [
                'href' => Router::url($e, true),
                'hints' => [
                    'allow' => $allow,
                    'formats' => [
                        'application/json',
                        'application/vnd.api+json',
                    ],
                    'display' => [
                        'label' => Inflector::camelize(substr($e, 1)),
                    ],
                    'object_type' => !empty($objectTypesEndpoints[$e]),
                ],
            ];
        }
        $project = Configure::read('Project');
        $version = Configure::read('BEdita.version');

        $this->set('_meta', compact('resources', 'project', 'version'));
        $this->set('_serialize', []);
    }

    /**
     * Returns available object types to list as endpoints
     *
     * @return array Array of object type names
     */
    protected function objectTypesEndpoints()
    {
        $allTypes = TableRegistry::get('ObjectTypes')->find('list', ['keyField' => 'name', 'valueField' => 'is_abstract'])->toArray();
        $endPoints = [];
        foreach ($allTypes as $t => $abstract) {
            $endPoints['/' . $t] = $abstract ? ['GET', 'DELETE'] : 'ALL';
        }

        return $endPoints;
    }

    /**
     * Check Authorization on endpoint
     *
     * @param string $endpoint Endpoint URI
     * @param string $method HTTP method
     * @return bool True on granted authorization, false otherwise
     */
    protected function checkAuthorization($endpoint, $method)
    {
        if (empty(LoggedUser::getUser()) && !$this->unloggedAuthorized($endpoint, $method)) {
            return false;
        }

        $environment = ['REQUEST_METHOD' => $method];
        $uri = new Uri($endpoint);
        $request = new ServerRequest(compact('environment', 'uri'));
        $authorize = $this->Auth->getAuthorize('BEdita/API.Endpoint');

        return $authorize->authorize(LoggedUser::getUser(), $request);
    }

    /**
     * Default unlogged authorization on endpoint method, without permissions check
     *
     * @param string $endpoint Endpoint URI
     * @param string $method HTTP method
     * @return bool True on granted authorization, false otherwise
     */
    protected function unloggedAuthorized($endpoint, $method)
    {
        $defaultAllow = Hash::get($this->defaultAllowUnlogged, $endpoint, $this->defaultAllowUnlogged['/*']);

        return in_array($method, $defaultAllow);
    }
}

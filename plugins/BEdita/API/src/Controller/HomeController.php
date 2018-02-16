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
     * Default endpoints with:
     *  - supported methods, where 'ALL' means 'GET', 'POST', 'PATCH' and 'DELETE' are supported
     *  - multiple types flag, if true multiple types are handled (like abstract object types or `/trash`)
     *
     * @var array
     */
    protected $defaultEndpoints = [
        '/auth' => [
           'methods' => ['GET', 'POST'],
           'multiple_types' => false,
        ],
        '/admin' =>  [
            'methods' => 'ALL',
            'multiple_types' => true,
         ],
         '/model' =>  [
            'methods' => 'ALL',
            'multiple_types' => true,
         ],
         '/roles' =>  [
            'methods' => 'ALL',
            'multiple_types' => false,
         ],
         '/signup' =>  [
            'methods' => ['POST'],
            'multiple_types' => false,
         ],
         '/status' =>  [
            'methods' => ['GET'],
            'multiple_types' => false,
         ],
         '/trash' =>  [
            'methods' => 'ALL',
            'multiple_types' => true,
         ],
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

        $default = Hash::insert($this->defaultEndpoints, '{*}.object_type', false);
        $endPoints = array_merge($this->objectTypesEndpoints(), $default);
        foreach ($endPoints as $e => $data) {
            $resources[$e] = $this->endpointFeatures($e, $data);
        }
        $project = Configure::read('Project');
        $version = Configure::read('BEdita.version');

        $this->set('_meta', compact('resources', 'project', 'version'));
        $this->set('_serialize', []);
    }

    /**
     * Return endpoint features to display in `/home` response
     *
     * @param string $endpoint Endpoint name
     * @param array $options Endpoint options - methods and multiple types flag
     * @return array Array of features
     */
    protected function endpointFeatures($endpoint, $options)
    {
        $methods = $options['methods'];
        if ($methods === 'ALL') {
            $methods = ['GET', 'POST', 'PATCH', 'DELETE'];
        }
        $allow = [];
        foreach ($methods as $method) {
            if ($this->checkAuthorization($endpoint, $method)) {
                $allow[] = $method;
            }
        }
        return [
            'href' => Router::url($endpoint, true),
            'hints' => [
                'allow' => $allow,
                'formats' => [
                    'application/json',
                    'application/vnd.api+json',
                ],
                'display' => [
                    'label' => Inflector::camelize(substr($endpoint, 1)),
                ],
                'object_type' => $options['object_type'],
                'multiple_types' => $options['multiple_types'],
            ],
        ];
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
            $endPoints['/' . $t] = [
                'methods' => $abstract ? ['GET', 'DELETE'] : 'ALL',
                'object_type' => true,
                'multiple_types' => $abstract,
            ];
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

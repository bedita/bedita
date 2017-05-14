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
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
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
     * List API available endpoints
     *
     * @return void
     */
    public function index()
    {
        $this->request->allowMethod(['get', 'head']);

        $baseUrl = Router::fullBaseUrl();

        $endPoints = ['/objects', '/roles', '/object_types', '/status', '/trash'];
        $endPoints = array_unique(array_merge($this->objectTypesEndpoints(), $endPoints));
        foreach ($endPoints as $e) {
            $allow = [];
            foreach (['GET', 'POST', 'PATCH', 'DELETE'] as $method) {
                if ($this->checkAuthorization($e, $method)) {
                    $allow[] = $method;
                }
            }
            $resources[$e] = [
                'href' => $baseUrl . $e,
                'hints' => [
                    'allow' => $allow,
                    'formats' => [
                        'application/json',
                        'application/vnd.api+json',
                    ],
                    'display' => [
                        'label' => Inflector::camelize(substr($e, 1)),
                    ]
                ],
            ];
        }

        $this->set('_meta', compact('resources'));
        $this->set('_serialize', []);
    }

    /**
     * Returns available object types to list as endpoints
     *
     * @return array Array of object type names
     */
    protected function objectTypesEndpoints()
    {
        $allTypes = TableRegistry::get('ObjectTypes')->find('list', ['valueField' => 'name'])->toArray();
        $endPoints = [];
        foreach ($allTypes as $t) {
            $endPoints[] = '/' . $t;
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
        if ($method !== 'GET' && empty(LoggedUser::getUser())) {
            return false;
        }
        $environment = ['REQUEST_METHOD' => $method];
        $uri = new Uri($endpoint);
        $request = new ServerRequest(compact('environment', 'uri'));
        $authorize = $this->Auth->getAuthorize('BEdita/API.Endpoint');

        return $authorize->authorize(LoggedUser::getUser(), $request);
    }
}

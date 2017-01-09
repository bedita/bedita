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
namespace BEdita\API\Controller;

use Cake\Routing\Router;
use Cake\Utility\Inflector;

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

        $endPoints = ['/objects', '/users', '/roles', '/object_types', '/status', '/trash'];
        foreach ($endPoints as $e) {
            $randomColor = substr(md5($e . 'color'), 0, 6);
            $resources[$e] = [
                'href' => $baseUrl . $e,
                'hints' => [
                    'allow' => ['GET', 'POST', 'PATCH', 'DELETE'],
                    'formats' => [
                        'application/json',
                        'application/vnd.api+json',
                    ],
                    'display' => [
                        'label' => Inflector::camelize(substr($e, 1)),
                        'color' => '#' . $randomColor
                    ]
                ],
            ];
        }

        $this->set('_meta', compact('resources'));
        $this->set('_serialize', []);
    }
}

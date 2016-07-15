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

/**
 * Controller for `/home` endpoint.
 *
 * @since 4.0.0
 */
class HomeController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->Auth->allow(['index']);
    }

    /**
     * List API available endpoints
     *
     * @return void
     */
    public function index()
    {
        $baseUrl = Router::fullBaseUrl();

        $endPoints = ['/objects', '/users', '/roles'];
        foreach ($endPoints as $e) {
            $resources[$e] = [
                'href' => $baseUrl . $e,
                'hints' => [
                    'allow' => ['GET'],
                    'formats' => [
                        'application/json',
                        'application/vnd.api+json',
                    ]
                ]
            ];
        }

        $meta = compact('resources');
        $this->set('_meta', $meta);
        $this->set('_serialize', []);
    }
}

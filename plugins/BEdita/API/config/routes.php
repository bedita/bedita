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

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'BEdita/API',
    [
        'path' => '/',
        '_namePrefix' => 'api:',
    ],
    function (RouteBuilder $routes) {
        $routes->connect(
            '/home',
            ['controller' => 'Home', 'action' => 'index'],
            ['_method' => 'GET', '_name' => 'home']
        );
        $routes->connect(
            '/roles',
            ['controller' => 'Roles', 'action' => 'index'],
            ['_method' => 'GET', '_name' => 'roles:index']
        );
        $routes->connect(
            '/roles/*',
            ['controller' => 'Roles', 'action' => 'view'],
            ['_method' => 'GET', '_name' => 'roles:view']
        );
        $routes->connect(
            '/roles/:role_id/users',
            ['controller' => 'Users', 'action' => 'index'],
            ['_method' => 'GET', '_name' => 'roles:users']
        );

        $routes->connect(
            '/users',
            ['controller' => 'Users', 'action' => 'index'],
            ['_method' => 'GET', '_name' => 'users:index']
        );
        $routes->connect(
            '/users/*',
            ['controller' => 'Users', 'action' => 'view'],
            ['_method' => 'GET', '_name' => 'users:view']
        );
    }
);

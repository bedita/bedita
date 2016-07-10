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
        // Home.
        $routes->connect(
            '/home',
            ['controller' => 'Home', 'action' => 'index', '_method' => 'GET'],
            ['_name' => 'home']
        );

        // Objects.
        $routes->connect(
            '/objects',
            ['controller' => 'Objects', 'action' => 'index', '_method' => 'GET'],
            ['_name' => 'objects:index']
        );
        $routes->connect(
            '/objects/*',
            ['controller' => 'Objects', 'action' => 'view', '_method' => 'GET'],
            ['_name' => 'objects:view']
        );

        // Roles.
        $routes->connect(
            '/roles',
            ['controller' => 'Roles', 'action' => 'index', '_method' => 'GET'],
            ['_name' => 'roles:index']
        );
        $routes->connect(
            '/roles/*',
            ['controller' => 'Roles', 'action' => 'view', '_method' => 'GET'],
            ['_name' => 'roles:view']
        );
        $routes->connect(
            '/roles/:role_id/users',
            ['controller' => 'Users', 'action' => 'index', '_method' => 'GET'],
            ['_name' => 'roles:users']
        );

        // Users.
        $routes->connect(
            '/users',
            ['controller' => 'Users', 'action' => 'index', '_method' => 'GET'],
            ['_name' => 'users:index']
        );
        $routes->connect(
            '/users/*',
            ['controller' => 'Users', 'action' => 'view', '_method' => 'GET'],
            ['_name' => 'users:view']
        );
        $routes->connect(
            '/users',
            ['controller' => 'Users', 'action' => 'add', '_method' => 'POST'],
            ['_name' => 'users:add']
        );
        $routes->connect(
            '/users/*',
            ['controller' => 'Users', 'action' => 'delete', '_method' => 'DELETE'],
            ['_name' => 'users:delete']
        );
    }
);

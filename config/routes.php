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
use Cake\Routing\Route\InflectedRoute;

Router::plugin(
    'BEdita/API',
    [
        'path' => '/',
        '_namePrefix' => 'api:',
    ],
    function (RouteBuilder $routes) {
        $resourcesControllers = [
            'object_types',
            'properties',
            'roles',
            'streams',
            'users',
        ];
        $routes->setRouteClass(InflectedRoute::class);

        // Home.
        $routes->redirect(
            '/',
            ['_name' => 'api:home'],
            ['persist' => true]
        );
        $routes->connect(
            '/home',
            ['controller' => 'Home', 'action' => 'index'],
            ['_name' => 'home']
        );

        // Status.
        $routes->connect(
            '/status',
            ['controller' => 'Status', 'action' => 'index'],
            ['_name' => 'status']
        );

        // Login.
        $routes->connect(
            '/auth',
            ['controller' => 'Login', 'action' => 'login', '_method' => 'POST'],
            ['_name' => 'login']
        );
        $routes->connect(
            '/auth/change',
            ['controller' => 'Login', 'action' => 'change'],
            ['_name' => 'login:change']
        );
        $routes->connect(
            '/auth',
            ['controller' => 'Login', 'action' => 'whoami', '_method' => 'GET'],
            ['_name' => 'login:whoami']
        );

        // Signup.
        $routes->connect(
            '/signup',
            ['controller' => 'Signup', 'action' => 'signup'],
            ['_name' => 'signup']
        );
        $routes->connect(
            '/signup/activation',
            ['controller' => 'Signup', 'action' => 'activation'],
            ['_name' => 'signup:activation']
        );

        // Upload.
        $routes->connect(
            '/streams/upload/:fileName',
            ['controller' => 'Streams', 'action' => 'upload'],
            ['_name' => 'streams:upload', 'pass' => ['fileName']]
        );

        // Resources.
        $resourcesControllers = implode('|', $resourcesControllers);
        $routes->connect(
            '/:controller',
            ['action' => 'index'],
            ['_name' => 'resources:index', 'controller' => $resourcesControllers]
        );
        $routes->connect(
            '/:controller/:id',
            ['action' => 'resource'],
            ['_name' => 'resources:resource', 'pass' => ['id'], 'controller' => $resourcesControllers]
        );
        $routes->connect(
            '/:controller/:related_id/:relationship',
            ['action' => 'related'],
            ['_name' => 'resources:related', 'controller' => $resourcesControllers]
        );
        $routes->connect(
            '/:controller/:id/relationships/:relationship',
            ['action' => 'relationships'],
            ['_name' => 'resources:relationships', 'controller' => $resourcesControllers]
        );

        // Trash.
        $routes->connect(
            '/trash',
            ['controller' => 'Trash', 'action' => 'index', '_method' => 'GET'],
            ['_name' => 'trash:index']
        );
        $routes->connect(
            '/trash/:id',
            ['controller' => 'Trash', 'action' => 'view', '_method' => 'GET'],
            ['_name' => 'trash:resource', 'pass' => ['id']]
        );
        $routes->connect(
            '/trash/:id',
            ['controller' => 'Trash', 'action' => 'restore', '_method' => 'PATCH'],
            ['_name' => 'trash:restore', 'pass' => ['id']]
        );
        $routes->connect(
            '/trash/:id',
            ['controller' => 'Trash', 'action' => 'delete', '_method' => 'DELETE'],
            ['_name' => 'trash:delete', 'pass' => ['id']]
        );

        // Objects.
        $routes->connect(
            '/:object_type',
            ['controller' => 'Objects', 'action' => 'index'],
            ['_name' => 'objects:index']
        );
        $routes->connect(
            '/:object_type/:id',
            ['controller' => 'Objects', 'action' => 'resource'],
            ['_name' => 'objects:resource', 'pass' => ['id']]
        );
        $routes->connect(
            '/:object_type/:related_id/:relationship',
            ['controller' => 'Objects', 'action' => 'related'],
            ['_name' => 'objects:related']
        );
        $routes->connect(
            '/:object_type/:id/relationships/:relationship',
            ['controller' => 'Objects', 'action' => 'relationships'],
            ['_name' => 'objects:relationships']
        );
    }
);

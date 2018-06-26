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
            'roles',
            'streams',
            'users',
            'media',
            'folders',
            'translations',
        ];
        $adminControllers = [
            'applications',
            'async_jobs',
            'config',
            'endpoints',
        ];
        $modelingControllers = [
            'object_types',
            'properties',
            'property_types',
            'relations',
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
        // GET /auth *deprecated* - to remove before `stable` relase
        $routes->connect(
            '/auth',
            ['controller' => 'Login', 'action' => 'whoami', '_method' => 'GET']
        );
        $routes->connect(
            '/auth/user',
            ['controller' => 'Login', 'action' => 'whoami', '_method' => 'GET'],
            ['_name' => 'login:whoami']
        );
        $routes->connect(
            '/auth/user',
            ['controller' => 'Login', 'action' => 'update', '_method' => 'PATCH'],
            ['_name' => 'login:update']
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

        // Upload and thumbnails.
        $routes->connect(
            '/streams/upload/:fileName',
            ['controller' => 'Streams', 'action' => 'upload'],
            ['_name' => 'streams:upload', 'pass' => ['fileName']]
        );
        $routes->connect(
            '/media/thumbs/:id',
            ['controller' => 'Media', 'action' => 'thumbs'],
            ['_name' => 'media:thumbs', 'pass' => ['id']]
        );
        $routes->connect(
            '/media/thumbs',
            ['controller' => 'Media', 'action' => 'thumbs'],
            ['_name' => 'media:thumbs:multiple']
        );

        $resourcesRoutes = function (array $controllers) {
            $controller = implode('|', $controllers);

            return function (RouteBuilder $routes) use ($controller) {
                $routes->connect(
                    '/:controller',
                    ['action' => 'index'],
                    ['_name' => 'resources:index'] + compact('controller')
                );
                $routes->connect(
                    '/:controller/:id',
                    ['action' => 'resource'],
                    ['_name' => 'resources:resource', 'pass' => ['id']] + compact('controller')
                );
                $routes->connect(
                    '/:controller/:related_id/:relationship',
                    ['action' => 'related'],
                    ['_name' => 'resources:related'] + compact('controller')
                );
                $routes->connect(
                    '/:controller/:id/relationships/:relationship',
                    ['action' => 'relationships'],
                    ['_name' => 'resources:relationships'] + compact('controller')
                );
            };
        };

        // Admin endpoints.
        $routes->prefix(
            'admin',
            [
                '_namePrefix' => 'admin:',
            ],
            $resourcesRoutes($adminControllers)
        );

        // Modeling endpoints.
        $routes->prefix(
            'model',
            [
                '_namePrefix' => 'model:',
            ],
            function (RouteBuilder $routes) use ($modelingControllers, $resourcesRoutes) {
                $callback = $resourcesRoutes($modelingControllers);
                $callback($routes);
                $routes->connect(
                    '/schema/:type',
                    ['controller' => 'Schema', 'action' => 'jsonSchema'],
                    ['_name' => 'schema', 'pass' => ['type']]
                );
            }
        );

        // Resources.
        $callback = $resourcesRoutes($resourcesControllers);
        $callback($routes);

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

        // Config.
        $routes->connect(
            '/config',
            ['controller' => 'Config', 'action' => 'index', '_method' => 'GET'],
            ['_name' => 'config:index']
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

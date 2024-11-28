<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017-2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes) {
    $routes->plugin('BEdita/API', ['path' => '/','_namePrefix' => 'api:'], function (RouteBuilder $routes) {
        $resourcesControllers = [
            'config',
            'roles',
            'history',
            'streams',
            'users',
            'media',
            'folders',
            'translations',
            'annotations',
            'object_permissions',
        ];
        $adminControllers = [
            'applications',
            'async_jobs',
            'config',
            'endpoints',
            'endpoint_permissions',
        ];
        $modelingControllers = [
            'object_types',
            'properties',
            'property_types',
            'relations',
            'categories',
            'tags',
        ];
        $routes->setRouteClass(InflectedRoute::class);

        // Home.
        $routes->redirect(
            '/',
            '/home',
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
        $routes->post(
            '/auth',
            ['controller' => 'Login', 'action' => 'login'],
            'login'
        );
        $routes->post(
            '/auth/optout',
            ['controller' => 'Login', 'action' => 'optout'],
            'login:optout'
        );
        $routes->connect(
            '/auth/change',
            ['controller' => 'Login', 'action' => 'change'],
            ['_name' => 'login:change']
        );
        $routes->get(
            '/auth/user',
            ['controller' => 'Login', 'action' => 'whoami'],
            'login:whoami',
        );
        $routes->patch(
            '/auth/user',
            ['controller' => 'Login', 'action' => 'update'],
            'login:update'
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
            '/streams/upload/{fileName}',
            ['controller' => 'Streams', 'action' => 'upload'],
            ['_name' => 'streams:upload', 'pass' => ['fileName']]
        );
        $routes->connect(
            '/streams/clone/{uuid}',
            ['controller' => 'Streams', 'action' => 'clone'],
            ['_name' => 'streams:clone']
        )->setPass(['uuid']);
        $routes->connect(
            '/media/thumbs/{id}',
            ['controller' => 'Media', 'action' => 'thumbs'],
            ['_name' => 'media:thumbs', 'pass' => ['id']]
        );
        $routes->connect(
            '/media/thumbs',
            ['controller' => 'Media', 'action' => 'thumbs'],
            ['_name' => 'media:thumbs:multiple']
        );
        // Download
        $routes->connect(
            '/streams/download/{uuid}',
            ['controller' => 'Streams', 'action' => 'download'],
            ['_name' => 'streams:download']
        )
        ->setPass(['uuid']);

        $resourcesRoutes = function (array $controllers) {
            $controller = implode('|', $controllers);

            return function (RouteBuilder $routes) use ($controller) {
                $routes->connect(
                    '/{controller}',
                    ['action' => 'index'],
                    ['_name' => 'resources:index'] + compact('controller')
                );
                $routes->connect(
                    '/{controller}/{id}',
                    ['action' => 'resource'],
                    ['_name' => 'resources:resource', 'pass' => ['id']] + compact('controller')
                );
                $routes->connect(
                    '/{controller}/{related_id}/{relationship}',
                    ['action' => 'related'],
                    ['_name' => 'resources:related'] + compact('controller')
                );
                $routes->connect(
                    '/{controller}/{id}/relationships/{relationship}',
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
            function (RouteBuilder $routes) use ($adminControllers, $resourcesRoutes) {
                $callback = $resourcesRoutes($adminControllers);
                $callback($routes);
                $routes->connect(
                    '/sysinfo',
                    ['controller' => 'Sysinfo', 'action' => 'index'],
                    ['_name' => 'sysinfo']
                );
            }
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
                    '/schema/{type}',
                    ['controller' => 'Schema', 'action' => 'jsonSchema'],
                    ['_name' => 'schema', 'pass' => ['type']]
                );
                $routes->connect(
                    '/project',
                    ['controller' => 'Project', 'action' => 'index'],
                    ['_name' => 'project']
                );
            }
        );

        // Resources.
        $callback = $resourcesRoutes($resourcesControllers);
        $callback($routes);

        // Trash.
        $routes->connect(
            '/trash',
            ['controller' => 'Trash', 'action' => 'index', '_method' => ['GET', 'DELETE']],
            ['_name' => 'trash:index']
        );
        $routes->connect(
            '/trash/{id}',
            ['controller' => 'Trash', 'action' => 'view', '_method' => 'GET'],
            ['_name' => 'trash:resource', 'pass' => ['id']]
        );
        $routes->connect(
            '/trash/{id}',
            ['controller' => 'Trash', 'action' => 'restore', '_method' => 'PATCH'],
            ['_name' => 'trash:restore', 'pass' => ['id']]
        );
        $routes->connect(
            '/trash/{id}',
            ['controller' => 'Trash', 'action' => 'delete', '_method' => 'DELETE'],
            ['_name' => 'trash:delete', 'pass' => ['id']]
        );

        // Applications.
        $routes->connect(
            '/applications',
            ['controller' => 'Applications', 'action' => 'index', '_method' => 'GET'],
            ['_name' => 'applications:index']
        );

        // Async jobs.
        $routes->connect(
            '/async_jobs',
            ['controller' => 'AsyncJobs', 'action' => 'index'],
            ['_name' => 'async_jobs:index']
        );

        // Trees.
        $routes->connect(
            '/trees/**',
            ['controller' => 'Trees', 'action' => 'index', '_method' => 'GET'],
            ['_name' => 'trees:index']
        );

        // Upload file and create object.
        $routes->connect(
            '/{object_type}/upload/{fileName}',
            ['controller' => 'Upload', 'action' => 'upload'],
            ['_name' => 'objects:upload', 'pass' => ['fileName']]
        );

        // Objects.
        $routes->connect(
            '/{object_type}',
            ['controller' => 'Objects', 'action' => 'index'],
            ['_name' => 'objects:index']
        );
        $routes->connect(
            '/{object_type}/{id}',
            ['controller' => 'Objects', 'action' => 'resource'],
            ['_name' => 'objects:resource', 'pass' => ['id']]
        );
        $routes->connect(
            '/{object_type}/{related_id}/{relationship}',
            ['controller' => 'Objects', 'action' => 'related'],
            ['_name' => 'objects:related']
        );
        $routes->connect(
            '/{object_type}/{id}/actions/clone',
            ['controller' => 'Objects', 'action' => 'clone'],
            ['_name' => 'objects:clone', 'pass' => ['id']]
        );
        $routes->connect(
            '/{object_type}/{id}/relationships/{relationship}',
            ['controller' => 'Objects', 'action' => 'relationships'],
            ['_name' => 'objects:relationships']
        );
        $routes->connect(
            '/{object_type}/{id}/relationships/{relationship}/sort',
            ['controller' => 'Objects', 'action' => 'relationshipsSort'],
            ['_name' => 'objects:relationshipsSort']
        );
    });
};

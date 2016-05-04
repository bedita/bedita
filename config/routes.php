<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'BEdita/API',
    ['path' => '/'],
    function (RouteBuilder $routes) {
        $routes->connect('/users', ['controller' => 'Users', 'action' => 'index']);
        $routes->connect('/users/*', ['controller' => 'Users', 'action' => 'view']);

        $routes->fallbacks('DashedRoute');
    }
);

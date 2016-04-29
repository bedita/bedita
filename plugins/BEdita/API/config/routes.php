<?php
use Cake\Routing\Router;

Router::plugin(
    'BEdita/API',
    ['path' => '/'],
    function ($routes) {

        $routes->connect('/users/*', ['controller' => 'Users', 'action' => 'index']);

        $routes->fallbacks('DashedRoute');
    }
);

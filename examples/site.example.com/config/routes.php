<?php

	// specific publication rules
	// first active section on the publication's tree is used as home page
	Router::connect('/', array('controller' => 'pages', 'action' => 'route', 'homePage'));
	
	// DO NOT EDIT OR CHANGE BELOW!!
	Router::connect('/lang/*', array('controller' => 'pages', 'action' => 'changeLang'));
	Router::connect('/section/*', array('controller' => 'pages', 'action' => 'section'));
	Router::connect('/content/*', array('controller' => 'pages', 'action' => 'content'));
    Router::connect('/*', array('controller' => 'pages', "action" => "route"));
?>
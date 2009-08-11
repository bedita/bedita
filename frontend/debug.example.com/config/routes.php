<?php
	// specific publication routing rules
	// normally you need to setup only default/home page section id
	Router::connect('/', array('controller' => 'pages', 'action' => 'section', '1'));
	
	// DO NOT EDIT OR CHANGE BELOW!!
	Router::connect('/lang/*', array('controller' => 'pages', 'action' => 'changeLang'));
	Router::connect('/section/*', array('controller' => 'pages', 'action' => 'section'));
	Router::connect('/content/*', array('controller' => 'pages', 'action' => 'content'));
    Router::connect('/(?!pages)(.*)', array('controller' => 'pages', "action" => "route"));
?>
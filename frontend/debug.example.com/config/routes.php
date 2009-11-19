<?php
	// specific publication routing rules
	// normally you need to setup only default/home page section id
	Router::connect('/', array('controller' => 'pages', 'action' => 'index'));
	
	// DO NOT EDIT OR CHANGE BELOW!!
	Router::connect('/lang/*', array('controller' => 'pages', 'action' => 'changeLang'));
	Router::connect('/section/*', array('controller' => 'pages', 'action' => 'section'));
	Router::connect('/content/*', array('controller' => 'pages', 'action' => 'content'));
    Router::connect('/(?!pages)(.*)', array('controller' => 'pages', "action" => "route"));
	
	/**
	 * feeds
	*/ 
    Router::connect('/rss/*', array('controller' => 'pages', 'action' => 'rss'));
    Router::connect('/feed/*', array('controller' => 'pages', 'action' => 'rss'));
	

?>
<?php
/* SVN FILE: $Id: bootstrap.php 4410 2007-02-02 13:31:21Z phpnut $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.10.8.2117
 * @version			$Revision: 4410 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 07:31:21 -0600 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */

$modelPaths = array();
$controllerPaths = array();
$componentPaths = array();
$behaviorPaths = array();
$helperPaths = array();
$pluginPaths = array();

$excludedDirs = array("behaviors", "datasources", "components");

function enableSubFoldersOn($baseDir, &$var, &$exclude) {         
  $cwd =getcwd();
  chdir($baseDir);
  $dirs = glob("*", GLOB_ONLYDIR);  
  if(sizeof($dirs) > 0) { 
    foreach($dirs as $dir) { 
      if(!in_array($dir, $exclude)) {
    	$var[] = $baseDir . DS . $dir . DS;
       	enableSubFoldersOn($baseDir . DS .$dir, $var, $exclude) ;
      }
    }
  }
  chdir($cwd);
}


// backend specific bootstrap
if (!defined("BEDITA_CORE_PATH")) {
	define("BEDITA_CORE_PATH", ROOT . DS . APP_DIR);
	define("BACKEND_APP", true);
	$controllerPaths = array();
	enableSubFoldersOn(BEDITA_CORE_PATH .DS . 'controllers', $controllerPaths, $excludedDirs);
	
	function shutdownTransation() {
		if(Configure::read("bedita.transaction") != null) {
			App::import('Component','Transaction');
			$Transaction = new TransactionComponent();
			$Transaction->init() ;
			$Transaction->rollback() ;
		}
	}
	
	// Register transaction shutdown function
	register_shutdown_function('shutdownTransation');
	
	/**
	 ** Load BEdita settings and define constants
	 */
	
	// load defaults
	Configure::load("bedita.ini") ;
	
	// load local installation specific settings
	if ( file_exists (BEDITA_CORE_PATH . DS . "config" . DS . "bedita.cfg.php") ) {
		Configure::load("bedita.cfg") ;	
	}

// frontends specific bootstrap
} else {
	define("BACKEND_APP", false);
	$modelPaths[]=BEDITA_CORE_PATH . DS . 'models' . DS;
	$viewPaths=array(BEDITA_CORE_PATH . DS . 'views' . DS);
	$componentPaths[] = BEDITA_CORE_PATH . DS . 'controllers' . DS . 'components' . DS;
	$behaviorPaths[] = BEDITA_CORE_PATH . DS . 'models' . DS . 'behaviors' . DS;
	$helperPaths[] = BEDITA_CORE_PATH . DS . 'views' . DS . 'helpers' . DS;
	Configure::load("frontend.ini") ;
}

/**
 * backend and frontend commons bootstrap operations
 */
if (!defined("BEDITA_ADDONS_PATH")) {
	define("BEDITA_ADDONS_PATH", BEDITA_CORE_PATH . DS . ".." . DS . 'addons');
}

if (!defined("BEDITA_MODULES_PATH")) {
	define("BEDITA_MODULES_PATH", BEDITA_CORE_PATH . DS . ".." . DS . 'modules');
}

if (!defined("BEDITA_FRONTENDS_PATH")) {
	define("BEDITA_FRONTENDS_PATH", BEDITA_CORE_PATH . DS . ".." . DS . 'frontends');
}

// add addons models, components and helpers path
if (is_dir(BEDITA_ADDONS_PATH . DS . 'models')) {
	$modelPaths[] = BEDITA_ADDONS_PATH . DS . 'models' . DS;
}
if (is_dir(BEDITA_ADDONS_PATH . DS . 'components')) {
	$componentPaths[] = BEDITA_ADDONS_PATH . DS . 'components' . DS;
}
if (is_dir(BEDITA_ADDONS_PATH . DS . 'helpers')) {
	$helperPaths[] = BEDITA_ADDONS_PATH . DS . 'helpers' . DS;
}

if (BACKEND_APP) {
	$pluginPaths[] = BEDITA_MODULES_PATH . DS;
}

enableSubFoldersOn(BEDITA_CORE_PATH . DS . 'models', $modelPaths, $excludedDirs);

// common bedita libs path
define('BEDITA_LIBS', BEDITA_CORE_PATH . DS .'libs');

// common exceptions definitions
require_once BEDITA_CORE_PATH . DS . "bedita_exception.php";

//EOF
?>
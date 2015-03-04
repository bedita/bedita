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
 *                              1785 E. Sahara Avenue, Suite 490-204
 *                              Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright       Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link                http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package         cake
 * @subpackage      cake.app.config
 * @since           CakePHP(tm) v 0.10.8.2117
 * @version         $Revision: 4410 $
 * @modifiedby      $LastChangedBy: phpnut $
 * @lastmodified    $Date: 2007-02-02 07:31:21 -0600 (Fri, 02 Feb 2007) $
 * @license         http://www.opensource.org/licenses/mit-license.php The MIT License
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

$additionalPaths =  array(
    'models' => array(),
    'behaviors' => array(),
    'datasources' => array(),
    'controllers' => array(),
    'components' => array(),
    'libs' => array(),
    'views' => array(),
    'helpers' => array(),
    'locales' => array(),
    'shells' => array(),
    'vendors' => array(),
    'plugins' => array()
);

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
	define('BEDITA_LOCAL_CFG_PATH', BEDITA_CORE_PATH . DS .'config' . DS . 'local');
	enableSubFoldersOn(BEDITA_CORE_PATH .DS . 'controllers', $additionalPaths["controllers"], $excludedDirs);
	
	// load BEdita configuration
	// bedita.ini.php, bedita.cfg.php
	require_once(CONFIGS . 'bedita.ini.php');	

// frontends specific bootstrap
} else {
    define('BEDITA_LOCAL_CFG_PATH', BEDITA_CORE_PATH . DS .'config' . DS . 'local');
    define("BACKEND_APP", false);

    foreach ($additionalPaths as $keyPath => $val) {
        $additionalPaths[$keyPath] = App::path($keyPath);
    }
    $additionalPaths["models"][] = BEDITA_CORE_PATH . DS . 'models' . DS;
    $additionalPaths["views"][] = BEDITA_CORE_PATH . DS . 'views' . DS;
    $additionalPaths["components"][] = BEDITA_CORE_PATH . DS . 'controllers' . DS . 'components' . DS;
    $additionalPaths["behaviors"][] = BEDITA_CORE_PATH . DS . 'models' . DS . 'behaviors' . DS;
    $additionalPaths["helpers"][] = BEDITA_CORE_PATH . DS . 'views' . DS . 'helpers' . DS;
    $additionalPaths["libs"][] = BEDITA_CORE_PATH . DS .'libs';
    // frontend.ini.php, includes bedita.ini/cfg/sys
    require_once(CONFIGS . 'frontend.ini.php');
}

function shutdownBEditaApp() {
    if (BACKEND_APP && Configure::read('bedita.transaction') != null) {
        App::import('Component','Transaction');
        $Transaction = new TransactionComponent();
        $Transaction->init() ;
        $Transaction->rollback() ;
    }

    $error = error_get_last();
    if (!empty($error) && $error['type'] == E_ERROR) {
        $msg = $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line'];
        $controller = null;
        if (class_exists('AppController')) {
            $controller = AppController::currentController();
            if (!empty($controller)) {
                $controller->handleError($msg, $msg);
            }
        }
        if (empty($controller) && class_exists('CakeLog')) {
            CakeLog::write('error', $msg);
        }
    }
}

// Register shutdown function to track cake errors
register_shutdown_function('shutdownBEditaApp');

Configure::write($config);

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

// add addons models, components, helpers and vendors path
if (is_dir(BEDITA_ADDONS_PATH . DS . 'models' . DS . 'enabled')) {
    $additionalPaths["models"][] = BEDITA_ADDONS_PATH . DS . 'models' . DS . 'enabled' . DS;
}
if (is_dir(BEDITA_ADDONS_PATH . DS . 'models' . DS . 'behaviors' . DS . 'enabled')) {
    $additionalPaths["behaviors"][] = BEDITA_ADDONS_PATH . DS . 'models' . DS . 'behaviors' . DS . 'enabled'. DS;
}
if (is_dir(BEDITA_ADDONS_PATH . DS . 'components' . DS . 'enabled')) {
    $additionalPaths["components"][] = BEDITA_ADDONS_PATH . DS . 'components' . DS . 'enabled'. DS;
}
if (is_dir(BEDITA_ADDONS_PATH . DS . 'helpers' . DS . 'enabled')) {
    $additionalPaths["helpers"][] = BEDITA_ADDONS_PATH . DS . 'helpers' . DS . 'enabled'. DS;
}
if (is_dir(BEDITA_ADDONS_PATH . DS . 'vendors')) {
    $additionalPaths["vendors"][] = BEDITA_ADDONS_PATH . DS . 'vendors' . DS;
}

enableSubFoldersOn(BEDITA_CORE_PATH . DS . 'models', $additionalPaths["models"], $excludedDirs);

if (BACKEND_APP) {
    $additionalPaths["plugins"][] = BEDITA_MODULES_PATH . DS;
// reorder frontend paths (first watch frontend app paths then bedita core paths and finally cake core paths)
} else {
    $additionalPaths["plugins"][] = BEDITA_CORE_PATH . DS . 'plugins' . DS;
    $cakeCorePaths = App::core();
    foreach ($additionalPaths as $type => $paths) {
        if (!empty($cakeCorePaths[$type])) {
            $additionalPaths[$type] = array_diff($paths, $cakeCorePaths[$type]);
        }
    }
}

// add paths to cakePHP
App::build($additionalPaths);

// common exceptions definitions
require_once BEDITA_CORE_PATH . DS . "bedita_exception.php";

// #398 - check proxy options
$proxyOpts = Configure::read("proxyOptions");
if (!empty($proxyOpts)) {
    $aContext = array(
        'http' => array(
            'proxy' => $proxyOpts["host"],
            'request_fulluri' => true,
        ),
    );
    if (!empty($proxyOpts["auth"])) {
        $aContext['http']['header'] = "Proxy-Authorization: Basic " .
            base64_encode($proxyOpts["auth"]);
    }
    stream_context_set_default($aContext);
}

$isCli = (php_sapi_name() === 'cli');

if (!$isCli) {
    // setup exception handler
    $exception = Configure::read('Exception.handler');
    if (!empty($exception['class']) && !empty($exception['method'])) {
        App::import('Lib', $exception['class']);
        if (class_exists($exception['class'])) {
            $exceptionHandler = array($exception['class'], $exception['method']);
        }
    }

    // default exception handler
    if (empty($exceptionHandler)) {
        App::import('Lib', 'BeExceptionHandler');
        $exceptionHandler = array('BeExceptionHandler', 'handleExceptions');
    }

    set_exception_handler($exceptionHandler);
}

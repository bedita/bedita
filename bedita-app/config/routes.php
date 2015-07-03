<?php
/**
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 4410 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 07:31:21 -0600 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

	Router::connect('/', array('controller' => 'home', 'action' => 'index'));
	Router::connect('/logout', array('controller' => 'authentications', 'action' => 'logout'));
	// generic view
	Router::connect('/view/*', array('controller' => 'home', 'action' => 'view'));
	
/**
 * route to switch locale
 */
	Router::connect('/lang/*', array('controller' => 'pages', 'action' => 'changeLang'));

/**
 * route for module plugins (controller must have the same name of module)
 * example:
 *		module name: sample_module
 *		controller file name: sample_module_controller.php
 */
	$confCached = Cache::read('beConfig');
	if (empty($confCached['plugged']['modules'])) {
        App::import('Core', 'Folder');
		$folder = new Folder(BEDITA_MODULES_PATH);
		$list = $folder->read();
		$listModules = $list[0];
	} else {
		$listModules = array_keys($confCached['plugged']['modules']);
	}

	foreach ($listModules as $moduleName) {
		Router::connect(
			'/' . $moduleName . '/:action/*', array('plugin' => $moduleName, 'controller' => $moduleName)
		);
	}
?>
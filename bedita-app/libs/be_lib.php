<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

if (!class_exists('ClassRegistry')) {
	App::import('Core', array('ClassRegistry'));
}

/**
 * BEdita libs class. Instantiate and put in the registry other classes
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

class BeLib {
	
	public static function &getInstance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] =& new BeLib();
		}
		return $instance[0];
	}
	
	/**
	 * return an instance of a class (by default search in libs dir)
	 * If class is not instantiated do it and put in CakePHP registry
	 * 
	 * @param string $name class name (file has to be underscorized MyClass => my_class.php)
	 * @param string or array $paths paths where search class file
	 * @return class instance
	 */
	public static function &getObject($name, $paths=BEDITA_LIBS) {
		if (!$libObject = ClassRegistry::getObject($name)) {
			if (!class_exists($name)) {
				$file = Inflector::underscore($name) . ".php";
				$paths = (is_array($paths))? $paths : array($paths);
				if (!App::import("File", $name, true, $paths, $file)) {
					return false;
				}
			}
			$libObject = new $name();
			ClassRegistry::addObject($name, $libObject);
		}
		return $libObject;
	}
	
}

?>
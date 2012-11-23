<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * Type object
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class ObjectType extends BEAppModel
{
	var $name = 'ObjectType';
	
	/**
	 * return an unused id for module plugin and addons
	 * @return int
	 */
	public function newPluggedId() {
		$maxid = $this->field("id", null, "id DESC");
		$ot_id = ($maxid < 1000)? 1000 : $maxid + 1;
		return $ot_id;
	}
	
	/**
	 * purge object type and related objects
	 * @param string $objectType
	 * @return void
	 */
	public function purgeType($objectType) {
		$ot_id = $this->field("id", array("name" => $objectType));
		if (empty($ot_id)) {
			throw new BeditaException(__("Object type not found: " . $objectType, true));
		}

		// delete all objects
		$beObject = ClassRegistry::init("BEObject");
		if (!$beObject->deleteAll(array("object_type_id" => $ot_id))) {
			throw new BeditaException(__("Error deleting objects " . Inflector::camelize($objectType), true));
		}
		// delete object type
		if (!$this->delete($ot_id)) {
			throw new BeditaException(__("Error deleting object type " . $objectType, true));
		}
	} 
	
	/**
	 * Get list of object types id given a module names list
	 *
	 * @param array $modules
	 * @return array, object types id
	 */
	function typesIdFromModules(array &$modules) {
		$res = array();
		$types = $this->find("all", array(
				"conditions" =>	array("module_name" => $modules),
				"fields" => array("id"),
		));
		foreach ($types as $t) {
			$res[] = $t["ObjectType"]["id"];
		}
		return $res;		
	}

}
?>
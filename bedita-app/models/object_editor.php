<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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
 * Object Editor
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class ObjectEditor extends BEAppModel
{
	
	public $belongsTo = array('User') ;
	
	/**
	 * Creates a new record or updates last access for a user/editor on an object
	 *
	 * @param unknown_type $object_id
	 * @param unknown_type $user_id
	 */
	public function updateAccess($object_id, $user_id) {
		
		$data = array("object_id" => $object_id, "user_id" => $user_id);
		$id = $this->field("id", $data);
		if(!empty($id)) {
			$this->id = $id;
			$this->saveField("last_access", date("Y-m-d H:i:s"));
		} else {
            $data += array('last_access' => date('Y-m-d H:i:s'));
            $this->create($data);
			if (!$this->save()) {
				throw new BeditaException(__("Error saving object editor", true), $this->validationErrors);
			}
		}
	}

	/**
	 * Remove old/expired items
	 */
	public function cleanup($object_id) {
		$ts = time() - 2 * Configure::read("concurrentCheckTime") / 1000;
		$this->deleteAll(array("last_access < '" . date("Y-m-d H:i:s", $ts) . "'", 
			"object_id" => $object_id));
	}
	
	
	/**
	 * Load current editors
	 */
	public function loadEditors($object_id) {

		return $this->find("all", array(
				"conditions" => array("object_id" => $object_id)
			)
		);
	}
	
}
?>
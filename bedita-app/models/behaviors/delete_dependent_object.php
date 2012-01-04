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

/**
 * Behavior to remove descendants of the object deleted 
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class DeleteDependentObjectBehavior extends ModelBehavior {
	var $config = array();
	private $descendants = array();
	
	function setup(&$model, $config = array()) {
		$this->config[$model->name] = $config ; // object type to delete
	}

	/**
	 * find the descendants to delete
	 */
	function beforeDelete(&$model) {
		// If no object types, return
		if(!count($this->config[$model->name])) return ;
		
		$filter = array() ;
		$conf  = Configure::getInstance() ;
		
		foreach ($this->config[$model->name] as $type) {
			if(!is_array($filter)) $filter = array() ;
			$filter["object_type_id"][] = $conf->objectTypes[Inflector::underscore($type)]["id"] ;
		}
		
		// get descendants
		$this->descendants = $model->findObjects($model->id, null, null, $filter, "priority", true, 1, null, true);
		
		return true ;
	}
	
	/**
	 * delete the descendants found previously
	 */
	public function afterDelete(&$model) {
		if (!empty($model->tmpTable))
			$model->table = $model->tmpTable;
		
		if (!empty($this->descendants["items"])) {
			foreach ($this->descendants["items"] as $item) {
				$modelDescName = Configure::read("objectTypes.".$item["object_type_id"].".model");
				if(!ClassRegistry::init($modelDescName)->delete($item["id"])) {
					throw new BeditaException(__("Error deleting depending object " . $item["title"], true));
				}
			}
		}
	}
	
}
?>
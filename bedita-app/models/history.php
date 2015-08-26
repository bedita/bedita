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
 * User access history to contents
 * Model is not a proper BEdita object, model data in history table
 */
class History extends BEAppModel {

	public $useTable = "history";
	
	public $belongsTo = array(
		"BEObject" => array(
			"foreignKey" => "object_id"
		),
		"User", "Area"
	);
	
	/**
	 * get user history in desc order
	 * 
	 * @param int $user_id
	 * @param int $limit
	 * @param array $group contains fields to group by
	 * @param int $areaId publication area id, all publications if not set
     * @param string $order possible options ASC, DESC. Default DESC.
	 * 
	 * @return array of history in the form 
	 * 		   array(
	 * 				0 => array(id => , user_id => , ...),
	 * 				1 => array(id => , user_id => , ...),
	 * 				...
	 * 				) 
	 */
	public function getUserHistory($user_id, $limit=null, $group=array(), $areaId=null, $order="DESC") {
		$conditions["user_id"] = $user_id;
		if($areaId) {
			$conditions["area_id"] = $areaId;
		}
		$history = $this->find("all", array(
				"conditions" => $conditions,
				"limit" => $limit,
				"group" => $group,
				"order" => "History.created ".$order,
				"contain" => array()
			)
		);
		
		return Set::extract("{n}.History", $history);
	}
	
}

?>
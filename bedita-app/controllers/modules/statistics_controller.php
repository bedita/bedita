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

 */
class StatisticsController extends ModulesController {
	var $name = 'Statistics';

	var $helpers 	= array('BeTree', 'BeToolbar');

	var $uses = array('BEObject','Tree','User') ;
	protected $moduleName = 'statistics';
	
	public function index($id=null) {
		
		$params = array();
		
		if (!empty($this->passedArgs["id"]))
			$id = $this->passedArgs["id"];
		
		// get section parents
		if (!empty($id)) {
			$parentArr[] = $id;
			$discendents = $this->BeTree->getDiscendents($id, null, array("object_type_id" => Configure::read("objectTypes.section.id")));
			
			if (!empty($discendents["items"])) {
				foreach($discendents["items"] as $item) {
					$parentArr[] = $item["id"];
				}		
			}
			
			$params["conditions"]["Tree.parent_id"] = $parentArr;	
			
			$this->BEObject->bindModel(array(
					"hasOne" => array(
						"Tree" => array('foreignKey' => 'id')
					)
				)
			);
			
			$params["contain"] = array("Tree", "ObjectType");
		} else {
			$params = array("contain" => array("ObjectType"), "conditions" => array());
		}
		
		// number of objects
		$this->totalObjects($params);
		
		// time evolution (get last five months)
		$this->timeEvolution($params);
		
		// count objects' for user
		$this->objectsForUser($params);
		
		// count comment
		$this->countRelations(array_merge($params, array("id" => $id, "relation" => "comment")));
		
		// count objects' relations
		$this->countRelations(array_merge($params, array("id" => $id)));
		
		$this->set('tree', $this->BeTree->getSectionsTree());
	 }
	
	 
	 private function totalObjects($params) {
	 	// number of objects
		$countTotal = $this->BEObject->find("all", array(
					"fields" => "COUNT(DISTINCT `BEObject`.id) as count, `ObjectType`.id, `ObjectType`.name",
					"conditions" => $params["conditions"],
					"contain" => $params["contain"],
					"group" => "`ObjectType`.id, `ObjectType`.name",
					"order" => "count DESC"	
				)
			);
		foreach ($countTotal as $c) {
			$totalObj[$c["ObjectType"]["name"]] = $c[0]["count"];
		}

		$this->set("totalObjectsNumber", $totalObj);
		$this->set("maxTotalObjectsNumber", max($totalObj));
	 }
	 
	 
	 private function timeEvolution($params) {
	 	$timeEvolution = array();
	 	$totalTimeEvolution = array();
	 	for ($i = 0; $i < 5; $i++) {
			$firstDayMonthTS = mktime(0, 0, 0, date("m")-$i, 1, date("Y"));
			$lastDayMonthTS = mktime(0, 0, 0, date("m")-$i+1, 0, date("Y"));
			$firstDayMonth = date("Y-m-d", $firstDayMonthTS);
			$lastDayMonth = date("Y-m-d", $lastDayMonthTS);
			
			$conditionsEvol = array_merge($params["conditions"], array("`BEObject`.created BETWEEN '" . $firstDayMonth . "' AND '" . $lastDayMonth ."'"));
			
			$countEvol = $this->BEObject->find("all", array(
					"fields" => "COUNT(DISTINCT `BEObject`.id) as count, `ObjectType`.id, `ObjectType`.name",
					"conditions" => $conditionsEvol,
					"contain" => $params["contain"],
					"group" => "`ObjectType`.id, `ObjectType`.name"	
				)
			);

			$totalEvolMonth = 0;
			foreach ($countEvol as $c) {
				$timeEvolution[$firstDayMonth][$c["ObjectType"]["name"]] = $c[0]["count"];
				$totalEvolMonth += $c[0]["count"];
			}
			$totalTimeEvolution[$firstDayMonth] = $totalEvolMonth;
			
		}
		
		$this->set("timeEvolution", $timeEvolution);
		$this->set("totalTimeEvolution", $totalTimeEvolution);
		$this->set("maxTotalTimeEvolution", max($totalTimeEvolution));
		
	 }
	 
	 private function countRelations($params) {
	 	
	 	$rel = array();
	 	$max = 1;
		$conditionsRel = $params["conditions"];
		if (!empty($params["relation"])) { 	 	
		 	$conditionsRel = array_merge(
		 		$conditionsRel, array("`RelatedObject`.switch" => $params["relation"])
	 		);
	 		if ($params["relation"] == "comment")
	 			$conditionsRel[] = "`BEObject`.object_type_id <> " . Configure::read('objectTypes.comment.id');
		}
 		if (!empty($params["id"])) {
		 	$bind = array(
				"belongsTo" => array(
					"BEObject" => array("foreignKey" => "id"),
	 				"Tree" => array('foreignKey' => 'id')
	 			)
	 		);
	 		$contain = array("Tree","BEObject" => array("ObjectType"));
 		} else {
 			$bind = array(
				"belongsTo" => array(
					"BEObject" => array("foreignKey" => "id")
		 		)
		 	);
		 	$contain = array("BEObject" => array("ObjectType"));
 		}
 		
 		$this->BEObject->RelatedObject->bindModel($bind);
 		
	 	$countRel = $this->BEObject->RelatedObject->find("all", array(
					"fields" => "COUNT(DISTINCT `RelatedObject`.object_id) as count_relations, `BEObject`.id",
					"conditions" => $conditionsRel,
					"group" => "`BEObject`.id",
	 				"limit" => 20,
	 				"order" => "count_relations DESC",
	 				"contain" => $contain
				)
			);

		foreach ($countRel as $key => $item) {
			$rel[] = array_merge($item["BEObject"], $item[0]);
			if ($key == 0)
				$max = $item[0]["count_relations"];
		}
		
		if (!empty($params["relation"])) {
			$this->set("contentCommented", $rel);
			$this->set("maxContentCommented", $max);
		} else {
			$this->set("relatedObject", $rel);
			$this->set("maxRelatedObject", $max);
		}
	 }

	 
	 private function objectsForUser($params) {
	 	
	 	$this->User->contain();
	 	$users = $this->User->find("all");
	 	
	 	foreach ($users as $k => $u) {
	 		
	 		$objects = $this->BEObject->find("all", array(
					"fields" => "COUNT(DISTINCT `BEObject`.id) as count, `ObjectType`.id, `ObjectType`.name",
					"conditions" => array_merge( $params["conditions"], array("user_created" => $u["User"]["id"]) ),
					"contain" => $params["contain"],
					"group" => "`ObjectType`.id, `ObjectType`.name",
					"order" => "count DESC"	
				)
			);

			$obj["objects"] = array();
			$totalObjects = 0;
		 	foreach ($objects as $c) {
				$obj["objects"][$c["ObjectType"]["name"]] = $c[0]["count"];
				$totalObjects += $c[0]["count"];
			}
			$objectsForUser[$u["User"]["id"]] = array_merge($u["User"], $obj);
			$totalObjectsForUser[$u["User"]["id"]] = $totalObjects;
	 	}
	 	
	 	$this->set("objectsForUser", $objectsForUser);
		$this->set("totalObjectsForUser", $totalObjectsForUser);
		$this->set("maxObjectsForUser", max($totalObjectsForUser));
		
	 }
	 
}	

?>
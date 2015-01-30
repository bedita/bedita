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
			$discendents = $this->BeTree->getDescendants($id, null, array("object_type_id" => Configure::read("objectTypes.section.id")));
			
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
				), false
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
		
		$user = $this->BeAuth->getUserSession();
		$expandBranch = array();
        if (!empty($id)) {
            $expandBranch[] = $id;
        }
        $treeModel = ClassRegistry::init("Tree");
        $tree = $treeModel->getAllRoots($user['userid'], null, array('count_permission' => true), $expandBranch);
		$this->set('tree', $tree);
		
		// publications
		$area = $this->loadModelByType("Area");
		$area->containLevel('default');
		$this->set('publications', $area->find('all'));
		
		//usersStuff
		$this->usersStuff($params);
	 }
	
		public function view() {
			$this->action = "index";
			$this->index($this->passedArgs["id"]);
		}

		private function totalObjects($params) {
	 		// number of objects
			$s = $this->BEObject->getStartQuote();
			$e = $this->BEObject->getEndQuote();
			$countTotal = $this->BEObject->find("all", array(
					"fields" => array("COUNT(DISTINCT {$s}BEObject{$e}.{$s}id{$e}) as count", "ObjectType.id", "ObjectType.name"),
					"conditions" => $params["conditions"],
					"contain" => $params["contain"],
					"group" => array("ObjectType.id", "ObjectType.name"),
					"order" => "count DESC"	
				)
			);
		$totalObj = array();
		foreach ($countTotal as $c) {
			$totalObj[$c["ObjectType"]["name"]] = $c[0]["count"];
		}

		$this->set("totalObjectsNumber", $totalObj);
		$this->set("maxTotalObjectsNumber", (!empty($totalObj)) ? max($totalObj) : 0);
	 }
	 
	 
	 private function timeEvolution($params) {
	 	$timeEvolution = array();
	 	$totalTimeEvolution = array();
		$s = $this->BEObject->getStartQuote();
		$e = $this->BEObject->getEndQuote();
	 	for ($i = 0; $i < 5; $i++) {
			$firstDayMonthTS = mktime(0, 0, 0, date("m")-$i, 1, date("Y"));
			$lastDayMonthTS = mktime(0, 0, 0, date("m")-$i+1, 0, date("Y"));
			$firstDayMonth = date("Y-m-d", $firstDayMonthTS);
			$lastDayMonth = date("Y-m-d", $lastDayMonthTS);
			
			$conditionsEvol = array_merge($params["conditions"], array("{$s}BEObject{$e}.{$s}created{$e} BETWEEN '" . $firstDayMonth . "' AND '" . $lastDayMonth ."'"));
			
			$countEvol = $this->BEObject->find("all", array(
					"fields" => array("COUNT(DISTINCT {$s}BEObject{$s}.{$s}id{$s}) as count", "ObjectType.id", "ObjectType.name"),
					"conditions" => $conditionsEvol,
					"contain" => $params["contain"],
					"group" => array("ObjectType.id", "ObjectType.name")	
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
	 	
		$s = $this->BEObject->getStartQuote();
		$e = $this->BEObject->getEndQuote();
	 	$rel = array();
	 	$max = 1;
		$conditionsRel = $params["conditions"];
		if (!empty($params["relation"])) { 	 	
		 	$conditionsRel = array_merge(
		 		$conditionsRel, array("RelatedObject.switch" => $params["relation"])
	 		);
	 		if ($params["relation"] == "comment")
	 			$conditionsRel[] = "BEObject.object_type_id <> " . Configure::read('objectTypes.comment.id');
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
					"fields" => array("COUNT(DISTINCT {$s}RelatedObject{$e}.{$s}object_id{$e}) as count_relations", "BEObject.id"),
					"conditions" => $conditionsRel,
					"group" => "BEObject.id",
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
	 	
		$s = $this->BEObject->getStartQuote();
		$e = $this->BEObject->getEndQuote();
	 	$this->User->contain();
	 	$users = $this->User->find("all");
	 	
	 	foreach ($users as $k => $u) {
	 		
	 		$objects = $this->BEObject->find("all", array(
					"fields" => array("COUNT(DISTINCT {$s}BEObject{$e}.{$s}id{$e}) as count", "ObjectType.id", "ObjectType.name"),
					"conditions" => array_merge( $params["conditions"], array("user_created" => $u["User"]["id"]) ),
					"contain" => $params["contain"],
					"group" => array("ObjectType.id", "ObjectType.name"),
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

	 private function usersStuff($params) {
		$groupstats = $this->User->Group->find("all", array("contain" => array()));
		$groupUserModel = ClassRegistry::init("GroupsUser");
		foreach ($groupstats as $key => $value) {
			$userscount = $groupUserModel->find("count", array(
				"conditions" => array("group_id" => $value['Group']['id'])
			));
			$groupstats[$key]['Group']['userscount'] = $userscount;
		}
		$this->set("groupstats", $groupstats);
	 }

}	

?>
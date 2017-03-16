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
 * BEdita instance data statistics controller class
 */
class StatisticsController extends ModulesController {
	var $name = 'Statistics';
	var $helpers = array('BeTree', 'BeToolbar');
	var $uses = array('BEObject', 'Tree', 'User') ;
	protected $moduleName = 'statistics';
	protected $objectTypes;
	protected $parents;
	protected $descendants;

	/**
	 * Index for statistcs - BEdita instance data statistics
	 *
	 * @return void
	 */
	public function index($id = null) {
		$this->parents = array();
		$this->objectTypes = ClassRegistry::init('ObjectType')->find('list', array('fields' => array('id', 'name')));
		$expandBranch = array();
		if (!empty($this->passedArgs['id'])) {
			$id = $this->passedArgs['id'];
		}
		if (!empty($id)) {
			$this->parents = array($id);
			$oo = $this->BeTree->getDescendants($id, null, array('object_type_id' => Configure::read('objectTypes.section.id')));
			if (!empty($oo['items'])) {
				$this->parents = Set::extract('/id', $oo['items']);
				$this->descendants = $this->Tree->find('list', array(
					'fields' => array('id'),
					'conditions' => array('parent_id' => $this->parents),
					'contain' => array()
				));
			} else {
				$this->descendants = array(0);
			}
			$expandBranch = array($id);
		}
		$user = $this->BeAuth->getUserSession();
		$this->set('tree', ClassRegistry::init('Tree')->getAllRoots($user['userid'], null, array('count_permission' => true), $expandBranch));

		// number of objects - basic statistics
		$this->totalObjects();

		// TOO SLOW on big data - enable at your own risk ($config['statistics.evolution'] = true, to enable it)
		// time evolution (get last five months)
		if (!empty(Configure::read('statistics.evolution'))) {
			$this->timeEvolution();
		}

		// TOO SLOW on big data - enable at your own risk ($config['statistics.comments'] = true, to enable it)
		// count comment
		if (!empty(Configure::read('statistics.comments'))) {
			$this->countRelations(array('id' => $id, 'relation' => 'comment'));
		}

		// TOO SLOW on big data - enable at your own risk ($config['statistics.objectsusers'] = true, to enable it)
		// count objects' for user
		if (!empty(Configure::read('statistics.objectsusers'))) {
			$this->objectsForUser();
		}

		// TOO SLOW on big data - enable at your own risk ($config['statistics.relations'] = true, to enable it)
		// count objects relations
		if (!empty(Configure::read('statistics.relations'))) {
			$this->countRelations(array('id' => $id));
		}

		// TOO SLOW on big data - enable at your own risk ($config['statistics.publications'] = true, to enable it)
		// publications
		if (!empty(Configure::read('statistics.publications'))) {
			$area = $this->loadModelByType('Area');
			$area->containLevel('default');
			$this->set('publications', $area->find('all'));
		}

		// TOO SLOW on big data - enable at your own risk ($config['statistics.usergroups'] = true, to enable it)
		// users/groups statistics
		if (!empty(Configure::read('statistics.usergroups'))) {
			$this->usersGroups();
		}
	}

	public function view() {
		$this->action = 'index';
		$this->index($this->passedArgs['id']);
	}

	/**
	 * Count objects by type.
	 * If $this->parents is not empty, get objects in tree for parents $this->parents
	 *
	 * @return void
	 */
	private function totalObjects() {
		foreach ($this->objectTypes as $object_type_id => $object_type_name) {
			$count = 0;
			if (empty($this->descendants)) {
				$count = $this->BEObject->find('count', array(
					'fields' => array('COUNT(DISTINCT id) AS count'),
					'conditions' => array('object_type_id' => $object_type_id),
					'contain' => array(),
				));
			} else {
				$count = $this->BEObject->find('count', array(
					'fields' => array('COUNT(DISTINCT id) AS count'),
					'conditions' => array(
						'object_type_id' => $object_type_id,
						'id' => $this->descendants
					),
					'contain' => array(),
				));
			}
			$totalObjectsNumber[$object_type_name] = $count;
		}
		arsort($totalObjectsNumber);
		$this->set('totalObjectsNumber', $totalObjectsNumber);
		$this->set('maxTotalObjectsNumber', (!empty($totalObjectsNumber)) ? max($totalObjectsNumber) : 0);
	 }

	 /**
	  * Objects evolution for $months range (default 5 months)
	  * If $this->parents is not empty, get objects in tree for parents $this->parents
	  *
	  * @param int $months range
	  * @return void
	  */
	 private function timeEvolution($months = 5) {
	 	$timeEvolution = array();
	 	$totalTimeEvolution = array();
	 	for ($i = 0; $i < $months; $i++) {
			$firstDayMonth = date('Y-m-d', mktime(0, 0, 0, date('m')-$i, 1, date('Y')));
			$lastDayMonth = date('Y-m-d', mktime(0, 0, 0, date('m')-$i+1, 0, date('Y')));
			$totalEvolMonth = 0;
			foreach ($this->objectTypes as $object_type_id => $object_type_name) {
				$count = 0;
				if (empty($this->descendants)) {
					$count = $this->BEObject->find('count', array(
						'fields' => array('COUNT(DISTINCT id) AS count'),
						'conditions' => array(
							'object_type_id' => $object_type_id,
							'created BETWEEN "' . $firstDayMonth . '" AND "' . $lastDayMonth . '"'
						),
						'contain' => array(),
					));
				} else {
					$count = $this->BEObject->find('count', array(
						'fields' => array('COUNT(DISTINCT id) AS count'),
						'conditions' => array(
							'id' => $this->descendants,
							'object_type_id' => $object_type_id,
							'created BETWEEN "' . $firstDayMonth . '" AND "' . $lastDayMonth . '"'
						),
						'contain' => array(),
					));
				}
				$timeEvolution[$firstDayMonth][$object_type_name] = $count;
				$totalEvolMonth += $count;
			}
			$totalTimeEvolution[$firstDayMonth] = $totalEvolMonth;			
		}
		$this->set('timeEvolution', $timeEvolution);
		$this->set('totalTimeEvolution', $totalTimeEvolution);
		$this->set('maxTotalTimeEvolution', max($totalTimeEvolution));
	 }

	 /**
	  * Count relations by $params and set data for view
	  *
	  * @param array $params for count
	  * @return void
	  */
	 private function countRelations($params) {
	 	$rel = array();
	 	$max = 1;
		$conditions = array();
		if (!empty($params['relation'])) {
		 	$conditions['RelatedObject.switch'] = $params['relation'];
	 		if ($params['relation'] === 'comment') {
	 			$conditions['NOT'] = array('BEObject.object_type_id' => Configure::read('objectTypes.comment.id'));
			}
		}
		if (!empty($this->descendants)) {
			$conditions['RelatedObject.id'] = $this->descendants;
		}
		if (!empty($params['id'])) {
		 	$bind = array(
				'belongsTo' => array(
					'BEObject' => array('foreignKey' => 'id'),
	 				'Tree' => array('foreignKey' => 'id')
	 			)
	 		);
	 		$contain = array('Tree', 'BEObject' => array('ObjectType'));
 		} else {
 			$bind = array(
				'belongsTo' => array(
					'BEObject' => array('foreignKey' => 'id')
		 		)
		 	);
		 	$contain = array('BEObject' => array('ObjectType'));
 		}
 		$this->BEObject->RelatedObject->bindModel($bind);
		$countRel = $this->BEObject->RelatedObject->find('all', array(
			'fields' => array('COUNT(DISTINCT RelatedObject.object_id) as count_relations', 'BEObject.id'),
			'conditions' => $conditions,
			'group' => "BEObject.id",
			'limit' => 20,
			'order' => "count_relations DESC",
			'contain' => $contain
		));
		foreach ($countRel as $key => $item) {
			$rel[] = array_merge($item['BEObject'], $item[0]);
			if ($key == 0) {
				$max = $item[0]['count_relations'];
			}
		}
		if (!empty($params['relation'])) {
			$this->set('contentCommented', $rel);
			$this->set('maxContentCommented', $max);
		} else {
			$this->set('relatedObject', $rel);
			$this->set('maxRelatedObject', $max);
		}
	 }

	 /**
	  * @deprecated too slow on big data
	  */
	 private function objectsForUser() {
	 	$users = $this->User->find('all', array(
			'contain' => array()
		));
	 	foreach ($users as $k => $u) {
			if (empty($this->descendants)) {
				$objects = $this->BEObject->find('all', array(
					'fields' => array('COUNT(DISTINCT BEObject.id) AS count', 'ObjectType.id', 'ObjectType.name'),
					'conditions' => array(
						'user_created' => $u['User']['id']
					),
					'contain' => array('ObjectType'),
					'group' => array('ObjectType.id', 'ObjectType.name'),
					'order' => 'count DESC'
				));
			} else {
				$objects = $this->BEObject->find('all', array(
					'fields' => array('COUNT(DISTINCT BEObject.id) AS count', 'ObjectType.id', 'ObjectType.name'),
					'conditions' => array(
						'id' => $this->descendants,
						'user_created' => $u['User']['id']
					),
					'contain' => array('ObjectType'),
					'group' => array('ObjectType.id', 'ObjectType.name'),
					'order' => 'count DESC'
				));
			}
			$obj['objects'] = array();
			$totalObjects = 0;
		 	foreach ($objects as $c) {
				$obj['objects'][$c['ObjectType']['name']] = $c[0]['count'];
				$totalObjects += $c[0]['count'];
			}
			$objectsForUser[$u['User']['id']] = array_merge($u['User'], $obj);
			$totalObjectsForUser[$u['User']['id']] = $totalObjects;
	 	}
	 	$this->set('objectsForUser', $objectsForUser);
		$this->set('totalObjectsForUser', $totalObjectsForUser);
		$this->set('maxObjectsForUser', max($totalObjectsForUser));
	 }

	/**
	 * Set groups/users statistics
	 *
	 * @deprecated too slow on big data
	 * @return void
	 */
	private function usersGroups() {
		$groupstats = $this->User->Group->find('all', array('contain' => array()));
		$groupUserModel = ClassRegistry::init('GroupsUser');
		foreach ($groupstats as $key => $value) {
			$userscount = $groupUserModel->find('count', array(
				'conditions' => array('group_id' => $value['Group']['id'])
			));
			$groupstats[$key]['Group']['userscount'] = $userscount;
		}
		$this->set('groupstats', $groupstats);
	}
}	

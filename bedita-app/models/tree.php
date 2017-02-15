<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2016 ChannelWeb Srl, Chialab Srl
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
 * Tree operations model
 */
class Tree extends BEAppModel
{

	public $primaryKey = "object_path";
    /**
     * Object cache 
     * 
     */
    protected $BeObjectCache = null;

    public function  __construct() {
        parent::__construct();
        if (!BACKEND_APP && Configure::read('objectCakeCache') && !Configure::read('staging')) {
            $this->BeObjectCache = BeLib::getObject('BeObjectCache');
        }
    }

	/**
	 * check object_path and parent_path, avoid object is parent or ancestor of itself
	 *
	 * @return boolean
	 */
	public function beforeSave() {

		// check object_path and parent_path consistency if they are defined (no recursion)
		$pathToCheck = array("object_path", "parent_path");
		foreach ($pathToCheck as $path) {
			if (isset($this->data["Tree"][$path])) {

				// no empty path permitted
				if (empty($this->data["Tree"][$path])) {
					return false;
				}

				// check for duplicates ids in path and stop save if find it
				$objectIds = explode("/", trim($this->data["Tree"][$path], "/"));
				if (!empty($objectIds)) {
					$countValues = array_count_values($objectIds);
					$sumCountValues = array_sum($countValues);
					if (count(array_unique($objectIds)) < $sumCountValues) {
						return false;
					}
				}
			}
		}

		if (!empty($this->data["Tree"]["id"])) {
			// avoid object is parent of itself
			if (isset($this->data["Tree"]["parent_id"]) && $this->data["Tree"]["id"] == $this->data["Tree"]["parent_id"]) {
				return false;
			}

			// avoid object is ancestor of itself
			if (isset($this->data["Tree"]["parent_path"]) && $this->data["Tree"]["parent_path"] != "/") {
				$parents = explode("/", $this->data["Tree"]["parent_path"]);
				foreach ($parents as $parent_id) {
					if ($this->data["Tree"]["id"] == $parent_id) {
						return false;
					}
				}
			}
		}
		if (!empty($this->data["Tree"]) && empty($this->data["Tree"]["area_id"]) && !empty($this->data["Tree"]["object_path"])) {
			$this->data["Tree"]["area_id"] = $this->getAreaIdByPath($this->data["Tree"]["object_path"]);
		}
		return true;
	}

	/**
	 * get parent or array of parents
	 *
	 * @param integer $id
	 * @param integer $area_id, publication id: if defined search parent only inside the publication
	 *
	 * @return mixed	integer, if only one parent found
	 * 					array, if two or more parents found
	 * 					false, error or none parent found
	 */
	public function getParent($id, $area_id=null, $status = array()) {
		if (empty($id)) {
			return false;
		}
		$conditions["Tree.id"] = $id;
		if (!empty($area_id)) {
			$conditions["Tree.area_id"] = $area_id;
		}

		if (!empty($status)) {
			// bind BEObject to get only parents with status in $status
			$this->bindModel(array(
				'belongsTo' => array(
					'BEObject'=> array(
						'foreignKey' => 'parent_id'
					)
				)
			));

			$conditions['BEObject.status'] = $status;
		}

		$ret = $this->find("all", array(
				"conditions" => $conditions,
				"fields" => array("parent_id")
			)
		);

		if(!$ret) {
			return false;
		}

		if(!count($ret)) {
			return false ;
		} else if (count($ret) == 1) {
			return $ret[0]['Tree']['parent_id'] ;
		} else {
			$tmp = array() ;
			for($i=0; $i < count($ret) ; $i++) {
				$tmp[] = $ret[$i]['Tree']['parent_id'] ;
			}

			return $tmp ;
		}
	}

    /**
     * Array of object $id tree parents objects
     *
     * If `$useCache` is true it will try to read/write from/to cache 
     *
     * @param int|null $id The child id
     * @param int|null $area_id The publication (area) id
     * @param array $status An array of object status that parent must have
     * @param bool $useCache If it has to use cache (true default)
     * @return array, parent ids (may be empty)
     */
    public function getParents($id = null, $area_id=null, $status = array(), $useCache = true) {
        if ($id === null) {
            return array();
        }

        $cacheOpts = array($area_id, $status);
        if ($this->BeObjectCache && $useCache) {
            $cachedValue = $this->BeObjectCache->read($id, $cacheOpts, 'parents');
            if ($cachedValue !== false) {
                return $cachedValue;
            }
        }

        $parents = $this->getParent($id, $area_id, $status) ;
        if ($parents === false) {
            $parents = array();
        } elseif (!is_array($parents)) {
            $parents = array($parents);
        }

        if ($this->BeObjectCache && $useCache) {
            $this->BeObjectCache->write($id, $cacheOpts, $parents, 'parents');
        }
        return $parents;
    }

    /**
     * Update tree position of object $id with new $destination array
     *
     * To update tree:
     * - all parents are taken
     * - ids in all parents that missing from $destination are removed
     * - ids in $destination that missing from all parents are added
     *
     * With $options you can limit the parents taken
     *
     * Possible values are:
     * - `area_id` to limit parents to some publication.
     * - `status` to limit parents with specific status
     *
     * @param integer $id
     * @param array $options
     * @param array $destination
     */
    public function updateTree($id, $destination, array $options = array()) {
        $options += array(
            'area_id' => null,
            'status' => array(),
			'mantain_hidden_branch' => true
        );
        if (!is_array($destination)) {
            $destination = (empty($destination))? array() : array($destination);
        }
		$excludeFromTreeIds = Configure::read('excludeFromTreeIds');
		if ($options['mantain_hidden_branch'] === true && !empty($excludeFromTreeIds)) {
			foreach ($excludeFromTreeIds as $excludeFromTreeId) {
				$result = $this->find('list', array(
					'fields' => array('parent_id'),
					'conditions' => array(
						'id' => $id,
						'object_path LIKE' => '%/' . $excludeFromTreeId . '/%'
					)
				));
				if (!empty($result)) {
					$destination = array_merge($destination, $result);
				}
			}
		}
        $currParents = $this->getParents($id, $options['area_id'], $options['status'], false);
        // remove
        $remove = array_diff($currParents, $destination) ;
        foreach ($remove as $parent_id) {
            $this->removeChild($id, $parent_id) ;
        }
        // insert
        $add = array_diff($destination, $currParents) ;
        foreach ($add as $parent_id) {
            $this->appendChild($id, $parent_id) ;
        }
    }

	/**
	 * Return id of publication that contains the section, by id
	 *
	 * @param int $id
	 * @return int
	 */
	public function getRootForSection($id) {
		$area_id = $this->field("area_id", array("id"=>$id));
		return $area_id;
	}

	/**
	 * append an object to a parent in tree
	 *
	 * @param int $id object id
	 * @param int $idParent parent object id
     * @param int $priority if not passed append as last child else use passed position
	 * @return boolean
	 */
	public function appendChild($id, $idParent = null, $priority = null) {
		// avoid to append item to itself
		if ($id == $idParent) {
			return false;
		}
		// root
		if (empty($idParent)) {
			$data["Tree"] = array(
				"id" => $id,
				"area_id" => $id,
				"object_path" => "/".$id,
				"parent_path" => "/",
				"priority" => 1
			);
		} else {
			$parentPath = $this->field("object_path", array("id" => $idParent));
			$area_id = $this->getAreaIdByPath($parentPath);
            if (empty($priority)) {
    			$maxPriority = $this->field("priority", array("parent_id" => $idParent), "priority DESC");
    			$priority = (!empty($maxPriority))? $maxPriority + 1 : 1;
            }

			$data["Tree"] = array(
				"id" => $id,
				"area_id" => $area_id,
				"parent_id" => $idParent,
				"object_path" => $parentPath . "/".$id,
				"parent_path" => $parentPath,
				"priority" => $priority
			);
		}

		$ret = $this->save($data);

		return (($ret === false)?false:true) ;

	}

	/**
	 * Return id of publication by path
	 *
	 * @param string $path
	 * @return int
	 */
	public function getAreaIdByPath($path) {
		$pathArr = explode("/", trim($path, "/"));
		return $pathArr[0];
	}

	/**
	 * move up or down a leaf tree inside a branch
	 *
	 * @param int $id object id to move
	 * @param int $idParent parent object (branch)
	 * @param boolean $up true move up (priority - 1), false move down (priority + 1)
	 * @return boolean
	 */
	public function movePriority($id, $idParent, $up=true) {
		$treeRow = $this->find("first", array(
			"conditions" => array("id" => $id, "parent_id" => $idParent)
		));

		if (empty($treeRow)) {
			return false;
		}

		$origPriority = $treeRow["Tree"]["priority"];

		if ($up) {
			$op = " < ";
			$dir = "DESC";
		} else {
			$op = " > ";
			$dir = "ASC";
		}
		$op = ($up)? " < " : " > ";
		$otherRow = $this->find("first", array(
			"conditions" => array("parent_id" => $idParent, "priority" . $op . $origPriority),
			"limit" => 1,
			"order" => "priority " . $dir
		));

		if (empty($otherRow["Tree"]["priority"])) {
			return false;
		}

		$treeRow["Tree"]["priority"] = $otherRow["Tree"]["priority"];
		$otherRow["Tree"]["priority"] = $origPriority;

		if (!$this->save($treeRow)) {
			return false;
		}
		$this->create();
		if (!$this->save($otherRow)) {
			return false;
		}

		return true;
	}

	/**
	 * move up a leaf tree inside a branch
	 *
	 * @param int $id to move
	 * @param int $idParent parent object (branch)
	 * @return boolean
	 */
	public function movePriorityUp($id, $idParent) {
		return $this->movePriority($id, $idParent);
	}

	/**
	 * move down a leaf tree inside a branch
	 *
	 * @param int $id to move
	 * @param int $idParent parent object (branch)
	 * @return boolean
	 */
	public function movePriorityDown($id, $idParent) {
		return $this->movePriority($id, $idParent, false);
	}

	/**
	 * remove a leaf tree from a branch
	 *
	 * @param int $id to remove
	 * @param int $idParent parent object (branch)
	 * @return boolean
	 */
	public function removeChild($id, $idParent) {
		$ret = $this->deleteAll(array("id" => $id, "parent_id" => $idParent));
		return (($ret === false)?false:true) ;
	}

	/**
	 * set position for a leaf tree in a branch
	 *
	 * @param int $id to move
	 * @param int $idParent parent object (branch)
	 * @return boolean
	 */
	public function setPriority($id, $priority, $idParent) {
		$row = $this->find("first", array(
			"conditions" => array(
				"id" => $id,
				"parent_id" => $idParent
			)
		));
		if (empty($row["Tree"])) {
			return false;
		}
		$row["Tree"]["priority"] = $priority;
		$ret =  $this->save($row);
		return (($ret === false)?false:true) ;
	}


	/**
	 * object position in a tree branch 
	 *
	 * @param int $id object id
	 * @param int $idParent parent object (branch) id
	 * @return int
	 */
	public function getPriority($id, $idParent) {
	    return $this->field("priority", 
	            array("id" => $id, "parent_id" => $idParent));
	}
	
	
	/**
	 * move branch to another parent
	 *
	 * @param int $idNewParent
	 * @param int $idOldParent
	 * @param int $id
	 * @return boolean
	 */
	public function move($idNewParent, $idOldParent, $id) {
		// avoid recursive move (item inside itself)
		if ($id == $idNewParent) {
			return false;
		}
		// Verify that new parent is not a descendant on the tree to move
		if ($this->isParent($id, $idNewParent)) {
			return false;
		}

		$rowToMove = $this->find("first", array(
			"conditions" => array(
				"id" => $id,
				"parent_id" => $idOldParent
			)
		));

		$newParentRow = $this->find("first", array(
			"conditions" => array(
				"id" => $idNewParent
			)
		));

		$newParentPath = $newParentRow["Tree"]["object_path"];
		$newPath = $newParentPath . "/" . $rowToMove["Tree"]["id"];
		$oldPath = $rowToMove["Tree"]["object_path"];

		$children = $this->find("all", array(
			"conditions" => array("object_path LIKE" => $oldPath."/%")
		));

		if (!$this->delete($rowToMove["Tree"]["object_path"])) {
			return false;
		}

		$area_id = $this->getAreaIdByPath($newPath);
		$rowToMove["Tree"]["parent_path"] = $newParentPath;
		$rowToMove["Tree"]["object_path"] = $newPath;
		$rowToMove["Tree"]["parent_id"] = $idNewParent;
		$rowToMove["Tree"]["area_id"] = $area_id;

		$maxBranchPriority = $this->field("priority", array("parent_id" => $idNewParent), "priority DESC");
		$rowToMove["Tree"]["priority"] = (empty($maxBranchPriority))? 1 : $maxBranchPriority + 1;

		$this->create();
		if (!$this->save($rowToMove)) {
			return false;
		}

		foreach ($children as $child) {
			if (!$this->delete($child["Tree"]["object_path"])) {
				return false;
			}
			$child["Tree"]["parent_path"] = str_replace($oldPath, $newPath, $child["Tree"]["parent_path"]);
			$child["Tree"]["object_path"] = str_replace($oldPath."/", $newPath."/", $child["Tree"]["object_path"]);
			$child["Tree"]["area_id"] = $area_id;
			$this->create();
			if (!$this->save($child)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * get all tree roots objects (publications)
	 *
	 * @param  int $userid
	 * @param  mixed $status string or array of status
	 * @param  array  $filter filter options see BEAppModel::findObjects
	 * @param  array  $expandBranch array of branch ids of which roots have to expanded
	 * @return array
	 */
	public function getAllRoots($userid = null, $status = null, $filter = array(), $expandBranch = array()) {
		$filter['object_type_id'] = array(Configure::read('objectTypes.area.id'));
		$roots = $this->getAll(null, $userid, $status, $filter);
		if (!empty($expandBranch)) {
			// get root of $expandedBranch array
			foreach ($expandBranch as &$branchId) {
				$branchId = $this->getRootForSection($branchId);
			}
			$filter['object_type_id'][] = Configure::read('objectTypes.section.id');
			foreach ($roots as $key => $root) {
				if (in_array($root['id'], $expandBranch)) {
					$res = $this->getAll($root['id'], $userid, $status, $filter);
					$roots[$key] = $res[0];
				}
			}
		}
		return $roots;
	}

	/**
	 * Get Tree where 'id' (if it passed) has to be tree root
	 * If it's a section id then an empty array is returned
	 * @param integer $id		publication id. If null get all trees, one for every publication
	 * @param string $userid	user. if null: no permission check (default); if '': guest user
	 * @param string $status	only objs with this status
	 * @param array $filter		see BEAppModel::findObjects
	 * @return array every first level key is a publication
	 */
	public function getAll($id = null, $userid = null, $status = null, $filter = array()) {
		// build tree
		$tree = array();

		$filter["Tree.*"] = "";
		if (!empty($id)) {
			$filter["Tree.area_id"] = $id;
		}

		$res = $this->findObjects(null, $userid, $status, $filter, "parent_path, priority, title", true, 1, null, true);

		$tree = $this->buildTree($res["items"]);
		return $tree ;
	}

	/**
	 * Return a tree build for the items passed
	 *
	 * @param array $items
	 * @return array
	 */
	public function buildTree($items) {
		$tree = array();
		$roots = array();
		foreach ($items as $root) {

			$root['children']	= array() ;
			$roots[$root['id']] = &$root ;

			if(isset($root['parent_id']) && isset($roots[$root['parent_id']])) {
				$roots[$root['parent_id']]['children'][] = &$root ;
			} elseif (!empty($root['parent_id'])) {
				$this->putBranchInTree($tree, $root);
			} elseif ($root["object_type_id"] == Configure::read("objectTypes.area.id")) {
				$tree[] = &$root;
			}

			unset($root);
		}
		return $tree;
	}

	/**
	 * search where have to stay $branch in $tree and put in
	 * @param array $tree
	 * @param array $branch to put in tree
	 */
	private function putBranchInTree(&$tree, $branch) {
		foreach ($tree as $k => $t) {
			if (!empty($branch['parent_id']) && $t["id"] == $branch['parent_id']) {
				$tree[$k]['children'][] = $branch;
			} elseif (!empty($t['children'])) {
				$this->putBranchInTree($t['children'], $branch);
			}
		}
	}

	/**
	 * check if $idParent is an ancestor
	 *
	 * @param integer $idParent
	 * @param integer $id
	 * @return boolean
	 */
	public function isParent($idParent, $id) {
		$c = $this->find("count", array(
			"conditions" => array(
				"object_path LIKE" => "%/" . $idParent . "/%",
				"id" => $id
			)
		));

		if ($c === 0) {
			return false;
		}

		return true;
	}

    /**
     * Titles path for branches related to $id passed
     *
     * @param int $id object id
     * @param array $hiddenBranchIds ids
     * @return array tree descriptive data
     */
    public function titlesPaths($id, $hiddenBranchIds = array()) {
        $result = array();
        $paths = $this->find('list', array(
            'fields' => 'Tree.parent_path',
            'conditions' => array('Tree.id' => $id)
        ));
        foreach ($paths as $path) {
            $ids = array_filter(explode("/", $path));
            if (empty($hiddenBranchIds) || !empty(array_intersect($ids, $hiddenBranchIds))) {
                $titles = ClassRegistry::init('BEObject')->find('list', array(
                    'fields' => array('BEObject.id', 'BEObject.title'),
                    'conditions' => array('BEObject.id' => $ids)
                ));
                $idsPath = $path;
                $titlesPath = '';
                foreach ($titles as $key => $value) {
                    if (!empty($titlesPath)) {
                        $titlesPath.= ' > ';
                    }
                    $titlesPath.= $value;
                }
                $result[] = array(
                    'ids' => $ids,
                    'parentId' => end(array_values($ids)),
                    'idsPath' => $idsPath,
                    'titles' => $titles,
                    'titlesPath' => $titlesPath
                );
            }
        }
        return $result;
    }

    /**
     * check if an object is on the tree
     *
     * @param integer $id
     * @param integer $area_id if defined check if the object is a descendant of a publication
     * @return boolean
     */
    public function isOnTree($id, $area_id = null, $status = array()) {
        $result = true;
        $cacheOpts = array($area_id, $status);
        if ($this->BeObjectCache) {
            $cachedValue = $this->BeObjectCache->read($id, $cacheOpts, 'is-on-tree');
            if ($cachedValue !== false) {
                return ($cachedValue == 1);
            }
        }
        $conditions['Tree.id'] = $id;
        if (!empty($area_id)) {
            $conditions['Tree.area_id'] = $area_id;
        }
        $joins = array();
        if (!empty($status)) {
            $conditions['BEObject.status'] = $status;
            $joins = array(
                array(
                    'table' => 'objects',
                    'alias' => 'BEObject',
                    'type' => 'inner',
                    'conditions' => array(
                        'BEObject.id = Tree.id'
                    )
                )
            );
        }
        $c = $this->find('count', array(
            'fields' => 'DISTINCT Tree.id',
            'conditions' => $conditions,
            'joins' => $joins
        ));
        if ($c === 0) {
            $result = false;
        }
        if ($this->BeObjectCache) {
            $resultToCache = ($result) ? 1 : 'no';
            $this->BeObjectCache->write($id, $cacheOpts, $resultToCache, 'is-on-tree');
        }
    	return $result;
    }

    /**
     * Return a list of a count (based on $option['count']) of related object to $id that are on tree
     *
     *  $options can contain
     *  - 'area_id' to filter on publication
     *  - 'status' to filter on object status
     *  - 'count' (default false) to get a count or a list of object ids
     *  - 'relation' to filter on relation name
     *
     * @param int $id the object id
     * @param array $options
     * @return array|int
     */
	public function relatedObjectsOnTree($id, array $options = array()) {
        $options += array(
            'area_id' => null,
            'status' => array(),
            'count' => false,
            'relation' => null
        );
		if (!empty($options['area_id'])) {
			$conditions['Tree.area_id'] = $options['area_id'];
		}

        $objectRelationJoin = array(
            'table' => 'object_relations',
            'alias' => 'ObjectRelation',
            'type' => 'INNER',
            'conditions' => array(
                'ObjectRelation.object_id = Tree.id',
                'ObjectRelation.id' => $id
            )
        );
        if (!empty($options['relation'])) {
            $objectRelationJoin['conditions']['ObjectRelation.switch'] = $options['relation'];
        }

        $objectJoin = array();
		if (!empty($options['status'])) {
            $objectJoin = array(
                'table' => 'objects',
                'alias' => 'BEObject',
                'type' => 'INNER',
                'conditions' => array(
                    'BEObject.id = Tree.id',
                    'BEObject.status' => $options['status']
                )
            );
		}
        if ($options['count']) {
            $findType = 'count';
            $fields = 'DISTINCT Tree.id';
            $group = '';
        } else {
            $findType = 'list';
            $fields = array('Tree.id');
            $group = 'Tree.id';
        }

		$objects = $this->find($findType, array(
            'fields' => $fields,
			'conditions' => $conditions,
            'group' => $group,
			'joins' => array(
				$objectRelationJoin,
				$objectJoin
			)
		));

        if (!$options['count']) {
            $objects = array_values($objects);
        }

		return $objects;
	}

	/**
	 * Children of id element (only 1 level in tree).
	 * If userid != null, only objects with read permissione for user, if ' ' - use guest/anonymous user,
	 * if userid = null -> no permission check.
	 * Filter: object types, search text query.
	 *
	 * @param integer $id		root id
	 * @param string $userid	user: null (default) => no permission check. ' ' => guest/anonymous user,
	 * @param string $status	object status
	 * @param array  $filter	Filter: object types, search text query, eg. array(21, 22, "search" => "text to search").
	 * 							Default: all object types
	 * @param string $order		field to order result (id, status, modified..)
	 * @param boolean $dir		true (default), ascending, otherwiese descending.
	 * @param integer $page		Page number (for pagination)
	 * @param integer $dim		Page dim (for pagination)
	 * @param array $excludeIds array of ids to exclude
	 * @return array
	 */
	function getChildren($id = null, $userid = null, $status = null, $filter = array(), $order = null, $dir  = true, $page = 1, $dim = null, $excludeIds = array()) {
		return $this->findObjects($id, $userid, $status, $filter, $order, $dir, $page, $dim, false, $excludeIds) ;
	}

	/**
	 * Descendants of id element (all elements in tree).
	 * (see: BEObject->find(), to search not using content tree ).
	 * If userid present, only objects with read permissione, if ' ' - guest/anonymous user,
	 * if userid = null -> no permission check.
	 * Filter: object types, search text query.
	 *
	 * @param integer $id		root id
	 * @param string $userid	user: null (default) => no permission check. ' ' => guest/anonymous user,
	 * @param string $status	object status
	 * @param array  $filter	Filter: object types, search text query, eg. array(21, 22, "search" => "text to search").
	 * 							Default: all object types
	 * @param string $order		field to order result (id, status, modified..)
	 * @param boolean $dir		true (default), ascending, otherwiese descending.
	 * @param integer $page		Page number (for pagination)
	 * @param integer $dim		Page dim (for pagination)
	 * @param array $excludeIds array of ids to exclude
	 * @return array
	 */
	function getDescendants($id = null, $userid = null, $status = null, $filter = array(), $order = null, $dir  = true, $page = 1, $dim = null, $excludeIds = array()) {
		return $this->findObjects($id, $userid, $status, $filter, $order, $dir, $page, $dim, true, $excludeIds) ;
	}

	/**
	 * save Tree.menu field to set menu and canonical path visibility
	 *
	 * @param mixed $ids, id or array of ids on which save menu field.
	 *			if it's an array cycles on ids and save $menu value foreach of them
	 * @param int $parent_id
	 * @param mixed $menu, can be 1, 0 or null
	 *			if it's null the default value for every object is used (section = 1, other objects = 0)
	 * @throws BeditaException
	 */
	public function saveMenuVisibility($ids, $parent_id, $menu = null) {
		if (empty($ids) || empty($parent_id)) {
			throw new BeditaException(__("Missing mandatory data id and/or parent_id to save menu visibility", true), array("ids" => $ids, "parent_id" => $parent_id));
		}
		if (is_numeric($ids)) {
			$ids = array($ids);
		}
		foreach ($ids as $id) {
			// set default value for every object (section = 1, other objects = 0)
			if ($menu === null) {
				$objectTypeId = ClassRegistry::init("BEObject")->findObjectTypeId($id);
				$menu = ($objectTypeId == Configure::read("objectTypes.section.id"))? 1 : 0;
			}
			$this->id = $this->field($this->primaryKey, array('id' => $id, 'parent_id' => $parent_id));
			if (!$this->id) {
				throw new BeditaException( __("Error saving visibility in menu and canonical paths", true), "Error retrieving Tree model primary key " . $this->primaryKey . " for id=" . $id);
			}
			if (!$this->saveField('menu', $menu)) {
				throw new BeditaException( __("Error saving visibility in menu and canonical paths", true), "Error saving Tree.menu field " . $menu . " for object " . $id);
			}
		}
	}

	/**
	 * Clone a tree structure
	 * Clone Publication and sections and add related contents
	 *
	 * @param int $id, publication/section id
	 * @param array $options, see BEAppObjectModel::arrangeDataForClone()
	 * @return array, contain couple of original id and cloned id
	 * @throws BeditaException
	 */
	public function cloneStructure($id, array $options = array()) {
		$idConversion = array();
		$objectTypeId = ClassRegistry::init("BEObject")->findObjectTypeId($id);
		// publication
		if ($objectTypeId == Configure::read("objectTypes.area.id")) {
			// clone publication
			$Area = ClassRegistry::init("Area");
			if (!$Area->cloneObject($id, $options)) {
				throw new BeditaException(__("Error cloning Publication", true) . ": id =  " . $id, array("id" => $id));
			}

			$newPubId = $Area->id;
			$idConversion[$id] = $newPubId;

			// clone publication's contents
			$this->copyContentsToBranch($id, $newPubId);

			// clone tree: get sections, clone them and build tree structure, get sections' children and clone tree structure
			$Section = ClassRegistry::init("Section");
			$sections = $Section->find("all", array(
				"conditions" => array(
					"Tree.area_id" => $id,
					"BEObject.object_type_id" => Configure::read("objectTypes.section.id")
				),
				"order" => "Tree.object_path ASC",// . $publication["priority_order"]
				"contain" => array(
					"BEObject" => array(
						"Permission",
						"Category",
					    "ObjectProperty",
						"LangText"
						),
					"Tree"
				)
			));

			if (!empty($sections)) {
				// reorder with natural sort of object_path
				$sectionsTmp = Set::combine($sections, "{n}.object_path", "{n}");
				$objectPaths = array_keys($sectionsTmp);
				natsort($objectPaths);
				$sections = array();
				foreach ($objectPaths as $path) {
					$sections[] = $sectionsTmp[$path];
				}

				foreach ($sections as $s) {
					$sectionId = $s["id"];
					// clone section
					$s["parent_id"] = $idConversion[$s["parent_id"]];
					$Section->arrangeDataForClone($s, $options);
					$Section->create();
					if (!$Section->save($s)) {
						throw new BeditaException(__("Error cloning Section", true) . " " . $s["title"], array("id" => $sectionId));
					}
					$newSectionId = $Section->id;
					$idConversion[$sectionId] = $newSectionId;

					// set priority
					if (!$this->setPriority($newSectionId, $s["priority"], $s["parent_id"])) {
						throw new BeditaException(__("Error setting Section priority", true), array("id" => $newSectionId, "priority" => $s["priority"]));
					}

					$this->copyContentsToBranch($sectionId, $newSectionId);
				}
			}
		} elseif ($objectTypeId == Configure::read("objectTypes.section.id")) {
			//@todo: a parent_id it has to be defined otherwise the root section will be transformed in a Publication
		}

		return $idConversion;
	}

	/**
	 * copy contents from a branch to another brnach
	 *
	 * @param int $originalBranchId, branch (publication/section) id where the contents are
	 * @param int $newBranchId, branch (publication/section) id where the contents have to be copied
	 * @throws BeditaException
	 */
	public function copyContentsToBranch($originalBranchId, $newBranchId) {
		$children = $this->getChildren(
			$originalBranchId,
			null,
			null,
			array(
				'NOT' => array('object_type_id' => Configure::read('objectTypes.section.id'))
			)
		);
		if (!empty($children["items"])) {
			foreach ($children["items"] as $item) {
				if (!$this->appendChild($item["id"], $newBranchId)) {
					throw new BeditaException(__("Error cloning tree", true), array("child id" => $item["id"]));

				}
				// set priority
				if (!$this->setPriority($item["id"], $item["priority"], $newBranchId)) {
					throw new BeditaException(__("Error setting contents priority", true), array("id" => $item["id"], "parent_id" => $newBranchId, "priority" => $s["priority"]));
				}
			}
		}
	}

    /**
     * Add to array of BEdita objects a count of ubiquity
     *
     * @param array $objects
     * @param array $options
     * @return array
     */
    public function countUbiquity(array $objects, array $options = array()) {
        foreach ($objects as &$obj) {
            $obj['ubiquity'] = $this->find('count', array(
                'conditions' => array('id' => $obj['id'])
            ));
        }
        return $objects;
    }

    /**
     * Removes a full branch that originates from the element with passed `$id`.
     * 
     * @param int $id ID of element to remove.
     * @return bool `true` if everything was ok, `false` otherwise.
     * @throws BeditaException Throws an exception if attempting to remove a leaf (or a branch generated by an object that should be a leaf instead!), or an ubiquitous branch.
     */
    // * @param mixed $parentId ID of parent object. Necessary to determinate "context". For root elements, please use `null`. If missing or `=== false` will delete ALL branches that originate from the given `$id`!
    public function removeBranch($id/* , $parentId = false */) {
        $collectionTypes = array(Configure::read('objectTypes.area.id'), Configure::read('objectTypes.section.id'));  // Types of 'collection' objects.

        // Check if it's a leaf.
        $check = ClassRegistry::init('BEObject')->find('count', array(
            'contain' => array(),
            'conditions' => array(
                'id' => $id,
                'object_type_id NOT' => $collectionTypes
            ),
        ));
        if ($check) {
            throw new BeditaException(__('Error deleting tree branch', true), array('id' => $id));
        }

        // If it's not a leaf, check if it's ubiquitous.
        $check = $this->find('count', array(
            'conditions' => array('id' => $id),
        ));
        if ($check > 1) {
            throw new BeditaException(__('Tree branch is ubiquitous', true), array('id' => $id));
        }

        // Find all descendant nodes (we'll need to call different methods to delete 'em according to their `object_type_id`).
        $descendants = $this->find('all', array(
            'contain' => array(),
            'fields' => array('Tree.id', 'BEObject.object_type_id'),
            'joins' => array(
                array(
                    'table' => 'objects',
                    'alias' => 'BEObject',
                    'type' => 'INNER',
                    'conditions' => array('BEObject.id = Tree.id'),
                ),
            ),
            'conditions' => array('parent_id' => $id),
        ));
        $descendants = Set::combine($descendants, '{n}.Tree.id', '{n}.BEObject.object_type_id');

        // Remove sub-branches and leafs.
        $ok = true;
        foreach ($descendants as $desc => $objectType) {
            if (in_array($objectType, $collectionTypes)) {
                // Remove sub-branches.
                $ok = $this->removeBranch($desc) && $ok;
            } else {
                // Remove leafs.
                $ok = $this->removeChild($desc, $id) && $ok;
            }
        }

        // If everything went OK, remove current tree node, which has descendants no more, so it behaves like a leaf.
        $ok = $ok && ($this->deleteAll(array('id' => $id)) !== false);

        return $ok;

        #####################################
        ############ OLD VERSION ############
        #####################################

        $ok = true;

        // Find current object path.
        $conditions = array(
            'id' => $id,
        );
        if ($parentId !== false) {
            $conditions['parent_id'] = $parentId;
        }
        $path = $this->find('list', array(
            'contain' => array(),
            'fields' => array('object_path'),
            'conditions' => $conditions,
        ));

        // Find descendants.
        $descendants = $this->find('list', array(
            'contain' => array(),
            'fields' => array('id'),
            'conditions' => array('parent_path' => $path),
        ));
        foreach ($descendants as $desc) {
            // Remove sub-branches and leafs.
            $ok = $this->removeBranch($desc, $id) && $ok;
        }
        // If everything went OK, remove current tree node (for by now it has already become a leaf).
        return $ok && $this->removeChild($id, $parentId);
    }

    /**
     * Removes a full tree that originates from the element with passed `$id`.
     * This method is an alias of `Tree::removeBranch()`.
     * 
     * @param int $id ID of element to remove.
     * @return bool `true` if everything was ok, `false` otherwise.
     * @throws BeditaException Throws an exception if attempting to remove a non-root element, a leaf (or a tree generated by an object that should be a leaf instead!), or an ubiquitous tree (?!).
     * @see Tree::removeBranch()
     */
    public function removeTree($id) {
        // Check if it's root.
        $check = $this->find('count', array(
            'contain' => array(),
            'conditions' => array('id' => $id, 'area_id' => $id, 'parent_id' => null),
        ));
        if (!$check) {
            throw new BeditaException(__('Object is not root', true), array('id' => $id));
        }

    	return $this->removeBranch($id/*, null*/);
    }

    /**
     * Count children using $options if any
     * $options can contain every params used to customize Model::find()
     *
     * @see self::countChildren()
     * @param int $parentId the parent id
     * @param array $options
     * @return int
     */
    public function countChildrenContents($parentId, array $options = array()) {
        $sectionObjectTypeId = Configure::read('objectTypes.section.id');
        $this->bindModel(array(
            'belongsTo' => array(
                'BEObject' => array(
                    'foreignKey' => 'id',
                    'type' => 'inner'
                )
            )
        ));

		if (empty($options['conditions']['NOT'])) {
			$options['conditions']['NOT'] = array();
		}
        $options['conditions']['NOT'] = array('BEObject.object_type_id' => $sectionObjectTypeId) + $options['conditions']['NOT'];

        return $this->countChildren($parentId, $options);
    }

    /**
     * Count children using $options if any
     * $options can contain every params used to customize Model::find()
     *
     * @see self::countChildren()
     * @param int $parentId the parent id
     * @param array $options
     * @return int
     */
    public function countChildrenSections($parentId, array $options = array()) {
        $sectionObjectTypeId = Configure::read('objectTypes.section.id');
        $this->bindModel(array(
            'belongsTo' => array(
                'BEObject' => array(
                    'foreignKey' => 'id',
                    'type' => 'inner'
                )
            )
        ));
        $options['conditions']['BEObject.object_type_id'] = $sectionObjectTypeId;
        return $this->countChildren($parentId, $options);
    }

    /**
     * Count children using $options if any
     * $options can contain every params used to customize Model::find()
     *
     * @param int $parentId the parent id
     * @param array $options
     * @return int
     */
    public function countChildren($parentId, array $options = array()) {
        $cacheOpts = $options;
        if ($this->BeObjectCache) {
            $cachedValue = $this->BeObjectCache->read($parentId, $cacheOpts, 'count-children');
            if ($cachedValue !== false) {
                return $cachedValue;
            }
        }
        $options += array(
            'fields' => 'DISTINCT (Tree.id)',
            'conditions' => array()
        );
        $options['conditions']['Tree.parent_id'] = $parentId;
        $count = $this->find('count', $options);
        if ($this->BeObjectCache) {
            $this->BeObjectCache->write($parentId, $cacheOpts, $count, 'count-children');
        }
        return $count;
    }

    /**
     * Return a list of children ids
     *
     * @param int $parentId The parent id
     * @param boolean $descendants True to get all descendants, false to get just children
     * @return array
     */
    public function findChildrenList($parentId, $descendants = false) {
        if ($descendants) {
            return $this->find('list', array(
                'fields' => array('id'),
                'conditions' => array('object_path LIKE' => '%/' . $parentId . '/%'),
                'group' => 'id'
            ));
        }

        return $this->find('list', array(
            'fields' => array('id'),
            'conditions' => array('parent_id' => $parentId)
        ));
    }

}

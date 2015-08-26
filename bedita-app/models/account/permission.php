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
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Permission extends BEAppModel
{
	
	var $belongsTo = array(
		'User' =>
			array(
				'className'		=> 'User',
				'conditions'	=> "Permission.switch = 'user' ",
				'foreignKey'	=> 'ugid'
			),
		'Group' =>
			array(
				'className'		=> 'Group',
				'conditions'	=> "Permission.switch = 'group' ",
				'foreignKey'	=> 'ugid'
			),
	);

	/**
	 * Add object permissions
	 *
	 * @param integer $objId	object id
	 * @param array $perms		array like (array("flag"=>1, "switch" => "group", "name" => "guest"), array(...))
	 */
	public function add($objectId, $perms) {
		foreach ($perms as $d) {
			$d["object_id"] = $objectId;
			$this->create();
			if($d["switch"] == "group") {
				$group = ClassRegistry::init("Group");
				$d["ugid"] = $group->field('id', array('name'=>$d["name"]));
			} else {
				$user = ClassRegistry::init("User");
				$d["ugid"] = $user->field('id', array('userid'=>$d["name"]));
			}
			if(!$this->save($d)) {
				throw new BeditaException(__("Error saving permissions", true), 
					"obj: $objectId - permissions: ". var_export($perms, true));
				;
			}
		}
	}
	
	/**
	 * Remove all object permissions.
	 *
	 * @param integer $objectId		object ID
	 */
	public function removeAll($objectId) {
		if(!$this->deleteAll(array("object_id" => $objectId), false))
			throw new BeditaException(__("Error removing permissions", true), "object id: $objectId");
	}	
	
	/**
	 * Updates/replaces object permissions
	 *
	 * @param integer $objId	object id
	 * @param array $perms		array like (array("flag"=>1, "switch" => "group", "name" => "guest"), array(...))
	 */
	public function replace($objectId, $perms) {
		$this->removeAll($objectId);
		$this->add($objectId, $perms);
	}


	/**
	 * remove old permissions on $groupId and add new $perms
	 *
	 * @param  int $groupId
	 * @param  array $perms array like (array("flag"=> 1, "object_id"), array(...))
	 */
	public function replaceGroupPerms($groupId, array $perms) {
		// remove all group permissions
		if(!$this->deleteAll(array('ugid' => $groupId, 'switch' => 'group'), false)) {
			throw new BeditaException(__("Error removing permissions for group", true) . " $groupId");
		}
		foreach ($perms as $p) {
			$p['ugid'] = $groupId;
			$p['switch'] = 'group';
			$this->create();
			if (!$this->save($p)) {
				throw new BeditaException(__("Error saving permissions for group", true), array($p));
			}
		}
	}
	
	/**
	 * Is object ($objectId) writable by user?
	 *
	 * @param integer $objectId
	 * @param array $userData user data, like array("id" => .., "userid" => ..., "groups" => array("administrator", "frontend",...))
	 * @param $perms permission array defined like in checkPermissionByUser() call
	 * 				 if it's defined use it else get permission by $objectId
	 * @return boolean, true if it's writable
	 */
	public function isWritable($objectId, array &$userData, $perms = array()) {
		// administrator can always write....
		if(!empty($userData['groups']) && in_array("administrator",$userData['groups'])) {
			return true;		
		}
		if (empty($perms)) {
			$perms = $this->isPermissionSet($objectId, Configure::read("objectPermissions.write"));
		}
		return $this->checkPermissionByUser($perms, $userData);
	}

	/**
	 * Is object ($objectId) forbidden to user?
	 * Backend only (check backend_private permission)
	 *
	 * @param integer $objectId
	 * @param array $userData user data, like array("id" => .., "userid" => ..., "groups" => array("administrator", "frontend",...))
	 * @return boolean, true if it's forbidden false if it's allowed
	 */
	public function isForbidden($objectId, array &$userData) {
		// no private objects for administrator
		if (!BACKEND_APP || ( !empty($userData['groups']) && in_array("administrator", $userData['groups'])) ) {
			return false;
		}

		$forbidden = false;
		$privatePermission = Configure::read("objectPermissions.backend_private");

		// check perms on main object ($objectId)
		$perms = $this->isPermissionSet($objectId, $privatePermission);
		$forbidden = !$this->checkPermissionByUser($perms, $userData);
		if ($forbidden) {
			return true;
		}

		// check if some branch parent is allowed, if so object is not forbidden
		$parentsPath = ClassRegistry::init('Tree')->find('list', array(
			'fields' => array('parent_path'),
			'conditions' => array('id' => $objectId)
		));

		if (!empty($parentsPath)) {
			foreach ($parentsPath as $path) {
				$path = trim($path, '/');
				$pathArr = explode('/', $path);
				$branchAllowed = array();
				foreach ($pathArr as $parentId) {
					$perms = $this->isPermissionSet($parentId, $privatePermission);
					$branchAllowed[] = $this->checkPermissionByUser($perms, $userData);
				}

				if (!in_array(false, $branchAllowed)) {
					$forbidden = false;
					break;
				} else {
					$forbidden = true;
				}
			}
		}

		return $forbidden;
	}

	/**
	 * Is object ($objectId) accessible by user in frontend?
	 * 
	 * @param $objectId
	 * @param $userData  user data, like array("id" => .., "userid" => ..., "groups" => array("administrator", "frontend",...))
	 * @param $perms permission array defined like in checkPermissionByUser() call
	 * 				 if it's defined use it else get permission by $objectId
	 * @return boolean, true if it's accessible
	 */
	public function isAccessibleByFrontend($objectId, array &$userData, $perms = array()) {
		if (empty($perms)) {
			$perms = $this->isPermissionSet($objectId, array(
				Configure::read("objectPermissions.frontend_access_with_block"),
				Configure::read("objectPermissions.frontend_access_without_block")
			));
		}
		return $this->checkPermissionByUser($perms, $userData);
	}
	
    /**
     * Return frontend level access to an object
     *
     * Possible returned values are:
     *
     * * 'free' if the object has not frontend_access perms
     * * 'denied' if the object isn't accessible (frontend_access_with_block perms set and user groups haven't that permission on that object)
     * * 'partial' if the object is accessible in preview (frontend_access_without_block perms set and user groups haven't that permission on that object)
     * * 'full' if the object has perms and user groups have that permission on that object
     *
     * @param int $objectId
     * @param array &$userData user data as
     *                         ```
     *                         array(
     *                             'id' => ..,
     *                             'userid' => ...,
     *                             'groups' => array('administrator', 'frontend',...)
     *                         )
     *                         ```
     * @return string
     */
	public function frontendAccess($objectId, array &$userData = array()) {
		$accessWithBlock = Configure::read('objectPermissions.frontend_access_with_block');
		$accessWithoutBlock = Configure::read('objectPermissions.frontend_access_without_block');
		$perms = $this->isPermissionSet($objectId, array($accessWithBlock, $accessWithoutBlock));

		// full access because no perms are set
		if (empty($perms)) {
			return 'free';
		}

		if (!empty($userData)) {
			// full access => one perm for user group
			if ($this->checkPermissionByUser($perms, $userData)) {
			    return 'full';
			}
		}

		$flags = Set::extract('/Permission/flag', $perms);

		// access denied => object has at least one perm 'frontend_access_with_block'
		if (in_array($accessWithBlock, $flags)) {
			return 'denied';
		}
		// partial access => object has at least one perm 'frontend_access_without_block'
		return 'partial';
	}

	/**
	 * check if user or user groups are in $perms array
	 * 
	 * @param $perms permission array like return from find("all)
	 * 						array(
	 * 							0 => array("Permission" => array(...), "User" => array(...), "Group" => array(...)),
	 * 							1 => ....
	 * 						)
	 * @param $userData user data, like array("id" => .., "userid" => ..., "groups" => array("administrator", "frontend",...))
	 * @return boolean (true if user have permission false otherwise)
	 */
	public function checkPermissionByUser($perms=array(), array &$userData) {
		if(empty($perms))
			return true;

		foreach ($perms as $p) {
			if(!empty($p['User']['id']) && $userData['id'] == $p['User']['id']) {
				return true;
			}
			if(!empty($p['Group']['name']) && !empty($userData['groups']) && in_array($p['Group']['name'], $userData['groups'])) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * check if a permission over an object is set
	 *
	 * @param integer $objectId
	 * @param array|integer $flag permission
	 * @return array of perms with users and groups or false if no permission is setted
	 */
	public function isPermissionSet($objectId, $flag) {
		if (!is_array($flag)) {
			$flag = array($flag);
		}
		// if frontend app (not staging) and object cache is active
		if (!BACKEND_APP && Configure::read('objectCakeCache') && !Configure::read('staging')) {
			$beObjectCache = BeLib::getObject('BeObjectCache');
			$options = array();
			$perms = $beObjectCache->read($objectId, $options, 'perms');
			if (!$perms && !is_array($perms)) {
				$perms = $this->find('all', array(
					'conditions' => array('object_id' => $objectId)
				));
				$beObjectCache->write($objectId, $options, $perms, 'perms');
			}
			// search $flag inside $perms
			$result = array();
			if (!empty($perms)) {
				foreach ($perms as $p) {
					if (in_array($p['Permission']['flag'], $flag)) {
						$result[] = $p;
					}
				}
			}
		} else {
			$result = $this->find('all', array(
				'conditions' => array('object_id' => $objectId, 'flag' => $flag)
			));
		}

		$ret = (!empty($result)) ? $result : false;
		return $ret;
	}
	
	/**
	 * Delete a permit for an object
	 *
	 * @param integer $id		object ID
	 * @param array $perms		array like (array("flag"=>1, "switch" => "group", "name" => "guest"), array(...))
	 */
	public function remove($objectId, $perms) {

		foreach ($perms as $p) {
			$conditions = array("object_id" => $objectId, "switch" => $p["switch"]);
			if (isset($p["flag"])) {
				$conditions["flag"] = $p["flag"];
			}
			if($p["switch"] == "group") {
				$group = ClassRegistry::init("Group");
				$conditions["ugid"] = $group->field('id', array('name' => $p["name"]));
			} else {
				$user = ClassRegistry::init("User");
				$conditions["ugid"] = $user->field('id', array('userid' => $p["name"]));
			}
			if(!$this->deleteAll($conditions, false))
				throw new BeditaException(__("Error removing permissions", true), "object id: $objectId");
		}
	}	

	/**
	 * Load all object permissions
	 *
	 * @param integer $objectId
	 * @return array (permissions)
	 */
	public function load($objectId) {
		return $this->find('all', array("conditions" => array("object_id" => $objectId)));
	}

	/**
	 * passed an array of BEdita objects add 'count_permission' key
	 * with the number of permissions applied to objects
	 *
	 * @param  array $objects
	 * @param  array $options
	 *         		- flag: if specified count permission with that flag
	 * @return array $objects with added 'count_permission' key
	 */
	public function countPermissions(array $objects, array $options) {
		foreach ($objects as &$obj) {
			$conditions = array('object_id' => $obj['id']);
			if (isset($options['flag'])) {
				$conditions['flag'] = $options['flag'];
			}
			$obj['num_of_permission'] = $this->find('count', array(
				'conditions' => $conditions
			));
		}
		return $objects;
	}

    /**
     * Return information about frontend not accessible related objects to $objectId
     *
     * If $options['count'] = false (default) it returns a list of object ids with permission 'frontend_access_with_block'
     * related to main object $objectId.
     * Passing also $options['relation'] it filters by relation name
     *
     * If $options['count'] = true it returns an array of count of objects with permission 'frontend_access_with_block'
     * related to main object $objectId and grouped by relation name
     *
     * Example:
     * ```
     * array(
     *     'attach' => 14,
     *     'seealso' => 7
     * )
     * ```
     *
     * If $user['groups'] is specified then it tests related objects against user groups and return a list
     * without objects allowed to user or a count of objects not allowed to user
     *
     * @param int $objectId the main object id
     * @param string $relation the relation name
     * @param array $user the user data on which check perms
     * @return array
     */
    public function relatedObjectsNotAccessibile($objectId, array $options = array(), array $user = array()) {
        $options += array(
            'relation' => null,
            'count' => false,
            'status' => null
        );

        $conditions = array(
            'Permission.flag' => Configure::read('objectPermissions.frontend_access_with_block'),
            'Permission.switch' => 'group',
            'ObjectRelation.id' => $objectId
        );
        if (!empty($options['relation'])) {
            $conditions['ObjectRelation.switch'] = $options['relation'];
        }

        if ($options['count']) {
            $findType = 'all';
            $fields = array('COUNT(DISTINCT(Permission.object_id)) as count, ObjectRelation.switch');
            $group = 'ObjectRelation.switch';
        } else {
            $findType = 'list';
            $fields = array('Permission.id', 'Permission.object_id');
            $group = 'Permission.object_id';
        }

        $joins = array(
            array(
                'table' => 'object_relations',
                'alias' => 'ObjectRelation',
                'type' => 'inner',
                'conditions' => array(
                    'Permission.object_id = ObjectRelation.object_id',
                )
            )
        );

        // if status is defined add join with objects
        if (!empty($options['status'])) {
            $joins[] = array(
                'table' => 'objects',
                'alias' => 'BEObject',
                'type' => 'inner',
                'conditions' => array(
                    'Permission.object_id = BEObject.id',
                    'BEObject.status' => $options['status']
                )
            );
        }

        $permission = ClassRegistry::init('Permission');
        $objectsForbidden = $this->find($findType, array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'group' => $group
        ));

        // get objects allowed to user
        if (!empty($user)) {
            if (!empty($user['groupsIds'])) {
                $conditions['Permission.ugid'] = $user['groupsIds'];
            } elseif (!empty($user['groups'])) {
                $groupList = ClassRegistry::init('Group')->getList(array(
                    'Group.name' => $user['groups']
                ));
                $conditions['Permission.ugid'] = array_keys($groupList);
            }
            $objectsAllowed = $this->find($findType, array(
                'fields' => $fields,
                'conditions' => $conditions,
                'joins' => $joins,
                'group' => $group
            ));
        }
        if (empty($objectsAllowed)) {
            $objectsAllowed = array();
        }

        if ($options['count']) {
            $relationsCount = array();
            $objectsAllowed = Set::combine($objectsAllowed, '{n}.ObjectRelation.switch', '{n}.{n}.count');
            foreach ($objectsForbidden as $detail) {
                $count = $detail[0]['count'];
                $switch = $detail['ObjectRelation']['switch'];
                if (!empty($objectsAllowed[$switch])) {
                    $count -= $objectsAllowed[$switch][0];
                }
                $relationsCount[$detail['ObjectRelation']['switch']] = $count;
            }
            return $relationsCount;
        } else {
            $objectsForbidden = array_diff(
                array_values($objectsForbidden),
                array_values($objectsAllowed)
            );
        }

        return $objectsForbidden;
    }

    /**
     * Return true if object $objectId and its parents are accessible i.e. for $user
     * 'Accessible' means without 'frontend_access_with_block' permission set
     *
     * $options params are:
     * - 'status' the status of parents to check
     * - 'area_id' the parents publication id
     * - 'stopIfMissingParents' true (default) to stop and return not valid if $objectId haven't parents and it isn't a publication
     *
     * @param int $objectId the object id
     * @param array $options
     * @param array $user the user data
     * @return boolean
     */
    public function isObjectsAndParentsAccessible($objectId, array $options = array(), array $user = array()) {
        $options += array(
            'status' => array(),
            'area_id' => null,
            'stopIfMissingParents' => true
        );
        $tree = ClassRegistry::init('Tree');
        $parents = $tree->getParents($objectId, $options['area_id'], $options['status']);
        // no parents
        if (empty($parents) && $options['stopIfMissingParents']) {
            $objectTypeId = ClassRegistry::init('BEObject')->findObjectTypeId($objectId);
            $areaObjectTypeId = Configure::read('objectTypes.area.id');
            // return false if object type is not area or if 'area_id' was passed and it's different from $objectId
            if ($objectTypeId != $areaObjectTypeId || (!empty($options['area_id']) && $objectId != $options['area_id'])) {
                return false;
            }
        }

        $conditions = array(
            'Permission.flag' => Configure::read('objectPermissions.frontend_access_with_block'),
            'Permission.switch' => 'group',
            'Permission.object_id' => array_merge(
                array($objectId),
                $parents
            )
        );

        $countForbidden = $this->find('count', array(
            'fields' => 'DISTINCT (Permission.object_id)',
            'conditions' => $conditions
        ));

        if (!empty($user)) {
            if (!empty($user['groupsIds'])) {
                $conditions['Permission.ugid'] = $user['groupsIds'];
            } elseif (!empty($user['groups'])) {
                $groupList = ClassRegistry::init('Group')->getList(array(
                    'Group.name' => $user['groups']
                ));
                $conditions['Permission.ugid'] = array_keys($groupList);
            }
            $joins = array();
            if (!empty($options['status'])) {
                $joins = array(
                    array(
                        'table' => 'objects',
                        'alias' => 'BEObject',
                        'type' => 'INNER',
                        'conditions' => array(
                            'BEObject.id = Permission.object_id',
                            'BEObject.status' => $options['status']
                        )
                    )
                );
            }
            $countAllowed = $this->find('count', array(
                'fields' => 'DISTINCT (Permission.object_id)',
                'conditions' => $conditions,
                'joins' => $joins
            ));

            if (is_numeric($countAllowed)) {
                $countForbidden -= $countAllowed;
            }
        }

        return $countForbidden == 0;
    }

}

<?php
/* -----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008-2011 ChannelWeb Srl, Chialab Srl
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
 * ------------------------------------------------------------------->8-----
 */

/**
 * UsersController: administrate users and groups
 *
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $LastChangedDate$
 * 
 * $Id$
 */
class UsersController extends ModulesController {
    
    public $uses = array('User', 'Group');
    
    public $helpers = array('Paginator');

    public $components = array('BeSecurity');
     
    public $paginate = array(
        'User' => array(
            'limit' => 20,
            'page' => 1,
            'order' => array('created' => 'desc')
        ),
        'Group' => array(
            'limit' => 20,
            'page' => 1,
            'order' => array('created' => 'desc')
        )
    );
    
    protected $moduleName = 'users';
    
    function index() {
        $users = $this->getUsers();
        $beObject = ClassRegistry::init("BEObject");
        foreach ($users as &$user) {
            $res = $beObject->find('list', array(
                "conditions" => "user_created=" . $user["User"]['id']
            ));

            if (!empty($res)) {
                $user['User']['related_obj'] = 1;
            } else {
                $user['User']['related_obj'] = 0;
            }
        }

        $this->set('users', $users);
    }

    /**
     * Return a paginated user list.
     * If 'query' filter is set add it to $conditions
     *
     * @param array $conditions
     * @return array
     */
    protected function getUsers($conditions = array()) {
        $query = $this->SessionFilter->read('query');
        if (empty($this->params["form"]["filter"]) || ($query && strlen($query) <= 3)) {
            $this->SessionFilter->clean();
        } elseif ($query) {
            $conditions = array(
                "OR" => array(
                    "User.userid LIKE" => $query . "%",
                    "User.realname LIKE" => "%" . $query . "%",
                    "User.email LIKE" => "%" . $query . "%"
                )
            );
        }

        $users = $this->paginate('User', $conditions);
        return $users;
    }

    /**
     * Load users that haven't a card related.
     * Used to promote card as user from Addressbook module
     *
     * @return void
     */
    function usersWithoutCard() {
        $this->layout = 'ajax';
        // clean session filter
        if (empty($this->params['form']['filter'])) {
            $this->SessionFilter->clean();
        }
        $objectUser = ClassRegistry::init('ObjectUser');
        $userIds = $objectUser->find('list', array(
            'fields' => array('user_id'),
            'conditions' => array(
                array('switch' => 'card')
            ),
            'group' => 'user_id'
        ));
        $this->paginate['User']['contain'] = array();
        $this->paginate['User']['order'] = array('userid' => 'asc');
        $users = $this->getUsers(array(
            'NOT' => array('id' => $userIds)
        ));
        $this->set('users', $users);
        if (isset($this->params['named']['page'])) {
            $this->render('inc/form_users_to_promote');
        }
    }

    /**
     * Load users that don't belong to given group.
     *
     * @return void
     */
    function showUsersToAddToGroup($id = null) {
        $this->layout = 'ajax';
        // clean session filter
        if (empty($this->params['form']['filter'])) {
            $this->SessionFilter->clean();
        }

        $g = $this->Group->findById($id);
        if (empty($g)) {
            throw new BeditaException(__("No group found with id", true) . " " . $id);
        }

        $not = array();
        foreach ($g['User'] as $u) {
            array_push($not, $u['id']);
        }

        $this->paginate['User']['contain'] = array();
        $this->paginate['User']['order'] = array('userid' => 'asc');
        $users = $this->getUsers(array(
            'NOT' => array('id' => $not)
        ));

        $this->set('users', $users);
        if (isset($this->params['named']['page'])) {
            $this->render('inc/form_users_to_associate');
        }
    }

    function userInGroupHtml($id = null) {
        $this->layout = 'ajax';
        $users = $this->getUsers(array(
            'id' => $id
        ));
        if (!empty($users)) {
            $this->set('u', $users[0]['User']);
        }
    }

    function saveUser() {

        $this->checkWriteModulePermission();

        $userGroups=array();
        if(isset($this->data['groups'] )) {
            foreach ($this->data['groups'] as $k=>$v) {
                array_push($userGroups, $k);
            }
        }

        //set userid param for external servie
        if ($this->data['User']['auth_type'] != 'bedita') {
            $type = $this->data['User']['auth_type'];
            if (isset($this->data['Service']) && isset($this->data['Service'][$type]) && isset($this->data['Service'][$type]['userid'])) {
                $this->data['User']['auth_params'] = $this->data['Service'][$type]['userid'];
            }
        }

        // Format custom properties
        $this->BeCustomProperty->setupUserPropertyForSave() ;

        if(!isset($this->data['User']['id'])) {
            if ($this->data['User']['auth_type'] == 'bedita') {
                if (!$this->BeAuth->checkConfirmPassword($this->params['form']['pwd'], $this->data['User']['passwd'])) {
                    throw new BeditaException(__("Passwords mismatch",true));
                }
            }

            $this->data['User']['passwd'] = trim($this->data['User']['passwd']);
            $this->BeAuth->createUser($this->data, $userGroups);
            $this->eventInfo("user ".$this->data['User']['userid']." created");
            $this->userInfoMessage(__("User created",true));
        } else {
            $pass = trim($this->data['User']['passwd']);
            $confirmPass = trim($this->params['form']['pwd']);
            if(empty($pass) && empty($confirmPass)) {
                unset($this->data['User']['passwd']);
            } elseif ($this->data['User']['auth_type'] == 'bedita') {
                if (!$this->BeAuth->checkConfirmPassword($this->params['form']['pwd'], $this->data['User']['passwd'])) {
                    throw new BeditaException(__("Passwords mismatch",true));
                }
            }
            $this->BeAuth->updateUser($this->data, $userGroups);
            $this->eventInfo("user ".$this->data['User']['userid']." updated");
            $this->userInfoMessage(__("User updated",true));
        }
    }

    function saveUserAjax () {
        $this->layout = null;
        $this->checkWriteModulePermission();
        try {
            $this->Transaction->begin() ;
            if(empty($this->data)) {
                throw new BeditaException(__("Empty data",true));
            }
            $userGroups=array();
            if(isset($this->data['groups'] )) {
                foreach ($this->data['groups'] as $k=>$v) {
                    array_push($userGroups, $k);
                }
            }
            $this->data['User']['passwd'] = substr($this->data['User']['userid'],0,4) . "+pwd";
            $this->BeAuth->createUser($this->data, $userGroups);
            $u = $this->User->findByUserid($this->data['User']['userid']);
            $this->eventInfo("user ".$this->data['User']['userid']." created");
            $this->Transaction->commit();
            $this->set("userId", $u['User']['id']);
            $this->set("userCreated", true);
        } catch (BeditaException $ex) {
            $errTrace = get_class($ex) . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
            $this->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
            $this->setResult(self::ERROR);
            $this->set("errorMsg", $ex->getMessage());
        }
    }
    
    function removeUser() {
        $this->checkWriteModulePermission();
        if (isset($this->data['id'])) {
            $id = $this->data['id'];
            $u = $this->isUserEditable($id);
            if ($u === false) {
                throw new BeditaException(__("You are not allowed to remove this user", true));
            }
            if (empty($u)) {
                throw new BeditaException(__("Bad data",true));
            }
            $userid = $u['User']['userid'];
            if ($userid === $this->BeAuth->user["userid"]) {
                throw new BeditaException(__("Auto-remove forbidden",true));
            }
            $this->BeAuth->removeUser($userid);
            $this->eventInfo("user ".$userid." deleted");
        }
    }

    function blockUser() {
        $this->checkWriteModulePermission();
        if (isset($this->data['id'])) {
            $id = $this->data['id'];
            if ($id === $this->BeAuth->user["userid"]) {
                throw new BeditaException(__("Auto-block forbidden",true));
            }

            $u = $this->User->findById($id);
            if (empty($u)) {
                throw new BeditaException(__("Bad data",true));
            }

            $data = array(
                "id" => $id,
                "valid" => 0,
                "userid" => "deleted-user-$id",
                "realname" => null,
                "email" => null
            );

            if (!$this->User->save($data)) {
                throw new BeditaException(__("Error blocking user", true));
            }
        }
    }

    /**
     * return user data if user in session can edit him
     * 
     * @param int $id
     * @return mixed 
     */
    protected function isUserEditable($id) {
        $userToEdit = $this->User->findById($id);
        if (!empty($userToEdit)) {
            $sessionUser = $this->BeAuth->getUserSession();
            $userGroups = Set::classicExtract($userToEdit, 'Group.{n}.name');
            if (!in_array("administrator", $sessionUser["groups"]) && in_array("administrator", $userGroups)) {
                return false;
            }
        }
        return $userToEdit;
    }


    function viewUser($id=NULL) {

        if(isset($id)) {
            $userdetail = $this->isUserEditable($id);
            if ($userdetail === false) {
                throw new BeditaException(__("You are not allowed to edit this user", true));
            }
            if(empty($userdetail)) {
                throw new BeditaException(__("Bad data",true));
            }
            $userdetailModules = ClassRegistry::init("PermissionModule")->getListModules($userdetail['User']['userid']);

        } else {
            
            $this->set('genpassword', substr( str_shuffle( 'abcdefghjkmnpqrstuvwxyz123456789' ) , 0 , 6 )); 
            $userdetail = NULL;
            $userdetailModules = NULL;
        }

        $this->set('externalAuthServices', $this->BeAuth->getExternalServices());

        $userGroups = array();
        if(isset($userdetail)) {
            foreach ($userdetail['Group'] as $g) {
                array_push($userGroups, $g['name']);
            }

            if ($userdetail['User']['userid'] == "deleted-user-" . $userdetail['User']['id']) {
                $this->set("userDeleted", true);
            }
        }
        
        $sessionUser = $this->BeAuth->getUserSession();
        $conditions = array();
        if (!in_array("administrator", $sessionUser["groups"])) {
            $conditions = array(
                "NOT" => array("name" => "administrator")
            );
        }
        $allGroups = $this->Group->find("all", array(
            "contain" => array(),
            "conditions" => $conditions
        ));
        
        $formGroups = array();
        $authGroups = array();
        foreach ($allGroups as $g) {
            $isGroup=false;
            if(array_search($g['Group']['name'],$userGroups) !== false) {
                $isGroup = true;
            }
            $formGroups[$g['Group']['name']] = $isGroup;
            if($g['Group']['backend_auth'] == 1) {
                $authGroups[] = $g['Group']['name'];
            }
        }

        $this->set('userdetail',  $userdetail['User']);
        if (is_array($userdetail["ObjectUser"])) {
            $this->set('objectUser', $this->objectRelationArray($userdetail["ObjectUser"]));
        }

        $this->set('formGroups',  $formGroups);
        $this->set('authGroups',  $authGroups);
        $this->set('userdetailModules', $userdetailModules);

        $property = $this->BeCustomProperty->setupUserPropertyForView($userdetail);
        $this->set('userProperty',  $property);

        BeLib::getObject("BeConfigure")->setExtAuthTypes();
    }

    function groups() {
        $query = $this->SessionFilter->read('query');
        if (empty($this->params["form"]["filter"]) || ($query && strlen($query) <= 3)) {
            $this->SessionFilter->clean();
        } elseif ($query) {
            $this->paginate["Group"]["conditions"] = array("Group.name LIKE" => $query . "%");
        }

        $this->Group->recursive = -1;
        $groups = $this->paginate('Group');
        foreach ($groups as &$g) {
            $g['Group']['num_of_users'] = $this->Group->countUsersInGroup($g["Group"]["id"]);
        }
        $this->set('groups', $groups);
        $this->set('group',  NULL);
        $this->set('modules', $this->allModulesWithFlag());
    }
     
    function viewGroup($id = null) {
        if(!empty($id)) {
            $g = $this->Group->findById($id);
            if (empty($g)) {
                throw new BeditaException(__("No group found with id", true) . " " . $id);
            }
            // find objects with permissions set for group
            $beObject = ClassRegistry::init('BEObject');
            $res = $beObject->find('all', array(
                'fields' => array('BEObject.id, BEObject.title, BEObject.nickname, BEObject.object_type_id, BEObject.status, Permission.id, Permission.flag'),
                'contain' => array(),
                'joins' => array(
                    array(
                        'table' => 'permissions',
                        'alias' => 'Permission',
                        'type' => 'inner',
                        'conditions'=> array(
                            'BEObject.id = Permission.object_id',
                            'Permission.ugid' => $g['Group']['id'],
                            'Permission.switch' => 'group'
                        )
                    )
                )
            ));

            // group permission by object
            $objects = array();
            foreach ($res as $key => $obj) {
                $objId = $obj['BEObject']['id'];
                if (empty($objects[$objId])) {
                    $objects[$objId] = $obj['BEObject'];
                }
                $objects[$objId]['Permission'][] = $obj['Permission'];
            }

            $g['objects'] = array_values($objects);
            $this->set('group', $g);
        }
        $modules = $this->allModulesWithFlag();
        $permsMod = ClassRegistry::init("PermissionModule")->getPermissionModulesForGroup($id);
        foreach ($permsMod as $p) {
            $modId = $p['PermissionModule']['module_id'];
            foreach ($modules as &$mod) {
                if($mod['Module']['id'] === $modId) {
                    $mod['Module']['flag'] = $p['PermissionModule']['flag'];
                }
            }
        }
        $this->set('modules', $modules);
    }

    /**
     * @throws BeditaException
     */
    function saveGroup() {
        $this->checkWriteModulePermission();

        $this->Transaction->begin();
        $newGroup = false;
        $groupId = $this->BeAuth->saveGroup($this->data);
        $groupName = $this->data['Group']['name'];

        //manage users to group
        if (!empty($this->data['users']) && !empty($groupName)) {
            
            $g = $this->Group->findById($groupId);
            $usersInGroup = array();
        
            foreach ($g['User'] as $u) {
                $usersInGroup[$u['id']] = true;
            }

            //add users to group
            foreach ($this->data['users'] as $userId) {
                $user = ClassRegistry::init("User")->find('first', array(
                    'conditions' => array(
                        'User.id' => $userId
                    )
                ));

                if (!empty($user)) {
                    unset($usersInGroup[$user['User']['id']]);
                    if (!isset($usersInGroup[$user['User']['id']])) {
                        $groupNames = array();
                        if (!empty($user['Group'])) {
                            foreach ($user['Group'] as $group) {
                                array_push($groupNames, $group['name']);
                            }
                        }
                    
                        array_push($groupNames, $groupName);
                        $this->BeAuth->updateUser(
                            array(
                                'User' => array(
                                    'id' => $user['User']['id'],
                                    'valid' => $user['User']['valid']
                                )
                            ),
                            $groupNames
                        );
                    }
                }                
            }

            //remove users from group
            foreach ($usersInGroup as $userId => $obj) {
                $user = ClassRegistry::init("User")->find('first', array(
                    'conditions' => array(
                        'User.id' => $userId
                    )
                ));

                if (!empty($user)) {
                    $groupNames = array();
                    if (!empty($user['Group'])) {
                        foreach ($user['Group'] as $group) {
                            if ($groupName != $group['name']) {
                                array_push($groupNames, $group['name']);
                            }
                        }
                    }
                
                    $this->BeAuth->updateUser(
                        array(
                            'User' => array(
                                'id' => $user['User']['id'],
                                'valid' => $user['User']['valid']
                            )
                        ),
                        $groupNames
                    );
                }      
            }
        }

        if (!isset($this->data['Group']['id'])) {
            $this->eventInfo("group ".$this->data['Group']['name']." created");
            $newGroup = true;
        } else {
            $this->eventInfo("group ".$this->data['Group']['name']." update");
        }
        if (isset($this->data['ModuleFlags'])) {
            $permissionModule = ClassRegistry::init("PermissionModule");
            $permissionModule->updateGroupPermission($groupId, $this->data['ModuleFlags']);
        }

        // replace perms
        $permission = ClassRegistry::init('Permission');
        $permissionToSave = array();
        $permissionToRemove = array();

        // get all previous permissions for that group
        $previousPerms = $permission->find('all', array(
            'conditions' => array(
                'ugid' => $groupId,
                'switch' => 'group'
            ),
            'contain' => array()
        ));

        if (isset($this->data['Permission'])) {
            $formPermission = $this->data['Permission'];
            if (!empty($previousPerms)) {
                foreach ($previousPerms as $p) {
                    $p = $p['Permission'];
                    // if prev permission 'object_id' isn't in $formPermission keys add it to $permissionToRemove
                    if (empty($formPermission[$p['object_id']])) {
                        $permissionToRemove[$p['id']] = $p['object_id'];
                    } else {
                        $formFlagKey = array_search($p['flag'], $formPermission[$p['object_id']]);
                        // if prev permission 'flag' for 'object_id' isn't in $formPermission add it to $permissionToRemove
                        if ($formFlagKey === false) {
                            $permissionToRemove[$p['id']] = $p['object_id'];
                        // else if it is present remove it from $formPermission to avoid useless save
                        } else {
                            if (count($formPermission[$p['object_id']]) > 1) {
                                unset($formPermission[$p['object_id']][$formFlagKey]);
                            } else {
                                unset($formPermission[$p['object_id']]);
                            }
                        }
                    }
                }
            }

            // in $formPermission remains only new permission to save
            foreach ($formPermission as $objectId => $flags) {
                foreach ($flags as $flag) {
                    $permissionToSave[] = array(
                        'object_id' => $objectId,
                        'ugid' => $groupId,
                        'switch' => 'group',
                        'flag' => $flag
                    );
                }
            }
        } elseif (!empty($previousPerms)) {
            $permissionToRemove = Set::combine($previousPerms, '{n}.Permission.id', '{n}.Permission.object_id');
        }

        // delete perms
        if (!empty($permissionToRemove)) {
            if (!$permission->deleteAll(array('Permission.id' => array_keys($permissionToRemove)), false)) {
                throw new BeditaException(__('Error removing permissions for group', true) . ' ' . $groupId);
            }
        }

        // save new perms
        if (!empty($permissionToSave)) {
            foreach ($permissionToSave as $p) {
                $permission->create();
                if (!$permission->save($p)) {
                    throw new BeditaException(__('Error saving permissions for group', true), array($p));
                }
            }
        }

        // if object cache is on clear cache
        if (Configure::read('objectCakeCache')) {
            $beObject = ClassRegistry::init('BEObject');
            $objectsToClean = Set::extract('/object_id', $permissionToSave);
            $objectsToClean = array_unique(
                array_merge($objectsToClean, $permissionToRemove)
            );
            $beObject->clearCacheByIds($objectsToClean);
        }

        $this->userInfoMessage(__("Group ".($newGroup? "created":"updated"),true));
        $this->Transaction->commit();
    }

    protected function addUsersToGroup() {

    }

    protected function removeUsersFromGroup() {

    }
      
    function removeGroup() {
        $this->checkWriteModulePermission();
        if (!isset($this->data['Group']['id'])) {
            throw new BeditaException(__('Missing data group to remove', true));
        }
        $id = $this->data['Group']['id'];
        $groupName = $this->Group->field("name", array("id" => $id));
        $this->Transaction->begin();
        $this->BeAuth->removeGroup($groupName);
        $this->Transaction->commit();
        $this->eventInfo("group ".$groupName." deleted");
        $this->userInfoMessage(__("Group deleted",true));
    }
    
    private function allModulesWithFlag() {
        $user = $this->BeAuth->getUserSession();
        $conditions = array();
        // if user doesn't belong to administrator group then exclude admin module
        if (!in_array("administrator", $user["groups"])) {
            $conditions = array(
                "NOT" => array("name" => array("admin"))
            );
        }
        $modules = ClassRegistry::init('Module')->find("all", array(
            "conditions" => $conditions
        ));
        foreach ($modules as &$mod) {
            $mod['Module']['flag'] = 0;
        }
        return $modules;
    }

    protected function forward($action, $result) {
        $moduleRedirect = array(
            'viewUser' =>   array(
                'ERROR' => $this->referer()
            ),
            'viewGroup' =>  array(
                'ERROR' => '/users/groups'
            ),
            'saveUser' =>   array(
                'OK'    => '/users/viewUser/' . @$this->User->id,
                'ERROR' => $this->referer() 
            ),
            'removeUser' =>     array(
                'OK'    => '/users',
                'ERROR' => '/users' 
            ),
            'blockUser' =>  array(
                'OK'    => '/users',
                'ERROR' => '/users' 
            ),
            'saveGroup' =>  array(
                'OK'    => '/users/viewGroup/' . @$this->Group->id,
                'ERROR' => $this->referer() 
            ),
            'removeGroup' =>    array(
                'OK'    => '/users/groups',
                'ERROR' => '/users/groups' 
            ),
            'saveUserAjax' =>   array(
                'OK'    => self::VIEW_FWD.'save_user_ajax_response',
                'ERROR' => self::VIEW_FWD.'save_user_ajax_response'
            )
        );
        return $this->moduleForward($action, $result, $moduleRedirect);
    }

}
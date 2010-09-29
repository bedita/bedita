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
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class User extends BEAppModel
{

	var $validate = array(
		'userid' => array(
			'rule' => 'notEmpty'
		),
		'passwd' => array(
			'rule' => 'notEmpty'
		),
		'email' => array(
			'rule' => 'email',
			'allowEmpty' => true
		)
	);

	protected $modelBindings = array( 
		"detailed" =>  array("Group", "ObjectUser", "Permission", "UserProperty"),
		"default" => array("Group", "ObjectUser", "UserProperty"),
		"minimum" => array()		
	);
	
	var $hasAndBelongsToMany = array('Group');

	var $hasMany = array(
		'Permission' =>
			array(
				'className'		=> 'Permission',
				'condition'		=> "Permission.switch = 'user' ",
				'fields'		=> 'Permission.object_id, Permission.switch, Permission.flag',
				'foreignKey'	=> 'id',
				'dependent'		=> true
			),
		'ObjectUser',
		'UserProperty'
	);

	private $hBTM = null; 
	
	public function passwordValidation(array &$userData) {
		$res = true;
		$validationRegExp = Configure::read("loginPolicy.passwordRule");
		if(!empty($validationRegExp) && !empty($userData["passwd"])) {
			$res = preg_match($validationRegExp, $userData["passwd"]);
			// change validation message on error??
		}		
		return $res;
	}

	function unbindGroups() {
        $this->hBTM = $this->hasAndBelongsToMany;
    	$this->unbindModel(array('hasAndBelongsToMany' => array('Group')), false);
    }
    	
    function rebindGroups() {
        $this->bindModel(array('hasAndBelongsToMany' => $this->hBTM));
    }
        
    /**
	 * Compact and reformat result
	 * 		id => ; passwd => ; realname => ; userid => ; groups => array({1..N} nomi_grupppi) : UserProperty => array()
	 *
	 * @param array $user
	 */
	function compact(&$user) {
		unset($user['Permission']);
		
		$user['User']['groups'] = array();
		if (!empty($user['Group'])) {
			foreach ($user['Group'] as $group) {
				$user['User']['groups'][] = $group['name'];
			}

			unset($user['Group']);
		}

		if (!empty($user['UserProperty'])) {
			$user["User"]["UserProperty"] = array();
			foreach ($user['UserProperty'] as $up) {
				if (empty($up["value"])) {
					$value = false;
				} elseif (!empty($up["value"]["property_value"])) {
					$value = $up["value"]["property_value"];
				} else {
					$value = Set::extract("/value/property_value", $up);
				}
				$user["User"]["UserProperty"][$up["name"]] = $value;
			}
		}
		
		$user = $user['User'] ;
	}
	
	public function getUsersToNotify($conditions) {
		$conditionBase = array("email IS NOT NULL AND email <> '' AND valid=1");
		$conditions = array_merge($conditionBase, $conditions);
		return $this->find("all", array(
				"fields" => array("id", "userid", "realname", "passwd", "email", "lang"),
				"conditions" => $conditions,
				"contain" => array()
				)
			);
	}
	
	function afterFind($results) {
		if(!empty($results[0]) || !empty($results["User"])) {
			foreach ($results as &$u) {
				if (!empty($u['User']['auth_params'])) {
					$u['User']['auth_params'] = unserialize($u['User']['auth_params']);
				}
			}
		}
		
		foreach ($results as &$u) {
			// format object properties
			if(!empty($u['UserProperty']) && is_array($u['UserProperty'])) {
				$propertyModel = ClassRegistry::init("Property");
				$property = $propertyModel->find("all", array(
									"conditions" => array("object_type_id" => null),
									"contain" => array("PropertyOption")
								)
							);
				if (!empty($property)) {
					foreach ($property as $keyProp => $prop) {					
						foreach ($u["UserProperty"] as $k => $value) {
							if ($value["property_id"] == $prop["id"]) {
								
								if ($prop["multiple_choice"] != 0) {
									$property[$keyProp]["value"][] = $value;
								} else { 
									$property[$keyProp]["value"] = $value;
								}
								
								// set selected to true in PropertyOption array
								if (!empty($prop["PropertyOption"])) {
									foreach ($prop["PropertyOption"] as $n => $option) {
										if ($option["property_option"] == $value["property_value"]) {
											$property[$keyProp]["PropertyOption"][$n]["selected"] = true;
										}
									}
								}
								unset($u["UserProperty"][$k]);
							}
						}
					}
					$u["UserProperty"] = $property;
					unset($property);
				}else {
					unset($u['UserProperty']);
				}
			}
		}
		
		return $results;
	}
	
	function beforeSave() {
		if (isset($this->data["User"]["email"]) && empty($this->data["User"]["email"])) {
			$this->data["User"]["email"] = null;
		}
		if (!empty($this->data["User"]["auth_params"]) && is_array($this->data["User"]["auth_params"])) {
			$this->data["User"]["auth_params"] = serialize($this->data["User"]["auth_params"]);
		} elseif (!empty($this->data["User"][0])) {
			foreach ($this->data["User"] as &$u) {
				if (!empty($u["auth_params"]) && is_array($u["auth_params"])) {
					$u["auth_params"] = serialize($u["auth_params"]);
				}
			}
		}

		if (empty($this->data["User"]["id"])) {
			if (empty($this->data["User"]["comments"])) {
				$this->data["User"]["comments"] = Configure::read("notifyOptions.comments");
			}
			if (empty($this->data["User"]["notes"])) {
				$this->data["User"]["notes"] = Configure::read("notifyOptions.notes");
			}
		}

		return true;
	}
	
	/**
	 * Salva i dati delle associazioni tipo hasMany
	 */
	function afterSave() {
		if (!empty($this->data['UserProperty'])) {
			
			$this->UserProperty->deleteAll(array('user_id' => $this->id));
			foreach($this->data['UserProperty'] as $prop) {
				$this->UserProperty->create();
				$prop['user_id'] = $this->id; 
				if (!$this->UserProperty->save($prop))
					throw new BeditaException(__("Error saving user", true), "Error saving hasMany user property");
			}
			
		}
			
	}
	
}
?>

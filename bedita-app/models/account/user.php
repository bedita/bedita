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
class User extends BEAppModel
{

    var $validate = array(
        'userid' => array(
            'useridRule1' => array(
                'rule' => 'notEmpty',
                'message' => 'User ID can not be empty',
            ),
            'useridRule2' => array(
                'rule' => array('minLength', 2),
                'message' => 'User ID must be at least 2 (two) characters long',
            ),
            'useridRule3' => array(
                'rule' => 'useridValidation',
                'message' => 'User ID can not contain illegal characters',
            ),
        ),
        'passwd' => array(
            'rule' => 'notEmpty',
        ),
        'email' => array(
            'rule' => 'email',
            'allowEmpty' => true,
        )
    );

	var $externalServiceValidate  = array(
		'userid' => array(
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

    public function useridValidation($values) {
        $value = array_shift($values);  // Get value.

        return preg_match('/^[[:print:]]+$/', $value) && preg_match('/^[^<>\|&\'"]+$/', $value);
    }

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
		$conditionBase = array("email IS NOT NULL AND email <> '' AND valid='1'");
		$conditions = array_merge($conditionBase, $conditions);
		return $this->find("all", array(
				"fields" => array("id", "userid", "realname", "passwd", "email", "lang"),
				"conditions" => $conditions,
				"contain" => array("Group")
				)
			);
	}
	
	function afterFind($results) {
		if(!empty($results[0]) || !empty($results["User"])) {
			foreach ($results as &$u) {
				if (!empty($u['User']['auth_params'])) {
					$auth_params = @unserialize($u['User']['auth_params']);
					if ($auth_params !== false) {
						$u['User']['auth_params'] = $auth_params;
					}
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
	
	public function beforeValidate() {
		App::import('Sanitize');
		if (!empty($this->data['User']['realname'])) {
			$this->data['User']['realname'] = Sanitize::stripAll($this->data['User']['realname']);
		}
	}

	function beforeSave() {
		if (isset($this->data["User"]["email"])) {
			if (empty($this->data["User"]["email"])) {
				$this->data["User"]["email"] = null;
			} else {
				$conditions = array("email" => $this->data["User"]["email"]);
				if(!empty($this->data["User"]["id"])) {
					$conditions[] = "id <> " . $this->data["User"]["id"];
				}
				$email = $this->field("email", $conditions);
				if(!empty($email)) {
					throw new BeditaException(__("Email already in use", true) . ": " . $email);
				}
			}
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

        // #573 - Automatic Card creation.
        $hasCardAssoc = $this->find('count', array(
            'contain' => array(),
            'joins' => array(
                array(
                    'table' => 'object_users',
                    'alias' => 'ObjectUser',
                    'type' => 'INNER',
                    'conditions' => array('ObjectUser.user_id = User.id', 'ObjectUser.switch' => 'card'),
                ),
            ),
            'conditions' => array('User.id' => $this->id),
        ));
        if (!$hasCardAssoc) {
            $Card = ClassRegistry::init('Card');
            $data = null;
            if (BACKEND_APP && isset($this->data['User']['_cardToAssoc']) && $this->data['User']['_cardToAssoc'] != null) {
                // Fetch data of chosen card.
                $data = $Card->find('first', array(
                    'conditions' => array('Card.id' => $this->data['User']['_cardToAssoc']),
                ));
            }
            if (empty($data)) {
                // Initialize new card.
                $name = explode(' ', isset($this->data['User']['realname']) ? $this->data['User']['realname'] : '', 2);
                $data = array(
                    'id' => null,
                    'title' => !empty($this->data['User']['realname']) ? $this->data['User']['realname'] : $this->data['User']['userid'],
                    'name' => (count($name) > 0) ? $name[0] : '',
                    'surname' => (count($name) > 1) ? $name[1] : '',
                    'email' => isset($this->data['User']['email']) ? $this->data['User']['email'] : '',
                    'status' => 'on',
                    'ObjectUser' => array(),
                );
                $Card->create();
            }
            $data['ObjectUser']['card'] = array(
                // Association data.
                array(
                    'user_id' => $this->id,
                    'object_id' => $data['id'],
                    'switch' => 'card',
                ),
            );
            $Card->save($data);  // Save card and association.
        }
	}

	function beforeDelete() {
		$beObject = ClassRegistry::init("BEObject");
		$res = $beObject->find('list', array(
			"conditions" => "user_created=" . $this->id
		));
		if (!empty($res)) {
			throw new BeditaException(__("Error deleting User",true), "Error deleting User, related objects present");
		}
		return true;
	}

    /**
    * Returns a list of Groups User belongs to.
    *
    * @param int $id User's ID (currently loaded user if omitted).
    * @return array ID-name list of Groups User belongs to.
    */
    public function getGroups ($id = null) {
        if (!is_null($id)) {
            $this->id = $id;
            $this->read();
        }

        return Set::combine($this->data['Group'], '{n}.id', '{n}.name');
    }
}
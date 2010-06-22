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
 * Basical object
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BEObject extends BEAppModel
{
	var $actsAs = array();
	
	var $name = 'BEObject';
	var $useTable	= "objects" ;
	
	var $validate = array(
//		'title' => array(
//			'rule' => 'notEmpty'
//		),
		'object_type_id' => array(
			'rule' => 'notEmpty'
		),
		'nickname' => array(
			'rule' => 'notEmpty'
		),
		'lang' => array(
			'rule' => 'notEmpty'
		),
		'ip_created' => array(
			'rule' => 'notEmpty'
		)
	) ;

	var $belongsTo = array(
		'ObjectType' =>
			array(
				'className'		=> 'ObjectType',
				'foreignKey'	=> 'object_type_id',
				'conditions'	=> ''
			),
		'UserCreated' =>
			array(
				'className'		=> 'User',
				'fields'		=> 'id, userid, realname',
				'foreignKey'	=> 'user_created',
			),
		'UserModified' =>
			array(
				'className'		=> 'User',
				'fields'		=> 'id, userid, realname',
				'foreignKey'	=> 'user_modified',
			),
	) ;
	
	var $hasMany = array(	
		'Permission',

		'Version' =>
			array(
				'className'		=> 'Version',
				'foreignKey'	=> 'object_id',
				'dependent'		=> true
			),
			
		'ObjectProperty' =>
			array(
				'className'		=> 'ObjectProperty',
				'foreignKey'	=> 'object_id',
				'dependent'		=> true
			),			
		'SearchText' =>
			array(
				'foreignKey'	=> 'object_id',
				'dependent'		=> true
			),
		'LangText' =>
			array(
				'className'		=> 'LangText',
				'foreignKey'	=> 'object_id',
				'dependent'		=> true
			),
		'Annotation' =>
			array(
				'foreignKey'	=> 'object_id',
				'dependent'		=> true
			),
		'RelatedObject' =>
			array(
				'className'				=> 'ObjectRelation',
				'joinTable'    			=> 'object_relations',
				'foreignKey'   			=> 'id',
				'associationForeignKey'	=> 'object_id',
				'order'					=> 'priority'
			),
		'Alias'
		);
	
	var $hasAndBelongsToMany = array(
		'Category' =>
			array(
				'className'				=> 'Category',
				'joinTable'    			=> 'object_categories',
				'foreignKey'   			=> 'object_id',
				'associationForeignKey'	=> 'category_id',
				'unique'				=> true
			),
		'User' =>
			   array(
			    'className'    			=> 'User',
			    'joinTable'       		=> 'object_users',
			    'foreignKey'      		=> 'object_id',
			    'associationForeignKey' => 'user_id',
			    'unique'    			=> true,
			   	'with' 					=> 'ObjectUser'
			   )
	);	

	/**
	 * Formatta i dati specifici dopo la ricerca
	 */	
	function afterFind($result) {
		
		// format object properties
		if(!empty($result['ObjectProperty'])) {
			$propertyModel = ClassRegistry::init("Property");
			$property = $propertyModel->find("all", array(
								"conditions" => array("object_type_id" => $result["object_type_id"]),
								"contain" => array("PropertyOption")
							)
						);
			
			foreach ($property as $keyProp => $prop) {
				
				foreach ($result["ObjectProperty"] as $k => $value) {
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
						
						unset($result["ObjectProperty"][$k]);
					}
				}
				
			}
			$result["ObjectProperty"] = $property;
//			pr($property);
			unset($property);
		}
		
		// set up LangText for view
		if (!empty($result['LangText'])) {
			$langText = array();
			foreach ($result['LangText'] as $lang) {
				if (!empty($lang["name"]) && !empty($lang["lang"])) {
					$langText[$lang["name"]][$lang["lang"]] = $lang["text"];
					$langText[$lang["object_id"]][$lang["lang"]][$lang["name"]] = $lang["id"];
				}
			}
			$result['LangText'] = $langText;
		}
		
		// divide tags from categories
		if (!empty($result["Category"])) {
			
			$tag = array();
			$category = array();
			
			foreach ($result["Category"] as $ot) {
				if (!empty($ot["object_type_id"])) {
					$category[] = $ot;
				} else {
					$tag[] = $ot;
				}
			}
			
			$result["Category"] = $category;
			$result["Tag"] = $tag;
		}

		if (!empty($result["Permission"])) {
			foreach ($result["Permission"] as &$perm) {
				if ($perm["switch"] == "group") {
					$perm["name"] = $this->Permission->Group->field("name", array("id" => $perm["ugid"]));
				} elseif ($perm["switch"] == "user") {
					$perm["name"] = $this->Permission->User->field("name", array("id" => $perm["ugid"]));
				}
			}
		}
		
		return $result ;
	}

	function beforeSave() {
		// format custom properties and searchable text fields
		$labels = array('SearchText');
		foreach ($labels as $label) {
		  if(!isset($this->data[$this->name][$label])) 
			continue ;
			
		  if(is_array($this->data[$this->name][$label]) && count($this->data[$this->name][$label])) {
		      $tmps = array() ;
		      foreach($this->data[$this->name][$label]  as $k => $v) {
					$this->_value2array($k, $v, $arr) ;
					$tmps[] = $arr ;

				}
			$this->data[$this->name][$label] = $tmps ;
		  }
		}
		$this->unbindModel(array("hasMany"=>array("LangText","Version")));
		$this->unbindModel(array("hasAndBelongsToMany"=>array("User")));
		return true ;
	}
	
	/**
	 * Salva i dati delle associazioni tipo hasMany
	 */
	function afterSave() {
		
		// hasMany relations
		foreach ($this->hasMany as $name => $assoc) {
			// skip specific manage
			if($name == 'Permission' || $name == 'RelatedObject' || $name == 'Annotation') {
				continue ;
			}

			// if not set data array do nothing
			if(!isset($this->data[$this->name][$name])) {
				continue ;
			}
			
			$db 		=& ConnectionManager::getDataSource($this->useDbConfig);
			$model 		= new $assoc['className']() ; 
			
			// delete previous associations
			$table 		= (isset($model->useTable)) ? $model->useTable : ($db->name($db->fullTableName($assoc->className))) ;
			$id 		= (isset($this->data[$this->name]['id'])) ? $this->data[$this->name]['id'] : $this->getInsertID() ;		
			$foreignK	= $assoc['foreignKey'] ;
			// #CUSTOM QUERY
			$db->query("DELETE FROM {$table} WHERE {$foreignK} = '{$id}'");
			
			if (!(is_array($this->data[$this->name][$name]) && count($this->data[$this->name][$name]))) {
				continue ;
			}
			
			// save associations
			$size = count($this->data[$this->name][$name]) ;
			for ($i=0; $i < $size ; $i++) {
				$modelTmp	 	 = new $assoc['className']() ; 
				$data 			 = &$this->data[$this->name][$name][$i] ;
				$data[$foreignK] = $id ; 
				if(!$modelTmp->save($data))
					throw new BeditaException(__("Error saving object", true), "Error saving hasMany relation in BEObject for model " . $assoc['className']);
				
				unset($modelTmp);
			}
						
		}

		// build ObjectUser Relation
		if (isset($this->data['BEObject']["ObjectUser"])) {
			$objectUserModel = ClassRegistry::init("ObjectUser");
			if (empty($this->data['BEObject']["ObjectUser"])) {
				$objectUserModel->deleteAll(array(
						"object_id" => $this->id
					)
				);
			} else {
				foreach ($this->data['BEObject']["ObjectUser"] as $switch => $objUserArr) {
					$objectUserModel->deleteAll(array(
							"object_id" => $this->id,
							"switch" => $switch
						)
					);
					if (is_array($objUserArr)) {
						foreach ($objUserArr as $objUserData) {
							if (!empty($objUserData["user_id"])) {
								if (empty($objUserData["object_id"]))
									$objUserData["object_id"] = $this->id;
								$objectUserModel->create();
								if (!$objectUserModel->save($objUserData)) {
									throw new BeditaException(__("error saving object_users relations",true));
								}
							}
						}
					}
				}
			}
		}
				
		$permissions = false;
		if(isset($this->data["Permission"]))
			$permissions = $this->data["Permission"] ;
		else if(isset($this->data[$this->name]["Permission"]))
			$permissions = $this->data[$this->name]["Permission"] ;
		
		if(is_array($permissions)) {
			$this->Permission->replace($this->{$this->primaryKey}, $permissions);
		}
		// save relations between objects
		if (!empty($this->data['BEObject']['RelatedObject'])) {
			
			
			$db 		= &ConnectionManager::getDataSource($this->useDbConfig);
			$queriesDelete 	= array() ;
			$queriesInsert 	= array() ;
			$queriesModified 	= array() ;
			$lang			= (isset($this->data['BEObject']['lang'])) ? $this->data['BEObject']['lang']: null ;
			
			// set one-way relation
			$oneWayRelation = array_merge( Configure::read("defaultOneWayRelation"), Configure::read("cfgOneWayRelation") );
			
			$assoc 	= $this->hasMany['RelatedObject'] ;
			$table 	= $db->name($db->fullTableName($assoc['joinTable']));
			$fields = $assoc['foreignKey'] .",".$assoc['associationForeignKey'].", switch, priority"  ;

			foreach ($this->data['BEObject']['RelatedObject'] as $switch => $values) {
				
				foreach($values as $key => $val) {
					$obj_id		= isset($val['id'])? $val['id'] : false;
					$priority	= isset($val['priority']) ? "'{$val['priority']}'" : 'NULL' ;
					
					// Delete old associations
					// #CUSTOM QUERY
					$queriesDelete[$switch] = "DELETE FROM {$table} 
											   WHERE ({$assoc['foreignKey']} = '{$this->id}' OR {$assoc['associationForeignKey']} = '{$this->id}')  
											   AND switch = '{$switch}' ";
					if (!empty($obj_id)) {
						// #CUSTOM QUERY
						$queriesInsert[] = "INSERT INTO {$table} ({$fields}) VALUES ({$this->id}, {$obj_id}, '{$switch}', {$priority})" ;
						
						if(!in_array($switch,$oneWayRelation)) {
							// find priority of inverse relation
							// #CUSTOM QUERY
							$inverseRel = $this->query("SELECT priority 
														  FROM {$table} 
														  WHERE id={$obj_id} 
														  AND object_id={$this->id} 
														  AND switch='{$switch}'");
							
							if (empty($inverseRel[0]["content_objects"]["priority"])) {
								// #CUSTOM QUERY
								$inverseRel = $this->query("SELECT MAX(priority)+1 AS priority FROM {$table} WHERE id={$obj_id} AND switch='{$switch}'");
								$inversePriority = (empty($inverseRel[0][0]["priority"]))? 1 : $inverseRel[0][0]["priority"];
							} else {
								$inversePriority = $inverseRel[0]["content_objects"]["priority"];
							}						
							// #CUSTOM QUERY
							$queriesInsert[] = "INSERT INTO {$table} ({$fields}) VALUES ({$obj_id}, {$this->id}, '{$switch}', ". $inversePriority  .")" ;
						}
						
						/**
						 * Proposta x salvare le modifiche a title e description di oggetto relazionato se ci sono i dati sufficenti. (giangi) 
						 */
						$modified = (isset($val['modified']))? ((boolean)$val['modified']) : false;
						if($modified && $obj_id) {
							$title 		= isset($val['title']) ? addslashes($val['title']) : "" ;
							if($switch == 'link') {
								// #CUSTOM QUERY
								$queriesModified[] = "UPDATE objects  SET title = '{$title}' WHERE id = {$obj_id} " ;
								$link = ClassRegistry::init('Link');
								$link->id = $obj_id;
								$link->saveField('url',$val['url']);
							} else {
								$description 	= isset($val['description']) ? addslashes($val['description']) : "" ;
								// #CUSTOM QUERY
								$queriesModified[] = "UPDATE objects  SET title = '{$title}', description = '{$description}' WHERE id = {$obj_id} " ;
							}
						}
					}
				}
			}

			foreach ($queriesDelete as $qDel) {
				if ($db->query($qDel) === false)
					throw new BeditaException(__("Error deleting associations", true), $qDel);
			}
			foreach ($queriesInsert as $qIns) {
				if ($db->query($qIns)  === false)
					throw new BeditaException(__("Error inserting associations", true), $qIns);
			}
			foreach ($queriesModified as $qMod) {
				if ($db->query($qMod)  === false)
					throw new BeditaException(__("Error modifying title and description", true), $qMod);
			}
		}
		
		return true ;
	}
	
	/**
	 * Definisce i valori di default.
	 */		
	function beforeValidate() {
		if(isset($this->data[$this->name])) 
			$data = &$this->data[$this->name] ;
		else 
			$data = &$this->data ;
	
		if(isset($data['title'])) {
			$data['title'] = trim($data['title']);
		}

		// set language -- disable for comments?
		if(!isset($data['lang'])) {
			$data['lang'] = $this->_getDefaultLang();
		}
		// check/set IP
		if(!isset($data['ip_created']) && !isset($data['id'])) {
			$data['ip_created'] = $this->_getDefaultIP();
		}
		// user created? - only for new objects
		if(empty($data['user_created']) && !isset($data['id'])) {
			$data['user_created'] = $this->_getIDCurrentUser();
		}
		// user modified
		if(!isset($data['user_modified'])) {
			$data['user_modified'] = $this->_getIDCurrentUser();
		}
		
		// nickname: verify nick and status change, object not fixed
		if(isset($data['id'])) {
			$currObj = $this->find("first", array(
											"conditions"=>array("BEObject.id" => $data['id']), 
											"fields" =>array("status", "nickname", "fixed"),
											"contain" => ("")
											));
			if($currObj['BEObject']['fixed'] == 1) {  // don't change nickname & status
				// throws exceptions if status/nicknames are changed
				if((!empty($data['status']) && $data['status'] != $currObj['BEObject']['status']) ||
				    (!empty($data['nickname']) && $data['nickname'] != $currObj['BEObject']['nickname'])) {
					throw new BeditaException(__("Error: modifying fixed object!", true));
				}
				$data['nickname'] = $currObj['BEObject']['nickname'];
				$data['status'] = $currObj['BEObject']['status'];
			} elseif (empty($data['nickname']) && !empty($currObj['BEObject']['nickname'])) {
				$data["nickname"] = $currObj['BEObject']['nickname'];
			} else {
				$data['nickname'] = $this->_getDefaultNickname($data['nickname']);
			}

		} else {
			$title = isset($data['title']) ? $data['title'] : null;
			$tmpName = !empty($data['nickname']) ? $data['nickname'] : $title;
			$data['nickname'] = $this->_getDefaultNickname($tmpName);
		}
		
		if(empty($data["user_created"])) unset($data["user_created"]) ;
		
		// format custom properties data type
		if (!empty($data["ObjectProperty"])) {
			foreach ($data["ObjectProperty"] as $key => $val) {
				if ($val["property_type"] == "date")
					$data["ObjectProperty"][$key]["property_value"] = $this->getDefaultDateFormat($val["property_value"]);
			}
		}
		
		// Se c'e' la chiave primaria vuota la toglie
		if(isset($data[$this->primaryKey]) && empty($data[$this->primaryKey]))
			unset($data[$this->primaryKey]) ;
			
		return true ;
	}

	public function findObjectTypeId($id) {
		$object_type_id = $this->field("object_type_id", array("BEObject.id" => $id));
		return $object_type_id;
	}

	/**
	 * Is object fixed??
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function isFixed($id) {
		$fixed = $this->field("fixed", array("BEObject.id" => $id));
		return ($fixed == 1);
	}	
	
	/**
	 * Model name/type from id
	 *
	 * @param unknown_type $id
	 */
	public function getType($id) {
		$type_id = $this->findObjectTypeId($id);
		if($type_id === false) {
			throw new BeditaException(__("Error: object type not found", true));
		}
		return Configure::getInstance()->objectTypes[$type_id]["model"] ;
	}
	
	/**
	* update title e description only.
	**/
	public function updateTitleDescription($id, $title, $description) {
		if(@empty($id) || @empty($title)) return false ;
		
		$db 		= &ConnectionManager::getDataSource($this->useDbConfig);
		// #CUSTOM QUERY
		$db->query("UPDATE objects  SET title =  '".addslashes($title)."', description = '".addslashes($description)."' WHERE id = {$id} " ) ;
		
		return true ;
	}	
	
	////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////
	
	/**
	 * Setta i valori di default per i diversi campi
	 */
	private function _getDefaultNickname($value) {
		
		if(is_null($value)) {
			$value = "";
		}
		if (is_numeric($value)) {
			$value = "n" . $value;
		}
		
		$value = htmlentities( strtolower($value), ENT_NOQUOTES, "UTF-8" );
		
		// replace accent, uml, tilde,... with letter after & in html entities
		$value = preg_replace("/&(.)(uml);/", "$1e", $value);
		$value = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $value);
		// replace special chars and space with dash (first decode html entities)
		$value = preg_replace("/[^a-z0-9\-_]/i", "-", html_entity_decode($value,ENT_NOQUOTES,"UTF-8" ) ) ;
		// remove digits and dashes in the beginning 
		$value = preg_replace("/^[0-9\-]{1,}/", "", $value);
		// replace two or more consecutive dashes with one dash
		$value = preg_replace("/[\-]{2,}/", "-", $value);
		// trim dashes in the beginning and in the end of nickname
		$nickname = $nickname_base = trim($value,"-");

		$conf = Configure::getInstance() ;
		$nickOk = false;
		$countNick = 1;
		$reservedWords = array_merge ( $conf->defaultReservedWords, $conf->cfgReservedWords );
		if(empty($nickname)) {
			$objTypeId = $this->data['BEObject']['object_type_id'];
			$nickname_base = $conf->objectTypes[$objTypeId]["name"]; // default name - model type name
			$nickname = $nickname_base . "-0";
		};


		$aliasModel = ClassRegistry::init("Alias");		
		while (!$nickOk) {
			
			$cond = "WHERE BEObject.nickname = '{$nickname}'";
			if ($this->id) {
				$cond .= " AND BEObject.id<>".$this->id;
			}
			$numNickDb = $this->find("count", array("conditions" => $cond, "contain" => array()));
			
			// check nickname in db and in reservedWords
			if ($numNickDb == 0 && !in_array($nickname, $reservedWords)) {
				// check aliases
				$object_id = $aliasModel->field("object_id", array("nickname_alias" => $nickname));
				if(empty($object_id)) {
					$nickOk = true;
				}
			}
			if(!$nickOk) {
				$nickname = $nickname_base . "-" . $countNick++;
			}
		}
		
		return $nickname ;
	}
	
	private function _getDefaultLang() {
		$conf = Configure::getInstance() ;
		return ((isset($conf->defaultLang))?$conf->defaultLang:'') ;
	}
	
//	private function _getDefaultPermission($value, $object_type_id) {
//		if(isset($value) && is_array($value)) return $value ;
//		
//		$conf = Configure::getInstance() ;
//		$permissions = &$conf->permissions ;
//		
//		// Aggiunge i permessi di default solo se sta creando un nuovo oggetto
//		if(isset($this->data[$this->name][$this->primaryKey])) return null ;
//		
//		// Seleziona i permessi in base al tipo di oggetti
//		if(isset($permissions[$object_type_id])) 	return $permissions[$object_type_id] ;
//		else if (isset($permissions['all']))		return $permissions['all'] ;
//		
//		return null ;
//	}
	
	private function _getDefaultIP() {
		$IP = $_SERVER['REMOTE_ADDR'] ;
		return $IP ;
	}
	
	private function _getIDCurrentUser() {
		// read user data from session or from configure
		$conf = Configure::getInstance();
		$userId=0; 

		if(isset($conf->beditaTestUserId)) {
			$userId = $conf->beditaTestUserId; // unit tests
		} else {
		
			$session = @(new CakeSession()) ;
			
			if($session->valid() === false)
				return null;
			$user = $session->read($conf->session["sessionUserKey"]) ; 
			if(!isset($user["id"])) 
				return null ;
			$userId = $user["id"];	
		}
		
		return $userId;
	}
	
	/**
	 * torna un array con la variabile archiviata in un array
	 */
	private function _value2array($name, &$val, &$arr) {
		$type = null ; 
		switch(gettype($val)) {
			case "integer" : 	{ $type = "integer" ; } break ;
			case "boolean" : 	{ $type = "bool" ; } break ;
			case "double" : 	{ $type = "float" ; } break ;
			case "string" :		{ $type = "string" ; } break ;
					
			default: {
				$type = "stream" ;
				$val = serialize($val) ;
 			}
		}
		$arr = array(
			'name'		=> $name,
			'type'		=> $type,
			$type		=> $val
		) ;	
	}
		
	function getIdFromNickname($nickname) {
		$id = $this->field("id", array("nickname" => $nickname));
		if(empty($id)) { // if nickname not found lookup aliases
			$aliasModel = ClassRegistry::init("Alias");
			$id = $aliasModel->field("object_id", array("nickname_alias" => $nickname));
		}
		return $id; 
	}

	function getNicknameFromId($id) {
		return $this->field("nickname", array("id" => $id));
	}
		
}
?>

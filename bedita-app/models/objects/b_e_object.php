<?php
/**
 *
 * PHP versions 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2007
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license
 * @author 		giangi giangi@qwerg.com			
*/
class BEObject extends BEAppModel
{
	var $name = 'BEObject';
	var $useTable	= "objects" ;
	
	var $validate = array(
		'title' 			=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
		'object_type_id' 	=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
		'nickname' 			=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
		'lang' 				=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
		'ip_created' 		=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),

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
		'Permissions' =>
			array(
				'className'		=> 'ViewPermission',
				'fields'		=> 'name, switch, flag',
				'foreignKey'	=> 'object_id',
			),

		'Version' =>
			array(
				'className'		=> 'Version',
				'foreignKey'	=> 'object_id',
				'dependent'		=> true
			),
			
		'CustomProperties' =>
			array(
				'className'		=> 'CustomProperty',
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
	);
	
	var $hasAndBelongsToMany = array(
		'RelatedObject' =>
			array(
				'className'				=> 'BEObject',
				'joinTable'    			=> 'object_relations',
				'foreignKey'   			=> 'id',
				'associationForeignKey'	=> 'object_id',
				'unique'				=> true,
				'order'					=> 'priority'
			),
		'Category' =>
			array(
				'className'				=> 'Category',
				'joinTable'    			=> 'object_categories',
				'foreignKey'   			=> 'object_id',
				'associationForeignKey'	=> 'category_id',
				'unique'				=> true
			)
	);	

	/**
	 * Formatta i dati specifici dopo la ricerca
	 */	
	function afterFind($result) {
		
		if(isset($result['CustomProperties'])) {
		
			// Formatta le custom properties
			$props 	= &$result['CustomProperties'] ;
			$tmps 	= array() ;
			
			$size = count($result['CustomProperties']) ;
			for($i=0; $i < $size ; $i++) {
				$record = &$props[$i] ;
					
				// carica le proprieta' custom
				$val = null ;
				switch($record["type"]) {
					case "integer" : 	{ $val = $record["integer"] ; settype($val, "integer") ; } break ;
					case "bool" : 		{ $val = $record["bool"] ; settype($val, "boolean") ; } break ;
					case "float" : 		{ $val = $record["float"] ; settype($val, "double") ; } break ;
					case "string" :		{ $val = $record["string"] ; settype($val, "string") ; } break ;
					case "stream" :		{ $val = unserialize($record["stream"]); } break ;
				}
					
				$tmps[$record['name']] = $val ;
			}
			$result['CustomProperties'] = $tmps ;
		}
		
		// set up LangText for view
		if (!empty($result['LangText'])) {
			$langText = array();
			foreach ($result['LangText'] as $lang) {
				if (!empty($lang["name"]) && !empty($lang["lang"])) {
					$langText[$lang["name"]][$lang["lang"]] = (!empty($lang["text"])) ? $lang["text"] : $lang["long_text"] ;
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
		
		return $result ;
	}

	function beforeSave() {
		// format custom properties and searchable text fields
		$labels = array('CustomProperties', 'SearchText');
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
		$this->unbindModel(array("hasMany"=>array("LangText")));

		// unbind relations type. Save it in aftersave
		$this->restoreRelatedObject = $this->hasAndBelongsToMany['RelatedObject'];
		$this->unbindModel( array('hasAndBelongsToMany' => array('RelatedObject')) );
		
		return true ;
	}
	
	/**
	 * Salva i dati delle associazioni tipo hasMany
	 */
	function afterSave() {
		
		// Scorre le associazioni hasMany
		foreach ($this->hasMany as $name => $assoc) {
			// Non gestisce i permessi
			if($name == 'Permissions') continue ;
			
			$db 		=& ConnectionManager::getDataSource($this->useDbConfig);
			$model 		= new $assoc['className']() ; 
			
			// Cancella le precedenti associazioni
			$table 		= (isset($model->useTable)) ? $model->useTable : ($db->name($db->fullTableName($assoc->className))) ;
			$id 		= (isset($this->data[$this->name]['id'])) ? $this->data[$this->name]['id'] : $this->getInsertID() ;		
			$foreignK	= $assoc['foreignKey'] ;
			
			$db->query("DELETE FROM {$table} WHERE {$foreignK} = '{$id}'");
			
			// Se non ci sono dati da salvare esce
			if(!isset($this->data[$this->name][$name])) continue ;
			
			if (!(is_array($this->data[$this->name][$name]) && count($this->data[$this->name][$name]))) continue ;
			
			// Salva le nuove associazioni
			$size = count($this->data[$this->name][$name]) ;
			for ($i=0; $i < $size ; $i++) {
				$modelTmp	 	 = new $assoc['className']() ; 
				$data 			 = &$this->data[$this->name][$name][$i] ;
				$data[$foreignK] = $id ; 
				if(!$modelTmp->save($data)) return false ;
				
				unset($modelTmp);
			}
		}
		
		// Salva eventuali permessi
		$permissions = false ;
		if(isset($this->data["Permissions"])) $permissions = $this->data["Permissions"] ;
		else if(isset($this->data[$this->name]["Permissions"])) $permissions = $this->data[$this->name]["Permissions"] ;
		
		if($permissions) {
		
			if(!class_exists('Permission')) {
				loadModel('Permission') ;
			}
			$Permission = new Permission() ;		
			// Aggiunge
			$this->_array2perms($permissions, $formatedPerms) ;
			for($i=0; $i < count($formatedPerms) ; $i++) {
				$item = &$formatedPerms[$i] ;
					
				if($Permission->replace($this->{$this->primaryKey}, $item['name'], $item['switch'], $item['flag']) === false) {
					return false ;
				}				
			}
		}
		
		
		// save realtions between objects
		if (!empty($this->data['RelatedObject'])) {
			
			$this->bindModel( array(
				'hasAndBelongsToMany' => array(
						'RelatedObject' => $this->restoreRelatedObject
							)
				) 
			);
			$db 		= &ConnectionManager::getDataSource($this->useDbConfig);
			$queriesDelete 	= array() ;
			$queriesInsert 	= array() ;
			$queriesModified 	= array() ;
			$lang			= (isset($this->data['BEObject']['lang'])) ? $this->data['BEObject']['lang']: null ;
			
			// set one-way relation
			$oneWayRelation = array_merge( Configure::read("defaultOneWayRelation"), Configure::read("cfgOneWayRelation") );
			
			$assoc 	= $this->hasAndBelongsToMany['RelatedObject'] ;
			$table 	= $db->name($db->fullTableName($assoc['joinTable']));
			$fields = $assoc['foreignKey'] .",".$assoc['associationForeignKey'].", switch, priority"  ;

			foreach ($this->data['RelatedObject']['RelatedObject'] as $switch => $values) {
				
				foreach($values as $key => $val) {
					$obj_id		= isset($val['id'])? $val['id'] : false;
					$priority	= isset($val['priority']) ? "'{$val['priority']}'" : 'NULL' ;
					
					// Delete old associations
					$queriesDelete[$switch] = "DELETE FROM {$table} 
											   WHERE ({$assoc['foreignKey']} = '{$this->id}' OR {$assoc['associationForeignKey']} = '{$this->id}')  
											   AND switch = '{$switch}' ";
					if (!empty($obj_id)) {
						$queriesInsert[] = "INSERT INTO {$table} ({$fields}) VALUES ({$this->id}, {$obj_id}, '{$switch}', {$priority})" ;
						
						if(!in_array($switch,$oneWayRelation)) {
							// find priority of inverse relation
							$inverseRel = $this->query("SELECT priority 
														  FROM {$table} 
														  WHERE id={$obj_id} 
														  AND object_id={$this->id} 
														  AND switch='{$switch}'");
							
							if (empty($inverseRel[0]["content_objects"]["priority"])) {
								$inverseRel = $this->query("SELECT MAX(priority)+1 AS priority FROM {$table} WHERE id={$obj_id} AND switch='{$switch}'");
								$inversePriority = (empty($inverseRel[0][0]["priority"]))? 1 : $inverseRel[0][0]["priority"];
							} else {
								$inversePriority = $inverseRel[0]["content_objects"]["priority"];
							}						
							$queriesInsert[] = "INSERT INTO {$table} ({$fields}) VALUES ({$obj_id}, {$this->id}, '{$switch}', ". $inversePriority  .")" ;
						}
						
						/**
						 * Proposta x salvare le modifiche a title e description di oggetto relazionato se ci sono i dati sufficenti. (giangi) 
						 */
						$modified = (isset($val['modified']))? ((boolean)$val['modified']) : false;
						if($modified && $obj_id) {
							$title 		= isset($val['title']) ? addslashes($val['title']) : "" ;
							$description 	= isset($val['description']) ? addslashes($val['description']) : "" ;
							
							$queriesModified[] = "UPDATE objects  SET title = '{$title}', description = '{$description}' WHERE id = {$obj_id} " ;
							
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
					throw new BeditaException(__("Error modifing title and description", true), $qMod);
			}
		}
		
		return true ;
	}
	
	/**
	 * Definisce i valori di default.
	 */		
	function beforeValidate() {
		if(isset($this->data[$this->name])) $data = &$this->data[$this->name] ;
		else $data = &$this->data ;
		
	 	$default = array(
			'nickname' 			=> array('_getDefaultNickname', 	(isset($data['nickname']) && !@empty($data['nickname']))?$data['nickname']:((isset($data['title']))?$data['title']:'')),
			'lang' 				=> array('_getDefaultLang', 		(isset($data['lang']))?$data['lang']:null),
			'ip_created' 		=> array('_getDefaultIP',			(isset($data['ip_created']))?$data['ip_created']:null),
			'user_created'		=> array('_getIDCurrentUser', 		((isset($data[$this->primaryKey]) && empty($data[$this->primaryKey])) || !isset($data[$this->primaryKey]))? (isset($data['user_created'])?$data['user_created']:true) :false),
			'user_modified'		=> array('_getIDCurrentUser', 		(isset($data['user_modified'])?$data['user_modified']:true)), 
			'Permissions' 		=> array('_getDefaultPermission', 	(isset($data['Permission']))?$data['Permission']:null, (isset($data['object_type_id']))?$data['object_type_id']:0),
		) ;
		
		foreach ($default as $name => $rule) {
			if(!is_array($rule)) {
				$data[$name] = $rule ;
				continue ;
			}
			
			$method = $rule[0];
			unset($rule[0]);
			
			if (method_exists($this, $method)) {
				$data[$name] = call_user_func_array(array(&$this, $method), $rule);
			} 
		}

		if(empty($data["user_created"])) unset($data["user_created"]) ;
		
		// Se c'e' la chiave primaria vuota la toglie
		if(isset($data[$this->primaryKey]) && empty($data[$this->primaryKey]))
			unset($data[$this->primaryKey]) ;
			

			return true ;
	}

	/**
	 * Search objects not using content tree.
	 * (see: Tre::getChildren(), Tree::getDiscendents()  for searches using content tree).
	 * 
	 * If userid != null, only objects with read permissione for user, if ' ' - use guest/anonymous user,
	 * if userid = null -> no permission check.
	 * Filter: object types, search text query.
	 *
	 * @param string $userid	user: null (default) => no permission check. ' ' => guest/anonymous user,
	 * @param string $status	object status
	 * @param array  $filter	Filter: object types, search text query, eg. array(21, 22, "search" => "text to search").
	 * 							Default: all object types
	 * @param string $order		field to order result (id, status, modified..)
	 * @param boolean $dir		true (default), ascending, otherwiese descending.
	 * @param integer $page		Page number (for pagination)
	 * @param integer $dim		Page dim (for pagination)
	 * @param array $excludeIds Array of id's to exclude
	 */	
	function findObjs($userid = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = 100000, $excludeIds=array()) {

		$searchFields = ""; 
		$fields  = " `BEObject`.* " ;
		
		// setta le condizioni di ricerca
		$conditions = array() ;
		$this->_getCondition_filterType($conditions, $filter) ;
		$this->_getCondition_userid($conditions, $userid ) ;
		$this->_getCondition_status($conditions, $status) ;
		$this->_getCondition_current($conditions, true) ;
		$this->getCondition_excludeIds($conditions, $excludeIds) ;
		
		// standard sql where for BEObject
		$db 		 =& ConnectionManager::getDataSource($this->useDbConfig);
		$sqlClausole = $db->conditions($conditions, true, true) ;
		
		$from = " objects as `BEObject` " ;
		
		$fromSearchText = "";
		$ordClausole  = "" ;
		$groupClausole  = "" ;
		$searchText = false;
		$searchClausole = ""; 
		// text search conditions?
		if(is_array($filter) && isset($filter['search'])) {
			$s = $filter['search'];
			$searchFields = "DISTINCT `SearchText`.`object_id` AS `oid`, SUM( MATCH (`SearchText`.`content`) AGAINST ('$s') * `SearchText`.`relevance` ) AS `points`, ";
			$searchText = true;
			$fromSearchText = ", search_texts as `SearchText` ";
			$searchClausole = " AND `SearchText`.`object_id` = `BEObject`.`id` AND MATCH (`SearchText`.`content`) AGAINST ('$s')";
			$groupClausole  = "  GROUP BY `SearchText`.`object_id`";
			$ordClausole = " ORDER BY points DESC ";
		}
		
		if(is_string($order) && strlen($order)) {
			$ordItem = "{$order} " . ((!$dir)? " DESC " : "");
			if($searchText) {
				$ordClausole .= ", ".$ordItem;
			} else {
				$ordClausole = " ORDER BY {$order} " . ((!$dir)? " DESC " : "") ;
			}
		}
		
		$limit 	= $this->getLimitClausole($page, $dim) ;
		$query = "SELECT {$searchFields}{$fields} FROM {$from} {$fromSearchText} {$sqlClausole} {$searchClausole} {$groupClausole} {$ordClausole} LIMIT {$limit}";
		$tmp  	= $this->query($query) ;
		
		// build items and toolbar
		$recordset = array(
			"items"		=> array(),
			"toolbar"	=> $this->toolbar($page, $dim, $fromSearchText.$sqlClausole.$searchClausole)
		) ;
		for ($i =0; $i < count($tmp); $i++) {
			$recordset['items'][] = $this->am($tmp[$i]);
		}

		return $recordset ;
	}
	
	function findCount($sqlConditions = null, $recursive = null) {
		$from = " objects as `BEObject` " ;
		$query = "SELECT COUNT(DISTINCT `BEObject`.`id`) AS count FROM {$from}";
		if(is_array($sqlConditions)) {
			$where = " WHERE ";
			$first = true;
			foreach ($sqlConditions as $k => $v) {
				if(!$first) {
					$where .= " AND ";
				}
				$where .= " $k = $v";
				$first = false;
			}
			$query .= $where;
			
		} else if(!empty($sqlConditions)) {
			$query .= $sqlConditions;
		}
		list($data)  = $this->query($query) ;

		if (isset($data[0]['count'])) {
			return $data[0]['count'];
		} elseif (isset($data[$this->name]['count'])) {
			return $data[$this->name]['count'];
		}

		return false;
	}
	
	public function findObjectTypeId($id) {
		$object_type_id = $this->field("object_type_id", array("BEObject.id" => $id));
		return $object_type_id;
	}
	
	/**
	 * Model name/type from id
	 *
	 * @param unknown_type $id
	 */
	public function getType($id) {
		$type_id = $this->findObjectTypeId($id);
		if($type_id === false) {
			return false;
		}		
		return Configure::getInstance()->objectTypeModels[$type_id] ;
	}
	
	/**
	* update title e description only.
	**/
	public function updateTitleDescription($id, $title, $description) {
		if(@empty($id) || @empty($title)) return false ;
		
		$db 		= &ConnectionManager::getDataSource($this->useDbConfig);
		
		$db->query("UPDATE objects  SET title =  '".addslashes($title)."', description = '".addslashes($description)."' WHERE id = {$id} " ) ;
		
		return true ;
	}	
	
	////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////
	
	/**
	 * Setta i valori di default per i diversi campi
	 */
	private function _getDefaultNickname($value) {
		
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
		if(@empty($nickname)) 
			return $nickname ;

		$nickOk = false;
		$countNick = 1;
		$conf = Configure::getInstance() ;
		$reservedWords = array_merge ( $conf->defaultReservedWords, $conf->cfgReservedWords );

		
		while (!$nickOk) {
			
			$cond = "WHERE nickname = '{$nickname}'";
			if ($this->id) {
				$cond .= " AND id<>".$this->id;
			}
			$numNickDb = $this->findCount($cond);
			
			// check nickname in db and in reservedWords
			if ($numNickDb == 0 && !in_array($nickname, $reservedWords)) {
				$nickOk = true;
			} else {
				$nickname = $nickname_base . "_" . $countNick++;
			}
			
		}
		
		return $nickname ;
	}
	
	private function _getDefaultLang($value = null) {
		if(isset($value)) return $value ;

		$conf = Configure::getInstance() ;
		return ((isset($conf->defaultLang))?$conf->defaultLang:'') ;
	}
	
	private function _getDefaultPermission($value, $object_type_id) {
		if(isset($value) && is_array($value)) return $value ;
		
		$conf = Configure::getInstance() ;
		$permissions = &$conf->permissions ;
		
		// Aggiunge i permessi di default solo se sta creando un nuovo oggetto
		if(isset($this->data[$this->name][$this->primaryKey])) return null ;
		
		// Seleziona i permessi in base al tipo di oggetti
		if(isset($permissions[$object_type_id])) 	return $permissions[$object_type_id] ;
		else if (isset($permissions['all']))		return $permissions['all'] ;
		
		return null ;
	}
	
	private function _getDefaultIP($value = null) {
		if(isset($value)) 
			return $value ;
		$IP = $_SERVER['REMOTE_ADDR'] ;
	
		return $IP ;
	}
	
	private function _getIDCurrentUser($get = true) {
		if(!$get) return null ;
		
		if(is_string($get)) return $get ;
		
		// Preleva l'utente dai dati di sessione
		$conf = Configure::getInstance() ;		
		$session = @(new CakeSession()) ;
		
		$user = $session->read($conf->session["sessionUserKey"]) ; 
		if(!isset($user["id"])) return null ;
		
		return $user["id"] ;
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
	
	
	private function _getCondition_filterType(&$conditions, $filter = false) {
		if(!$filter) 
			return ;
		// exclude search query from object_type_id list
		if(is_array($filter)) {
			$types = array();
			foreach ($filter as $k => $v) {
				if($k !== "search" && $k !== "lang")
					$types[] = $v;
				elseif ($k === "lang")
					$conditions["`BEObject`.lang"] = $v;
			}
			$conditions['object_type_id'] = $types;
		} else {
			$conditions['object_type_id'] = $filter;
		}
	}
	
	private function _getCondition_userid(&$conditions, $userid = null) {
		if(!isset($userid)) return ;

		$conditions[] 	= " prmsUserByID ('{$userid}', `BEObject`.id, ".BEDITA_PERMS_READ.") > 0 " ;
	}

	private function _getCondition_status(&$conditions, $status = null) {
		if(!isset($status)) 
			return ;
		$conditions[] = array('status' => $status) ;
	}

	private function _getCondition_current(&$conditions, $current = true) {
		if(!$current) return ;
		$conditions[] = array("current" => 1);
	}
	
	private function getCondition_excludeIds(&$conditions, $excludeIds) {
		if(empty($excludeIds)) return ;
		$conditions["NOT"] = array(array("id" => $excludeIds));
	}
	
	/**
	 * Transform permission array ==> cake-style associative array
	 *
	 * @param array $arr	{0..N} item:
	 * 						0:ugid, 1:switch, 2:flag 
	 * @param array $perms	dove torna l'array associativo:
	 * 						ugid => ; switch => ; flag => 
	 */
	private function _array2perms(&$arr, &$perms) {
		$perms = array() ;
		if(!count($arr))  return ;

		foreach ($arr as $item) {
			$perms[] = array(
					'name'		=> $item[0],
					'switch'	=> $item[1],
					'flag'		=> (isset($item[2]))?$item[2]:null,
			) ;
		}
	}
	
	function getIdFromNickname($nickname) {
		$sql = "SELECT objects.id FROM objects WHERE nickname = '{$nickname}' LIMIT 1" ;
		$tmp  	= $this->query($sql) ;
		return ((isset($tmp[0]['objects']['id'])) ? $tmp[0]['objects']['id'] : null) ;
	}

	function getNicknameFromId($id) {
		$sql = "SELECT objects.nickname FROM objects WHERE id = '{$id}' LIMIT 1" ;
		$tmp  	= $this->query($sql) ;
		return ((isset($tmp[0]['objects']['nickname'])) ? $tmp[0]['objects']['nickname'] : null) ;
	}
}
?>

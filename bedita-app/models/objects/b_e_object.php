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
		'Index' =>
			array(
				'className'		=> 'Index',
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

	/**
	 * Formatta i dati specifici dopo la ricerca
	 */	
	function afterFind($result) {
		
		if(!isset($result['CustomProperties'])) return $result ;
		
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
		
		return $result ;
	}

	/**
	 * Formatta i dati specifici prima di salvare
	 */
	function beforeSave() {
		// Formatta le custom properties e i campi da indicizzare
		$labels = array('CustomProperties', 'Index');
		foreach ($labels as $label) {
			if(!isset($this->data[$this->name][$label])) continue ;
			
			if(is_array($this->data[$this->name][$label]) && count($this->data[$this->name][$label])) {
				$tmps 	= array() ;

				foreach($this->data[$this->name][$label]  as $k => $v) {
					$this->_value2array($k, $v, $arr) ;
					$tmps[] = $arr ;

				}
				$this->data[$this->name][$label] = $tmps ;
			}
		}
		
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
		
		if(!$permissions) return true ;
		
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
			'ip_created' 		=> array('_getDefaultIP'),
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
	 * Esegue ricerche complesse sugli oggetti indipendentemente da dove sono collocati.
	 * (vedere: tree->getChildren(), tree->getChildren() ).
	 * Se l'userid e' presente, preleva solo gli oggetti di cui ha i permessi, se '' � un utente anonimo,
	 * altrimenti li prende tutti.
	 * Si possono selezionare i tipi di oggetti da prelevare.
	 *
	 * @param string $userid	l'utente che accede. Se null: non controlla i permessi. Se '': utente guest.
	 * 							Default: non verifica i permessi.
	 * @param string $status	Prende oggetti solo con lo status passato
	 * @param array $filter		definisce i tipi gli oggetti da prelevare. Es.:
	 * 							1, 3,  22 ... aree, sezioni, documenti.
	 * 							Default: tutti.
	 * @param string $order		Campo testuale su cui ordinare il risultato
	 * @param boolean $dir		TRUE, ordine ascenedente, altrimenti discendente. Default: TRUE
	 * @param integer $page		Numero di pagina da selezionare
	 * @param integer $dim		Dimensione della pagina
	 */	
	function findObjs($userid = null, $status = null, $filter = false, $order = null, $dir  = true, $page = 1, $dim = 100000, $excludeIds=array()) {
		if(!isset($userid)) {
			$fields 		= " *, prmsUserByID ('{$userid}', id, 15) as perms " ;
		} else {
			$fields  = " * " ;
		}
		
		// setta le condizioni di ricerca
		$conditions = array() ;
		$this->_getCondition_filterType($conditions, $filter) ;
		$this->_getCondition_userid($conditions, $userid ) ;
		$this->_getCondition_status($conditions, $status) ;
		$this->_getCondition_current($conditions, true) ;
		$this->getCondition_excludeIds($conditions, $excludeIds) ;
		
		// Costruisce i criteri di ricerca
		$db 		 =& ConnectionManager::getDataSource($this->useDbConfig);
		$sqlClausole = $db->conditions($conditions, true, true) ;
		
		// costruisce il join dalle tabelle
		$from = " objects " ;
		
		// Clausola ordinamento
		$ordClausole  = "" ;
		if(is_string($order) && strlen($order)) {
			$ordClausole = " ORDER BY {$order} " . ((!$dir)? " DESC " : "") ;
		}
		
		// Esegue la ricerca
		$limit 	= $this->_getLimitClausole($page, $dim) ;
		$tmp  	= $this->execute("SELECT {$fields} FROM {$from} {$sqlClausole} {$ordClausole} LIMIT {$limit}") ;

		// Torna il risultato
		$recordset = array(
			"items"		=> array(),
			"toolbar"	=> $this->toolbar($page, $dim, $sqlClausole)
		) ;
		for ($i =0; $i < count($tmp); $i++) {
			$recordset['items'][] = $this->am($tmp[$i]);
		}

		return $recordset ;
	}
	
	function findCount($conditions = null, $recursive = null) {
		$from = " objects " ;
		list($data)  = $this->execute("SELECT COUNT(*) AS count FROM {$from} {$conditions}") ;

		if (isset($data[0]['count'])) {
			return $data[0]['count'];
		} elseif (isset($data[$this->name]['count'])) {
			return $data[$this->name]['count'];
		}

		return false;
	}
	
	public function findObjectTypeId($id) {
		$object = $this->find("first", array(
									"restrict" 	=> array(),
									"fields" 	=> "object_type_id",
									"conditions" => "BEObject.id=".$id
								)
						);
		if (!empty($object)) {
			return $object["BEObject"]["object_type_id"];
		} else {
			return false;
		}
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
		$nickname = $nickname_base = strtolower(preg_replace("/[^0-9A-Za-z\-_.]/i", "", $value)) ;
		if(@empty($nickname)) 
			return $nickname ;
		
		// Verifica l'assenza del nickname selezionato
		$tentativi 	= 100 ;
		$unico 		= false ;
		for($i=0 ; $i < $tentativi && !$unico; $i++ ) {
			$count = $this->findCount("WHERE nickname = '{$nickname}'") ;
			
			// Se crea un nuovo obj deve essere assente 
			if (!$count || ($count == 1 && $this->id)) {
				$unico = true ;
				continue ;
			}
			
			// Prova con un nuovo nickname
			srand(time());
			$num = rand(100, 9999);
			$nickname = "{$nickname_base}_{$num}" ;
		}
		
		// Se non ha trovato un nickname libeo esce
		if(!$unico) return "" ;
		
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
	
	
	/**
	 * Creano le cluasole di ricerca
	 */
	private function _getLimitClausole($page = 1, $dim = 100000) {
		// Esegue la ricerca
		if($page > 1) $offset = ($page -1) * $dim ;
		else $offset = null ;
		$limit = isset($offset)? "$offset, $dim" : "$dim";

		return $limit ;
	}
	
	private function _getCondition_filterType(&$conditions, $filter = false) {
		if(!$filter) return ;
		$conditions['object_type_id'] = $filter ;
	}

	private function _getCondition_userid(&$conditions, $userid = null) {
		if(!isset($userid)) return ;

		$conditions[] 	= " prmsUserByID ('{$userid}', id, ".BEDITA_PERMS_READ.") > 0 " ;
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
	 * Trasforma un array di permessi da aggiungere in un array associativo x Cake
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
		$tmp  	= $this->execute($sql) ;
		return ((isset($tmp[0]['BEObject']['id'])) ? $tmp[0]['BEObject']['id'] : null) ;
	}

	function getNicknameFromId($id) {
		$sql = "SELECT objects.nickname FROM objects WHERE id = '{$id}' LIMIT 1" ;
		$tmp  	= $this->execute($sql) ;
		return ((isset($tmp[0]['BEObject']['nickname'])) ? $tmp[0]['BEObject']['nickname'] : null) ;
	}
}
?>

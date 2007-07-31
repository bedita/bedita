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
	var $name = 'Object';
	
	var $validate = array(
		'title' 			=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true, 'message' => 'Campo vuoto')),
		'object_type_id' 	=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
		'nickname' 			=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
		'lang' 				=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
		'IP_created' 		=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),

	) ;

	var $belongsTo = array(
		'ObjectType' =>
			array(
				'className'		=> 'ObjectType',
				'foreignKey'	=> 'object_type_id',
				'conditions'	=> ''
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
		
	}
	
	/**
	 * Definisce i valori di default.
	 */		
	function beforeValidate() {
		if(isset($this->data[$this->name])) $data = &$this->data[$this->name] ;
		else $data = &$this->data ;
		
	 	$default = array(
			'nickname' 			=> array('_getDefaultNickname', 	(isset($data['nickname']))?$data['nickname']:((isset($data['title']))?$data['title']:'')),
			'lang' 				=> array('_getDefaultLang', 		(isset($data['lang']))?$data['lang']:null),
			'IP_created' 		=> array('_getDefaultIP'),
			'Permission' 		=> array('_getDefaultPermission', 	(isset($data['Permission']))?$data['Permission']:null, (isset($data['object_type_id']))?$data['object_type_id']:0),
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

		// Se c'e' la chiave primaria vuota la toglie
		if(isset($data[$this->primaryKey]) && empty($data[$this->primaryKey]))
			unset($data[$this->primaryKey]) ;
			

			return true ;
	}
	
	/**
	 * Setta i valori di default per i diversi campi
	 */
	private function _getDefaultNickname($value) {
		return preg_replace("/[^0-9A-Za-z\-_.]/i", "", $value) ;
	}
	
	private function _getDefaultLang($value = null) {
		if(isset($value)) return $value ;

		$conf = Configure::getInstance() ;
		return ((isset($conf->lang))?$conf->lang:'') ;
	}
	
	private function _getDefaultPermission($value, $object_type_id) {
		if(isset($value) && is_array($value)) return $value ;
		
		$conf = Configure::getInstance() ;
		$permissions = &$conf->permissions ;
		
		// Seleziona i permessi in base al tipo di oggetti
		if(isset($permissions[$object_type_id])) 	return $permissions[$object_type_id] ;
		else if (isset($permissions['all']))		return $permissions['all'] ;
		
		return null ;
	}
	
	private function _getDefaultIP($value = null) {
		$IP = $_SERVER['REMOTE_ADDR'] ;
	
		return $IP ;
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
	
	
	
}
?>

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
class Section extends BEAppCollectionModel
{
	var $name 		= 'Section';
	var $useTable 	= 'view_sections' ;
	var $recursive 	= 2 ;
	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'ForeignDependenceSave' => array('BEObject', 'Collection'),
			'DeleteDependentObject'	=> array('section', 'community'),
			'DeleteObject' 			=> 'objects',
	); 

	/**
	 * Contenitore dove inserire la community
	 *
	 * @var unknown_type
	 */
	var $validate = array(
		'parent_id'	=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
	) ;
	
	var $hasOne = array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => 'BEObject.object_type_id = 3',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Collection' =>
				array(
					'className'		=> 'Collection',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
	) ;			

	function __construct() {
		parent::__construct() ;
	}

	/**
 	* Sovrascrive completamente il save() l'oggetto non ha una tabella
 	* specifica ma una vista, non deve salvare
 	*/
	function save($data = null, $validate = true, $fieldList = array()) {
		$conf = Configure::getInstance() ;		
		
		if(isset($data['BEObject']) && !isset($data['BEObject']['object_type_id'])) {
			$data['BEObject']['object_type_id'] = $conf->objectTypes[strtolower($this->name)] ;
		} else if(!isset($data['object_type_id'])) {
			$data['object_type_id'] = $conf->objectTypes[strtolower($this->name)] ;
		}

		$this->set($data);

		if ($validate && !$this->validates()) {
			return false;
		}

		if (!empty($this->behaviors)) {
			$behaviors = array_keys($this->behaviors);
			$ct = count($behaviors);
			for ($i = 0; $i < $ct; $i++) {
				if ($this->behaviors[$behaviors[$i]]->beforeSave($this) === false) {
					return false;
				}
			}
		}
		
		if(empty($this->id)) $created = true ;
		else $created = false ; 
		
		$this->setInsertID($this->BEObject->id);
		$this->id = $this->BEObject->id ;
		
		if (!empty($this->behaviors)) {
			$behaviors = array_keys($this->behaviors);
			$ct = count($behaviors);
			for ($i = 0; $i < $ct; $i++) {
				if($this->behaviors[$behaviors[$i]]->afterSave($this, null) === false)
					return false;
			}
		}

		if($this->afterSave($created)===false)
			return false;
		$this->data = false;
		$this->_clearCache();
		$this->validationErrors = array();
		
		return true ;
	}

	function afterSave($created) {
		if (!$created) return true;
		if(!class_exists('Tree')) loadModel('Tree');
		$tree 	=& new Tree();
		if($tree->appendChild($this->id, $this->data[$this->name]['parent_id'])===false)
			return false;
		return true;
	}

	//////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	/**
	 * Formatta i dati per la creazione di un clone, ogni tipo
	 * di oggetto esegue operazioni specifiche richiamando.
	 * Trova l'id del ramo in cui e' inserita
	 *
	 * @param array $data		Dati da formattare
	 * @param object $source	Oggetto sorgente
	 */
	protected function _formatDataForClone(&$data, $source = null) {
		if(!class_exists('Tree')) loadModel('Tree');

		$tree =& new Tree();
		
		$data['parent_id'] = $tree->getParent($data['id'])  ;		
		parent::_formatDataForClone($data);
	}	
	
	/**
	 * Esegue ricorsivamente solo la clonazione dei figli di tipo: Section e Community,
	 * gli altri reinscerisce un link
	 *
	 */
	protected function insertChildrenClone() {
		$conf  	= Configure::getInstance() ;
		$tree 	=& new Tree();
		
		// Preleva l'elenco dei figli
		$children = $tree->getChildren($this->oldID , null, null, false, 1, 10000000) ;
		
		// crea le nuove associazioni
		for ($i=0; $i < count($children["items"]) ; $i++) {
			$item = $children["items"][$i] ;
			
			switch($item['object_type_id']) {
				case $conf->objectTypes['section']:
				case $conf->objectTypes['community']: {
					$className	= $conf->objectTypeModels[$item['object_type_id']] ;
					
					$tmp = new $className() ;
					$tmp->id = $item['id'] ;
					
					$clone = clone $tmp ; 
					$tree->move($this->id, $this->oldID , $clone->id) ;
				}  break ;
				default: {
					$tree->appendChild($item['id'], $this->id) ;
				}
			}
		}
	}

}
?>

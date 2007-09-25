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
 * 		
 * 						
*/
class Comment extends BEAppObjectModel
{
	var $name 		= 'Comment';
	var $recursive 	= 2 ;
	
	/**
	 * Oggetto da commentare
	 *
	 * @var unknown_type
	 */
	var $validate = array(
		'object_id'	=> array(array('rule' => VALID_NOT_EMPTY, 'required' => true)),
	) ;

	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'CreateIndexFields'		=> array(),
			'ForeignDependenceSave' => array('Object', 'ContentBase'),
			'DeleteObject' 			=> 'objects',
	); 

	var $hasOne = array(
			'Object' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'ContentBase' =>
				array(
					'className'		=> 'ContentBase',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
		) ;			

	function __construct() {
		parent::__construct() ;
	}

	/**
	 * Associa il commento creato/modificato all'oggetto commentato
	 */
	function afterSave($created) {
		if (!$created) return ;
		
		$this->query(
			"INSERT INTO content_bases_objects (object_id, id, switch) 
			VALUES 
			({$this->id}, {$this->data[$this->name]['object_id']}, 'COMMENTS')"
		) ;
	}
	
	function getParentID($id = null) {
		if (!isset($id)) $id = $this->id ;
		
		if(!($ret = $this->execute("SELECT id FROM content_bases_objects WHERE object_id = {$id}"))) return false ;
		
		return ($ret[0]['content_bases_objects']['id']) ;
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
		
		$data['object_id'] = $this->getParentID($data['id'])  ;		
		parent::_formatDataForClone($data);
	}	

}
?>

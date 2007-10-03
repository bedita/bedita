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
class Newsletter extends BEAppCollectionModel 
{
	var $name 		= 'Newsletter';
	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'ForeignDependenceSave' => array('Object', 'Collection'),
			'CreateIndexFields'		=> array(),
			'DeleteDependentObject'	=> array('section', 'community'),
			'DeleteObject' 			=> 'objects',
	); 

	var $recursive = 2 ;

	var $hasOne = array(
			'Object' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
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
	 * Inserisce l'area nell'albero
	 */
	function afterSave($created) {
		if (!$created) return ;
		
		if(!class_exists('Tree')) loadModel('Tree');
		$tree 	=& new Tree();
		$tree->appendChild($this->id, null) ;		
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

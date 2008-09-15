<?php
/**
 * Addressbook card - vCard format
 * 
 * @copyright		Copyright (c) 2008 Channelweb, Chialab
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @license
 * @author 		Stefano Rosanelli ste@channelweb.it
 * 		
*/
class Card extends BEAppObjectModel {

	
	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave'		=> array("title" => 8 , "description" => 4, "company_name" => 3, "city" => 4),
			'ForeignDependenceSave' => array('BEObject'),
			'DeleteObject' 			=> 'objects',
	); 
	
	var $hasOne= array(
			'BEObject' =>
			array(
				'className'		=> 'BEObject',
				'conditions'   => '',
				'foreignKey'	=> 'id',
				'dependent'		=> true
			)
		);
	
	function beforeValidate() {
		
		$this->checkDate('birthdate');
		$this->checkDate('deathdate');

		return true;
	}
}
?>

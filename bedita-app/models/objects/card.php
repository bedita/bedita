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
		$this->data['Card']['birthdate'] = $this->getDefaultDateFormat($this->data['Card']['birthdate']);
		$this->data['Card']['deathdate'] = $this->getDefaultDateFormat($this->data['Card']['deathdate']);
		return true;
	}
}
?>

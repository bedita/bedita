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

	public $searchFields = array("title" => 8 , "description" => 4, 
		"company_name" => 3, "city" => 4);
	
	function beforeValidate() {
		
		$this->checkDate('birthdate');
		$this->checkDate('deathdate');

		return true;
	}
}
?>

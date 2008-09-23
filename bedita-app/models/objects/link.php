<?php
/**
 * Web link
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
 * @author 		giangi giangi@qwerg.com,	ste ste@channelweb.it
 * 		
*/
class Link extends BEAppObjectModel {
	
	public $searchFields = array();
	
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"RelatedObject")),

       			"default" => array("BEObject" => array("ObjectType", "RelatedObject" )),

				"minimum" => array("BEObject" => array("ObjectType"))		
		);
	
}
?>

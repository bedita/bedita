<?php
/**
 * @copyright		Copyright (c) 2008, Channelweb - Chialab
 * @link
 * @package
 * @subpackage
 * @since
 * @version
 * @modifiedby
 * @lastmodified
 * @license
 * @author 		Stefano Rosanelli ste@channelweb.it
*/

class Gallery extends BeditaContentModel
{
		var $useTable = 'contents';  

       protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"CustomProperties",
															"LangText",
															"RelatedObject",
															"Category")),

       			"default" => array("BEObject" => array("CustomProperties", 
									"LangText", "ObjectType", 
									"Category", "RelatedObject" )),

				"minimum" => array("BEObject" => array("ObjectType"))		
		);
       
}
?>

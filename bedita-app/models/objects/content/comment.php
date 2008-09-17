<?php
/**
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
 * @author 		giangi giangi@qwerg.com, ste ste@channelweb.it
 * 		
 * 						
*/
class Comment extends BeditaContentModel 
{
	
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", "RelatedObject"), 
								"Content"),
				"default" =>  array("BEObject" => array("ObjectType", "RelatedObject"), 
								"Content"),
				"minimum" => array("BEObject" => array("ObjectType"))
	);
	
	var $validate = array(
			'author' => array(
				'rule' 			=> 'alphaNumeric',
				'required' 		=> true				
	   		),
	   		'email' => array(
	   			'rule' 			=> 'email',
				'required' 		=> true,
	   			'message' 		=> 'email not valid'
	   		),
	   		'url' => array (
	   			'rule' 		 	=> 'url',
	   			'required' 		=> false,
	   			'allowEmpty'	=> true,
	   			'message' 		=> 'URL not valid'
	   		)
	   );
}
?>

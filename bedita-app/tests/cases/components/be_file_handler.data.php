<?php 
/**
 * 
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5

 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @author giangi@qwerg.com
 * 
 */
class BeFileHandlerData extends Object {
	var $data =  array(
		'minimo'	=> array(
			'title' 		=> 'test_target.jpg', 
			'name'			=> 'test_target.jpg',
			'mime_type'			=> 'image/jpeg',
			'nameSource'	=> 'test.jpg'
			),
		'minimoURL'	=> array(
			'title' 		=> 'test_target.jpg', 
			)
		) ;
	
	function &getData() { return $this->data ;  }

}

?> 
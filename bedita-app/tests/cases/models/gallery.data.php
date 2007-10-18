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
class GalleryData extends Object {
	var $data =  array('gallery'	=>
		array(
			'object_type_id' 	=> 29,
			'status' 			=> 'on',
			'title' 			=> 'Gallery'
		)
	) ;

	function &getData() { return $this->data ;  }
}

?>
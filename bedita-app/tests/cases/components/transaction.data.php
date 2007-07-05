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
class AreaData extends Object {
	var $data =  array(
		'minimo'	=> array('title' 			=> 'Titolo di test'),
		
		'makeFileFromData'	=> array(
				'title' 	=> 'Titolo test File', 
				'name'		=> 'txtFileTest.txt',
				'type'		=> 'plain/txt',
				'data'		=> "Questo file e' una prova"
		),
		
		'makeFileFromFile'		=> array(
				'title' 		=> 'Titolo test File', 
				'name'			=> 'test_target.jpg',
				'type'			=> 'image/jpeg',
				'nameSource'	=> 'test.jpg'
		),

		) ;
	
	function &getData() { return $this->data ;  }

}

?> 
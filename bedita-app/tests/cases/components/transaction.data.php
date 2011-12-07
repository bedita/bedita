<?php 
/**
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

class TransactionTestData extends BeditaTestData {
	var $data =  array(
		'minimo'	=> array('title' 			=> 'Test title'),
		
		'makeFileFromData'	=> array(
				'title' 	=> 'Test title File', 
				'name'		=> 'txtFileTest.txt',
				'mime_type'		=> 'plain/txt',
				'data'		=> "Questo file e' una prova"
		),
		
		'makeFileFromFile'		=> array(
				'title' 		=> 'Test title File', 
				'name'			=> 'test_target.jpg',
				'mime_type'			=> 'image/jpeg',
				'nameSource'	=> 'test.jpg'
		),

		) ;
}

?>
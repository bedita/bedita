<?php 
/**
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

class TransactionTestData extends BeditaTestData {
	var $data =  array(
		'minimo'	=> array('title' 			=> 'Titolo di test'),
		
		'makeFileFromData'	=> array(
				'title' 	=> 'Titolo test File', 
				'name'		=> 'txtFileTest.txt',
				'mime_type'		=> 'plain/txt',
				'data'		=> "Questo file e' una prova"
		),
		
		'makeFileFromFile'		=> array(
				'title' 		=> 'Titolo test File', 
				'name'			=> 'test_target.jpg',
				'mime_type'			=> 'image/jpeg',
				'nameSource'	=> 'test.jpg'
		),

		) ;
}

?> 
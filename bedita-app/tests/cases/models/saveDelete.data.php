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
		
		'domanda'	=> array('title' 			=> 'Titolo Domanda di test'),
		'risposta1'	=> array('title' 			=> 'Titolo Risposta 1 di test'),
		'risposta2'	=> array('title' 			=> 'Titolo Risposta 2 di test'),
		'risposta3'	=> array('title' 			=> 'Titolo Risposta 3 di test'),

		'emptyStream'	=> array('title' 			=> 'Titolo empty Stream'),

		'file'			=> array(
				'title' 	=> 'Titolo empty Stream', 
				'path'		=> 'path_file',
				'name'		=> 'file_name',
				'type'		=> 'plain/txt',
				'size'		=> 123456
		),
		
		'docWithLinks'		=> array(
				'title' 	=> 'Titolo Doc con links', 
				'links'		=> array(
					array('url' => 'www.qwerg.com', 'switch' => 'url'),
					array('url' => 'www.google.com', 'switch' => 'url'),
				)
		),
		
		'eventWithDate'		=> array(
				'title' 	=> 'Titolo Event widh date', 				
				'calendars'	=> array(
					array('start' => '2007-06-23', 'end'=> '2007-07-12'),
					array('start' => '2007-06-23', 'end'=> '2007-06-23'),
				)

		),
		
		'biblio'	=> array('title' 			=> 'Bibliografia di test'),
		'item1'		=> array('title' 			=> 'Item biblio 1'),
		'book2'		=> array('title' 			=> 'Book biblio 2'),
		'item3'		=> array('title' 			=> 'Item biblio 3'),

		'user'		=> array(
				'title' 	=> 'Titolo Object user', 
				'user_id'	=> 'bedita',
		),
	) ;
	
	function &getData() { return $this->data ;  }

}

?> 
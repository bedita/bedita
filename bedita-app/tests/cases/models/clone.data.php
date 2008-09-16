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
		'insert' => array(
			// AREA
			'area'	=> array(
				'minimo'	=> array(
					'title' 			=> 'Titolo di test',
					'CustomProperties'	=> array(
								'valTestInt'	=> 1,
								'valTestFloat'	=> 1.1,
								'valTestBool'	=> true,
								'valTestArray'	=> array(1, 2, 3),
					),
					'LangText'	=> array(
							array('lang' => 'en', 'name' => 'title', 'text' => 'Title of test'),
							array('lang' => 'fr', 'name' => 'title', 'text' => 'France Title'),
					),
					'calendars'	=> array(
						array('start' => '2007-06-23', 'end'=> '2007-07-12'),
						array('start' => '2007-06-23', 'end'=> '2007-06-23'),
					),
					'links'		=> array(
						array('url' => 'www.qwerg.com', 'switch' => 'url'),
						array('url' => 'www.google.com', 'switch' => 'url'),
					)	
				),
			),
			'idDocPresente'	=> 5,
			'file'			=> array(
				'title' 	=> 'Titolo empty Stream', 
				'path'		=> 'path_file',
				'name'		=> 'file_name',
				'mime_type'		=> 'plain/txt',
				'size'		=> 123456
			),
			
			'biblio'	=> array('title' 			=> 'Bibliografia di test'),
			'item1'		=> array('title' 			=> 'Item biblio 1'),
			'book2'		=> array('title' 			=> 'Book biblio 2'),
			'item3'		=> array('title' 			=> 'Item biblio 3'),
			
			'FAQ'		=> array('title' 			=> 'FAQ di test'),
			'domanda1'	=> array('title' 			=> 'Domanda 1'),
			'domanda2'	=> array('title' 			=> 'Domanda 2'),
			
			'community'	=> array(
				'title' 	=> 'Comunity Test', 
				'parent_id'	=> 2,
				
				'user_id'	=> array('bedita','giangi','alberto','torto')
			),

			'questionario'	=> array('title' 			=> 'Titolo questionario'),
			'domanda'		=> array('title' 			=> 'Titolo Domanda di test'),
			'risposta1'		=> array('title' 			=> 'Titolo Risposta 1 di test'),
			'risposta2'		=> array('title' 			=> 'Titolo Risposta 2 di test'),
			'risposta3'		=> array('title' 			=> 'Titolo Risposta 3 di test'),
			
			'documento'		=> array('title' 			=> 'Titolo Documento di test'),
			'commento'		=> array('title' 			=> 'Titolo Commento di test'),
		),

		'insertTree'		=> array(

			array('Area'	=> array(
								'title' 	=> 'Area 4',
								'perms' 	=> 'Perms2',
								'children'	=> array(
									array('Section'	=> array('title' => 'Sezione 9' , 'children' => array(
										array('Section'	=> array('title' => 'Sezione 10' , 'children' => array())),
									))),
									array('Section'	=> array('title' => 'Sezione 11' , 'children' => array())),
									array('Section'	=> array('title' => 'Sezione 12' , 'perms' => 'Perms3', 'children' => array(
										array('Section'	=> array('title' => 'Sezione 13' , 'children' => array())),
										array('Section'	=> array('title' => 'Sezione 14' , 'children' => array())),
									))),
									array('Section'	=> array('title' => 'Sezione 15' , 'perms' => 'Perms3', 'children' => array())),
									array('Section'	=> array('title' => 'Sezione 16' , 'children' => array())),
								),
				  )
			),
		), 			

	) ;
		
	function __construct() {
		/*
		$this->data['insert']['area']['minimo']['permissions'] = array(
				array('administrator', 	'group', (BEDITA_PERMS_CREATE | BEDITA_PERMS_DELETE|BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ)),
				array('guest', 			'group', BEDITA_PERMS_READ),
				array('alberto', 		'user',  ( BEDITA_PERMS_CREATE | BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ)),
				array('torto', 			'user',  ( BEDITA_PERMS_DELETE | BEDITA_PERMS_MODIFY|BEDITA_PERMS_READ))
			) ;
		*/
	}
	
	function &getData() { return $this->data ;  }

}

?> 
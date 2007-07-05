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
				),
				'customProperties'	=> array(
					'title' 			=> 'Titolo di test',
					'CustomProperties'	=> array(
								'valTestInt'	=> 1,
								'valTestFloat'	=> 1.1,
								'valTestBool'	=> true,
								'valTestArray'	=> array(1, 2, 3),
					),
				),
				'traduzioni'	=> array(
					'title' 	=> 'Titolo di test',
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
				),
			)
		)
	) ;
	
	function &getData() { return $this->data ;  }
/*
$data['insert']['area'] = array(
  	'id' 				=> 2,
    'object_type_id' 	=> 1,
    'status' 			=> on,
    'created' 			=> '2007-06-12 09:35:54',
    'modified' 			=> '2007-06-12 09:35:54',
    'title' 			=> 'Test site',
    'nickname' 			=> 'TestSite',
    'current' 			=> 1,
    'lang' 				=> 'it',
    'IP_created'	 	=> '192.168.0.1',
    'Permission' 		=> Array(),
    'CustomProperties' 	=> Array(),
    'LangText' 			=> Array(),
    'create_rules' 		=> null,
    'access_rules' 		=> null,
   );
*/
}

?> 
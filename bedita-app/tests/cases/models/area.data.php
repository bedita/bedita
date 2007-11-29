<?php 
/**
 *
 * @author giangi@qwerg.com ste@channelweb.it
 * 
 */

class AreaTestData extends BeditaTestData {
	var $data =  array(
		'insert' => array(
			// AREA
			'area'	=> array(
				'minimo'	=> array(
					'title' 			=> 'Titolo di test',
					'user_created' => 1
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
			),
			
			'sezione'	=> array(
				'minimo1'	=> array(
					'title' 			=> 'Titolo Sezione 1 di test',
				),
				'minimo2'	=> array(
					'title' 			=> 'Titolo Sezione 2 di test',
				),
				'minimo3'	=> array(
					'title' 			=> 'Titolo Sezione 3 di test',
				),
			)
			
		)
	) ;
}

?> 
<?php 
/**
 *
 * @author ste@channelweb.it
 * 
 */

class DocumentTestData extends BeditaTestData {
	var $data =  array(
		'insert' => array(
			'title' => "中国",
			'description' => "Inserimento contenuto UTF-8, funziona o no??",
	        'user_created' => 1,
			'object_type_id' => 22
		),
		'searches' => array("funziona", "inserimento"),
		'searchTree' => array(13, 14)
	);
}

?> 
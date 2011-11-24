<?php
/**
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * $Id$
 */
class BelibTestData extends BeditaTestData {
	var $data =  array(

//		"little-endian" => array("22/04/98", "22/4/1998", "22.4.1998", "22-4-98", "22 4 98"),
//		"middle-endian" => array("04/22/98", "4/22/1998", "4-22-98", "4 22 98" ,"4.22.1998"),
//		"sql"  	=> "1998-04-22",

		"little-endian" => array("22/04/08", "22/4/2008", "22.4.08", "22-4-8", "22 4 08"),
		"middle-endian" => array("04/22/08", "4/22/2008", "4-22-8", "4 22 08" ,"4.22.2008"),
		"sql"  	=> "2008-04-22",
	
		"ddmm" => array("22.04", "22/4", "22 4"),
		"mmdd" => array("04.22", "4/22", "4 22"),
		"m-d" => "04-22",
	
		"year" => array("98",   "1998", "08"),
		"yyyy" => array("1998", "1998", "2008"),
		
		"nickname" => array(
			"my-fantastic-nick-name" => "myFantasticNickName",
			"another_nick-name" => "anotherNickName",
			"another-1-nick-2-name" => "another1Nick2Name"
		)
	);
}
 
?>
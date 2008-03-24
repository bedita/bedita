<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     agent
 * Purpose:  riconoscimento dell' user_agent.
 			 Parametri:
			 var	nome della variabile dove inserire il risultato. Un'array con i seguenti valori:
			    [IE] => true/false
			    [IE5] => true/false
			    [IE6] => true/false
			    [IE7] => true/false
			    [MOZ] => true/false
			    [OP] => true/false
			    [OP5] => true/false
			    [OP6] => true/false
			    [OP7] => true/false
			    [OP8] => true/false
			    [OP9] => true/false
			    [NS] => true/false
			    [NS3] => true/false
			    [NS4] => true/false
			    [MAC] => true/false
			    [IE55] => true/false
			 
 * -------------------------------------------------------------
 */
function smarty_function_agent($params, &$smarty)
{
	global $_SERVER ;
	
    extract($params);
	
    if (empty($var)) {
        $smarty->trigger_error("assign: missing 'var' parameter");
        return;
    }
	
	$agent = array(
					"IE" => false,"IE5" => false,"IE6" => false,"IE7" => false,"MOZ" => false,"OP" => false,"OP5" => false,
					"OP6" => false,"NS" => false,"NS3" => false,"NS4" => false,"MAC" => false
	) ;
	
	$agent["IE"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE")) ? true : false ;
	if($agent["IE"]) {
		$agent["IE5"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE 5")) ? true : false ;
		$agent["IE55"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE 5.5")) ? true : false ;
		$agent["IE6"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE 6")) ? true : false ;
		$agent["IE7"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE 7")) ? true : false ;
	} 
	
	$agent["MOZ"] = (stristr($_SERVER["HTTP_USER_AGENT"], "Gecko")) ? true : false ;
	$agent["OP"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera")) ? true : false ;
	if($agent["OP"]) {
		$agent["OP5"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera 5") || stristr($_SERVER["HTTP_USER_AGENT"], "opera/5")) ? true : false ;
		$agent["OP6"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera 6") || stristr($_SERVER["HTTP_USER_AGENT"], "opera/6")) ? true : false ;
		$agent["OP7"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera 7") || stristr($_SERVER["HTTP_USER_AGENT"], "opera/7")) ? true : false ;
		$agent["OP8"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera 8") || stristr($_SERVER["HTTP_USER_AGENT"], "opera/8")) ? true : false ;
		$agent["OP9"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera 9") || stristr($_SERVER["HTTP_USER_AGENT"], "opera/9")) ? true : false ;
	} 
	
	if(!$agent["IE"] && !$agent["OP"] && !$agent["MOZ"]) {
		$agent["NS"] = true ;
		$agent["NS3"] = (stristr($_SERVER["HTTP_USER_AGENT"], "Mozilla/3")) ? true : false ;
		$agent["NS4"] = (stristr($_SERVER["HTTP_USER_AGENT"], "Mozilla/4")) ? true : false ;
	}
	
	$agent["MAC"] = (stristr($_SERVER["HTTP_USER_AGENT"], "mac")) ? true : false ;
	
	$smarty->assign($var, $agent);
}


/* vim: set expandtab: */

?>

<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     agent
 * Purpose:  get user_agent.
 			 Parameters:
			 var	variable that will contain the result. An array with following values:
			    [IE] => true/false
			    [IE5] => true/false
			    [IE6] => true/false
			    [IE7] => true/false
			    [IE8] => true/false
			    [IE9] => true/false
			    [MOZ] => true/false
			    [OP] => true/false
			    [OP5] => true/false
			    [OP6] => true/false
			    [OP7] => true/false
			    [OP8] => true/false
			    [OP9] => true/false
			    [OP10] => true/false
			    [NS] => true/false
			    [NS3] => true/false
			    [NS4] => true/false
			    [MAC] => true/false
			    [IE55] => true/false 
			    [SAFARI] => true/false
			    [iPAD] => true/false
			    [iPHONE] => true/false
			 
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
					"IE" => false,"IE5" => false,"IE6" => false,"IE7" => false,"IE8" => false,"MOZ" => false,"OP" => false,"OP5" => false,
					"OP6" => false,"NS" => false,"NS3" => false,"NS4" => false,"MAC" => false, "SAFARI" => false, "iPAD" => false, "iPHONE" => false
	) ;
	
	$agent["IE"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE")) ? true : false ;
	if($agent["IE"]) {
		$agent["IE5"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE 5")) ? true : false ;
		$agent["IE55"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE 5.5")) ? true : false ;
		$agent["IE6"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE 6")) ? true : false ;
		$agent["IE7"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE 7")) ? true : false ;
		$agent["IE8"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE 8")) ? true : false ;
		$agent["IE9"] = (stristr($_SERVER["HTTP_USER_AGENT"], "MSIE 9")) ? true : false ;
	} 
	
	$agent["MOZ"] = (stristr($_SERVER["HTTP_USER_AGENT"], "Gecko")) ? true : false ;
	if($agent["MOZ"]) {
		$agent["FF3"] = (stristr($_SERVER["HTTP_USER_AGENT"], "Firefox 3") || stristr($_SERVER["HTTP_USER_AGENT"], "Firefox/3")) ? true : false ;
		$agent["SAFARI"] = (stristr($_SERVER["HTTP_USER_AGENT"], "AppleWebKit")) ? true : false ;
	} 

	$agent["OP"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera")) ? true : false ;
	if($agent["OP"]) {
		$agent["OP5"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera 5") || stristr($_SERVER["HTTP_USER_AGENT"], "opera/5")) ? true : false ;
		$agent["OP6"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera 6") || stristr($_SERVER["HTTP_USER_AGENT"], "opera/6")) ? true : false ;
		$agent["OP7"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera 7") || stristr($_SERVER["HTTP_USER_AGENT"], "opera/7")) ? true : false ;
		$agent["OP8"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera 8") || stristr($_SERVER["HTTP_USER_AGENT"], "opera/8")) ? true : false ;
		$agent["OP9"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera 9") || stristr($_SERVER["HTTP_USER_AGENT"], "opera/9")) ? true : false ;
		$agent["OP10"] = (stristr($_SERVER["HTTP_USER_AGENT"], "opera 10") || stristr($_SERVER["HTTP_USER_AGENT"], "opera/10")) ? true : false ;

	} 
	
	if(!$agent["IE"] && !$agent["OP"] && !$agent["MOZ"]) {
		$agent["NS"] = true ;
		$agent["NS3"] = (stristr($_SERVER["HTTP_USER_AGENT"], "Mozilla/3")) ? true : false ;
		$agent["NS4"] = (stristr($_SERVER["HTTP_USER_AGENT"], "Mozilla/4")) ? true : false ;
	}
	
	$agent["MAC"] = (stristr($_SERVER["HTTP_USER_AGENT"], "mac")) ? true : false ;
	if($agent["MAC"]) {
		$agent["iPAD"] = (stristr($_SERVER["HTTP_USER_AGENT"], "iPad")) ? true : false ;
		$agent["iPHONE"] = (stristr($_SERVER["HTTP_USER_AGENT"], "iPhone")) ? true : false ;
	}
	
	$smarty->assign($var, $agent);
}


/* vim: set expandtab: */

?>
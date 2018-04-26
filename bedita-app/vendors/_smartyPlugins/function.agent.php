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
        throw new SmartyException("assign: missing 'var' parameter");
    }
	
	$agent = array(
					"IE" => false,"IE5" => false,"IE6" => false,"IE7" => false,"IE8" => false,"IE9" => false,"IE10" => false,"MOZ" => false,"OP" => false,"OP5" => false,
					"OP6" => false,"NS" => false,"NS3" => false,"NS4" => false,"MAC" => false, "SAFARI" => false, "iPAD" => false, "iPHONE" => false,
					"FIREFOX" => false,"UNKNOW" => false,"GOOGLEBOT" => false
	) ;

	$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
	
	$agent["IE"] = (stristr($userAgent, "MSIE")) ? true : false ;
	if($agent["IE"]) {
		$agent["IE5"] = (stristr($userAgent, "MSIE 5")) ? true : false ;
		$agent["IE55"] = (stristr($userAgent, "MSIE 5.5")) ? true : false ;
		$agent["IE6"] = (stristr($userAgent, "MSIE 6")) ? true : false ;
		$agent["IE7"] = (stristr($userAgent, "MSIE 7")) ? true : false ;
		$agent["IE8"] = (stristr($userAgent, "MSIE 8")) ? true : false ;
		$agent["IE9"] = (stristr($userAgent, "MSIE 9")) ? true : false ;
		$agent["IE10"] = (stristr($userAgent, "MSIE 10")) ? true : false ;
	} 
	
	$agent["MOZ"] = (stristr($userAgent, "Gecko")) ? true : false ;
	if($agent["MOZ"]) {
		$agent["FF3"] = (stristr($userAgent, "Firefox 3") || stristr($userAgent, "Firefox/3")) ? true : false ;
		$agent["SAFARI"] = (stristr($userAgent, "Safari")) ? true : false ;
		$agent["CHROME"] = (stristr($userAgent, "Chrome")) ? true : false ;
		$agent["FIREFOX"] = (stristr($userAgent, "Firefox")) ? true : false ;
	} 

	$agent["OP"] = (stristr($userAgent, "opera")) ? true : false ;
	if($agent["OP"]) {
		$agent["OP5"] = (stristr($userAgent, "opera 5") || stristr($userAgent, "opera/5")) ? true : false ;
		$agent["OP6"] = (stristr($userAgent, "opera 6") || stristr($userAgent, "opera/6")) ? true : false ;
		$agent["OP7"] = (stristr($userAgent, "opera 7") || stristr($userAgent, "opera/7")) ? true : false ;
		$agent["OP8"] = (stristr($userAgent, "opera 8") || stristr($userAgent, "opera/8")) ? true : false ;
		$agent["OP9"] = (stristr($userAgent, "opera 9") || stristr($userAgent, "opera/9")) ? true : false ;
		$agent["OP10"] = (stristr($userAgent, "opera 10") || stristr($userAgent, "opera/10")) ? true : false ;
	} 
	
	$agent["MAC"] = (stristr($userAgent, "mac")) ? true : false ;
	if($agent["MAC"]) {
		$agent["iPAD"] = (stristr($userAgent, "iPad")) ? true : false ;
		$agent["iPHONE"] = (stristr($userAgent, "iPhone")) ? true : false ;
	}

	if(!$agent["IE"] && !$agent["OP"] && !$agent["MOZ"] && !$agent["MAC"]) {
		$agent["UNKNOW"] = true ;
		$agent["GOOGLEBOT"] = (stristr($userAgent, "googlebot")) ? true : false ;
		$agent["NS3"] = (stristr($userAgent, "Mozilla/3")) ? true : false ;
		$agent["NS4"] = (stristr($userAgent, "Mozilla/4")) ? true : false ;
	}

	$smarty->assign($var, $agent);
}


/* vim: set expandtab: */

?>
<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	function
 * Name:	html_list
 * Version:	0.1
 * Date:	2003-03-10
 * Author:	Joscha Feth, joscha@feth.com, www.feth.com
 * Purpose:	Prints a list (either an ordered list <ol>  or an unordered list <ul>)
 * Params:   
 * 		needed		array	values	the values for the list, array can cotain other arrays
 *  	optional	string	list	either "ol" or "ul" default is "ul"
 *		optional	string	type	for ordered lists:	I,i,A or a
 *									for unordered lists:	circle, square or disc
 * 		optional	string	xl_attr	additional attributes for the "ol" or "ul" tags
 * 		optional	string	li_attr	additional attributes for the "li" tags
 * 		optional	string	assign	the variable, the value shall be assigned to.
 * 		Usage:	{html_list values=$array} - creates an unordered list
 * 		{html_list values=$array list="ol"} - creates an ordered list
 * 		{html_list values=$array type="square"} - creates an unordered list with squares
 * 		Install: Drop into the plugin directory 
 * -------------------------------------------------------------
 *		CHANGES:	2003-03-10		-	added possibility to assign output to a variable
 *				2003-03-06		-	created 
 *							
 * -------------------------------------------------------------
 */
function smarty_function_html_list($params, &$smarty)
{
	if(	!is_array($params["values"]) ||
		!count($params["values"])) {
		return "I think you want to create a list - where are the values?";
	}
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    $params["list"] = strtolower($params["list"]);
    
    if(	empty($params["list"]) ||
    	!in_array(strtolower($params["list"]),array("ol","ul"))) {
    	$params["list"] = "ul";
    }
    
    if(	$params["list"] == "ul"	&&
    	!in_array(strtolower($params["type"]),array("circle","square","disc"))) {
    	unset($params["type"]);
    } else if (	$params["list"] == "ol" &&
    			!in_array($params["type"],array("I","i","A","a"))) {
    	unset($params["type"]);
    }
    
    
    $html_result = smarty_function_html_list_make(
    						$params["values"],
    						$params["list"],
    						$params["type"],
    						$params["xl_attr"],
    						$params["li_attr"]);

	if(!empty($params["assign"])) {
		$smarty->assign($params["assign"],$html_result);
	} else {
    	return $html_result;
	}
}


function smarty_function_html_list_make($arr,$list,$type,$xl_attr,$li_attr,$level = 0)
{
	$temp = str_repeat("\t",$level)."<".$list;
	if (!empty($type)) {
		$temp .= " type=\"".$type."\"";
	}
	if (!empty($xl_attr)) {
		$temp .= " ".$xl_attr;
	}
	$temp .= ">\n";
	foreach($arr AS $val) {
		if(is_array($val)) {
			$temp .= smarty_function_html_list_make($val,$list,$type,$xl_attr,$li_attr,$level+1);
		} else {
			$temp .= str_repeat("\t",$level+1)."<li";
            if (!empty($li_attr)) {
            	$temp .= " ".$li_attr;
            }   		
			$temp .= ">".smarty_function_escape_special_chars($val)."</li>\n";
		}
	}
	return $temp.str_repeat("\t",$level)."</".$list.">\n";
}
?>
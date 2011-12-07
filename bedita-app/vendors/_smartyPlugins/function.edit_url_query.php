<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     edit_url_query
 * Version:  1.0
 * Author:   xho - Christiano Presuttu
 * Purpose:  modify query portion of a url
 * Input:    url, query parameters in the form var=value
 * Opt:		 optional parameter 'remove' is an array or a single string which contain parameter you want to delete in query string
 *			 (note: it is performed before processing new values)
 * -------------------------------------------------------------
 */

function smarty_function_edit_url_query($params, &$smarty) {

	if (!array_key_exists ("url", $params))
		$smarty->trigger_error("editUrlQuery: missing url argument", E_USER_NOTICE);

	if (!($params["url"])) $smarty->trigger_error("editUrlQuery: url argument is empty", E_USER_NOTICE);
	else
		// parse url
		$parsed = parse_url($params["url"]);		

	// define
	$query = array();
	$queryString = "";
	$url = "";

	// get vars in query
	parse_str($parsed["query"], $query);

	// remove
	if (array_key_exists ("remove", $params)) {

		if ( is_array($params["remove"]) ) {

			foreach ($params["remove"] as $_key) unset ($query[$_key]);

		} else unset ($query[$params["remove"]]);

	}

	// set values in query
    foreach($params as $_key => $_value) {

		if ($_key != "url" && $_key != "remove") $query[$_key] = $_value;

	}

	// rebuild query
	// $parsed['query'] = http_build_query($query); # function documented but still not implemented in our version
	// another method
	foreach ($query as $_key => $_value) {

		if ( !empty($queryString) ) $queryString .= "&";
		$queryString .= urlencode($_key) . "=" . urlencode($_value);
	
	}

	$parsed['query'] = $queryString;


	// rebuild complete url
	if (array_key_exists("scheme", $parsed)) {
		$url .= $parsed["scheme"];
		$url .= ( (strtolower($parsed["scheme"]) == "mailto") ? ":" : "://" );
	}

	if (array_key_exists("user", $parsed)) {
		$url .= $parsed["user"];
		$url .= (array_key_exists("pass", $parsed) && ($parsed["pass"]) )? ":" . $parsed["pass"] : "";
		$url .= ($parsed["user"])? "@" : "";
	}

	if (array_key_exists("host", $parsed)) $url .= $parsed["host"];

	if (array_key_exists("port", $parsed)) $url .= ":" . $parsed["port"];

	if (array_key_exists("path", $parsed)) $url .= $parsed["path"];

	$url .= "?" . $parsed["query"];

	if (array_key_exists("fragment", $parsed)) $url .= "#" . $parsed["fragment"];

	print $url;
	
	//return $url;
}

?>
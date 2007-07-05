<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     ahref
 * Purpose:  if url is present make link out of a string otherwise simply disply the string
 *			extra features: prefix, postfix if the string is not empty, nice way to decrease the number of 'if' expressions
 * Version:  1.0.1
 * Date:     September 25, 2002
 * Purpose:  Parses the intermediate tags left by compiler.lang
 *           and replaces them with the translated strings,
 *           according to the $compile_id value (language code).
 *          
 * Install:  Drop into the plugin directory
 * Author:   Peter Dudas <duda at bigfish dot hu>
 * -------------------------------------------------------------
 *		CHANGES: 	v1.0	2002.09.25		- created
 *				v1.0.1	-easier support for # urls
 *					-parameters in the url
 */
function smarty_function_ahref($params, &$smarty)
{
/*	
Parameters
	string		- text to put between the <a> tags
	url			- href (simple 'ftp://', http://', 'mailto:'  is regarded as empty)
	extra		- extra tags to put in the <A> tag (alternatives: class, onClick, target)
	class		- <a class=""
	onClick		- <a onClick=""
	target		- <a target=""
	prefix		- if the string was not empty print BEFORE the first char
	postfix		- if the string was not empty print AFTER the last char
	external	- check if http://, email, ftp: is in the url (default: 1);
	par0		- [0] to be replaced with in url
	par1		- [1] to be replaced with in url
	par2		- [2] to be replaced with in the url
*/
	$external = TRUE;

	foreach($params as $key=>$value)	{
		$tmps[strtolower($key)] = $value;
		$tmp = strtolower($key);
		if (!(${$tmp} = $value))	{
			${$tmp} = '';
		}
	}	


	if (empty($string))	{
		return;
	}

	$url = trim($url);
/*	if (($external == 'yes') AND !preg_match('#^([htf]+p://)|(mailto:)$#', $url))	{
		$url = 'http://'.$url;
	}
*/		

	// replacing [0] in the url
	if (preg_match_all('/\[(\d+)\]/', $url, $matches))	{
		foreach($matches[1] as $i)	{
		    $url = preg_replace('/\['.$i.'\]/', ${'par'.$i}, $url);
		}
	}
	
	 
	if (!empty($url))	{	
		// if url is email, add 'mailto:' before it
		if (!preg_match('#^([htf]+p://)|(mailto:)|(/)#', $url))	{
			if 	(ereg('[\w_]@[\w-]',$url))	{
				$url = 'mailto:'.$url;
			} elseif (preg_match('/^#.+/', $url))    {
                                ; 
			} elseif ($external == TRUE)	{
				$url = 'http://'.$url;
			}
		}
		if (!empty($class))	{
			$extra .= ($extra ? ' ' : '').' class="'.$class.'"';
		}
		if (!empty($onClick))	{
			$extra .= ($extra ? ' ' : '').'onClick="'.$onClick.'"';
		}
		if (!empty($target))	{
			$extra .= ($extra ? ' ' : '').'target="'.$target.'"';
		}
		
		$string = $prefix.'<a href="'.$url.'"'.($extra ? ' '.$extra : '').'>'.$string.'</a>'.$postfix;	
	} else	{
		if (!empty($class))	{
			$string = '<span class="'.$class.'">'.$string.'</span>';
		}
		$string = $prefix.$string.$postfix;
	}
	print $string;
}


?>
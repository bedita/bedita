<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     exif_thumb
 * Version:  1.0
 * Author:   Christiano Presutti - aka xho - ChanelWeb srl
 * Purpose:  extract thumbnail from EXIF part of image file
 * Input:    fileURI = string pointer to file - (yet to be made access file path on filesystem)
 * Returns:  image
 * -------------------------------------------------------------
 */
 
function smarty_function_exif_thumbnail ($params, &$smarty)
{
	$pluginName = "image_info";
	$imageInfo	= array(
		"filename"	=> "",
		"w"			=> "",
		"h"			=> "",
		"hrtype"	=> "",
		"attr"		=> "",
		"mimetype"	=> "",
		"bits"		=> "",
		"channels"	=> "",
		"exif"		=> array ()
	);

	extract($params);

    if (empty($var))
	{
        $smarty->trigger_error($pluginName . ": missing 'var' parameter");
        return;
    }

	if ( empty($file) )
	{
		$smarty->trigger_error ($pluginName . ": missing 'file' parameter", E_USER_NOTICE);
		return;
	}

	$size = getimagesize($file);
	$fp = fopen($filename, "rb");
	if ($size && $fp) {
    	header("Content-type: {$size['mime']}");
    	fpassthru($fp);
    	exit;
	} else return false;
?>

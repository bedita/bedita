<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     framed_image (bedita edition)
 * Version:  1.0
 * Author:   Christiano Presutti - aka xho - ChanelWeb srl
 * Purpose:  display an image file in a frame with bg color, ignoring orientation
 * 
 * Input:    fileURI = required, string, string pointer to file (URI),
 *           width   = required if missing height, int number, frame width,
 *           height  = required if missing width, int number, frame height,
 *           
 *           caption      = optional, string, put a caption under the framed image
 *           captionstyle = optional, string, css used to display caption
 *           framestyle   = optional, string, the css style applied to the frame containing image
 *           frameclass   = optional, string, name of the class to apply to the containing frame
 *           imagealign   = optional, string, image alignment into the containing frame
 *           alt          = optional, string, alt attribute for <img> tag
 *           title        = optional, string, title attribute for <img> tag
 *           var          = optional, string, return HTML into $var generating no output
 *           
 *           inherited from frontend.ini (or bedita.ini) conf
 *           $config['smarty']['framed_images']['framestyle'],   overwrites $_default_framestyle
 *           $config['smarty']['framed_images']['imagealign'],   overwrites $_default_imagealign
 *           $config['smarty']['framed_images']['captionstyle'], overwrites $_default_captionstyle
 *           
 * Returns:  ready XHTML to embed the framed image directly to output or in $var (if present) 
 * ----------------------------------------------------------------------------
 */

function smarty_function_framed_image ($params, &$smarty)
{
	$pluginName  = "framed_image";
	
	$_default_framestyle   = "border: 0; background-color: #FFF;";
	$_default_imagealign   = "left";
	$_default_captionstyle = "color: black; font-size: 0.9em;";

	// empties
	$_html		   = "";
	$_framestyle   = "";
	$_captionstyle = "";
	$_imageInfo	   = array (
						"filename"		=> "",
						"path"			=> "",
						"filesize"		=> "",
						"w"				=> "",
						"h"				=> "",
						"orientation"	=> ""
					);

	extract($params);

	if ( empty($file) )
	{
		$smarty->trigger_error ($pluginName . ": missing 'file' parameter", E_USER_NOTICE);
		return;
	}

	if ( empty($width) && empty($height) )
	{
		$smarty->trigger_error ($pluginName . ": missing 'height' or 'width' parameter, at least one is needed", E_USER_NOTICE);
		return;
	}



	// sanitize file path & name (why does not work?)
	$_imageInfo['path'] = str_replace(' ','%20', $file); ; // rawurlencode( $file );


	/*
	 *  Get data or trigger errors
	 */	
	if ( !$_image_data = getimagesize($_imageInfo['path']) )
	{

		if ( !file_exists($_imageInfo['path']) )
		{
			return false;
		}
		else if ( !is_readable($_imageInfo['path']) )
		{
			$smarty -> trigger_error ( $pluginName . ": unable to read '" . $_imageInfo['path'] . "'", E_USER_NOTICE ) ;
			return;
		}
		else
		{
			$smarty -> trigger_error ( $pluginName . ": '" . $_imageInfo['path'] . "' is not a valid image file", E_USER_NOTICE ) ;
			return;
		}
	}



	/*********************************
	 * got everything, proceed
	 *********************************
	 */



	/*
	 * set up image info array
	 */
	$_path					= parse_url ( $_imageInfo['path'], PHP_URL_PATH );
	$_imageInfo['filename']	= end ( explode ( '/', $_path ) );
	$_imageInfo["w"]		= $_image_data [0];
	$_imageInfo["h"]		= $_image_data [1];

	if ($_imageInfo["w"] == $_imageInfo["h"])
	{
		$_imageInfo["portrait"]		= true;
		$_imageInfo["landscape"]	= true;
	}
	else if ($_imageInfo["w"] > $_imageInfo["h"])
	{
		$_imageInfo["portrait"]		= false;
		$_imageInfo["landscape"]	= true;
	}
	else
	{
		$_imageInfo["portrait"]		= true;
		$_imageInfo["landscape"]	= false;
	}




	// set up image resize strategy
	if ( !empty ($width) && !empty ($height) )
	{
		// compare frame size ratio vs image size ratio
		if ( ( $width / $height ) > ( $_imageInfo["w"] / $_imageInfo["h"] ) )
		{
			$_image_target_h = $height;
			$_image_target_w = round ( $_image_target_h * ( $_imageInfo["w"] / $_imageInfo["h"] ) );
		}
		else if ( ( $width / $height ) < ( $_imageInfo["w"] / $_imageInfo["h"] ) )
		{
			$_image_target_w = $width;
			$_image_target_h = round ( $_image_target_w * ( $_imageInfo["h"] / $_imageInfo["w"] ) );
		}
		else if ( ( $width / $height ) == ( $_imageInfo["w"] / $_imageInfo["h"] ) )
		{
			$_image_target_w = $width;
			$_image_target_h = $height;
		}
	}
	else if ( !empty ($width) )
	{
		$_image_target_w = $width;
		$_image_target_h = round ( $_image_target_w * ( $_imageInfo["h"] / $_imageInfo["w"] ) );
		$height = $_image_target_h;
	}
	else if ( !empty ($height) )
	{
		$_image_target_h = $height;
		$_image_target_w = round ( $_image_target_h * ( $_imageInfo["w"] / $_imageInfo["h"] ) );
		$width = $_image_target_w;
	}




	/*
	 * frame style
	 */
	$_framestyle = "position: relative; overflow: visible; width: " . $width . "px; height: " . $height . "px; ";

	// dafaults from bedita conf
	$_bedita_framestyle   =@ Configure::getInstance()->smarty ['framed_images']['framestyle'];
	$_bedita_imagealign   =@ Configure::getInstance()->smarty ['framed_images']['imagealign'];
	$_bedita_captionstyle =@ Configure::getInstance()->smarty ['framed_images']['captionstyle'];


	// bedita conf vs defaults
	if ( !empty( $_bedita_imagealign ) )								// image align
	{
		$_framestyle .= "text-align: " . $_bedita_imagealign . "; ";	// get align from bedita conf
	}
	else
	{
		$_framestyle .= "text-align: " . $_default_imagealign . "; ";	// or use default align
	}
	if ( !empty( $_bedita_framestyle ) )								// frame style
	{
		$_framestyle .= $_bedita_framestyle;							// get style from bedita conf
	}
	else
	{
		$_framestyle .= $_default_framestyle;							// or use default style
	}
	if ( !empty($_bedita_captionstyle) )
	{
		$_captionstyle = $_bedita_captionstyle;
	}
	else
	{
		$_captionstyle = $_default_captionstyle;
	}


	// also add user style (eventually overrides some of the attributes already set) 
	if ( !empty($imagealign) )
	{
		$_framestyle .= " text-align: " . $imagealign;
	}
	if ( !empty($framestyle) )
	{
		$_framestyle .= " " . $framestyle;
	}
	if ( !empty($captionstyle) )
	{
		$_captionstyle .= " " . $captionstyle;
	}


	// so build html attribute
	$_framestyle = "style ='" . $_framestyle . "'";
	


	/*
	 * finally build up HTML code
	 */
	$_html  = "<div " . $_framestyle . ">" . "\n";
	$_html .= "<img src='" . $_imageInfo['path'] . "' alt='" . $alt . "' title='" . $title . "' width='" . $_image_target_w . "' height='" . $_image_target_h . "' />" . "\n";
	if ( !empty($caption) )
	{
		$_html .= "<div style='position: absolute; bottom: -15px; left: 1px; " . $_captionstyle . "'>" . $caption . "</div>" . "\n";
	}

	$_html .= "</div>" . "\n";
	

	// assign to defined var or return
	if ( empty($var) )
	{
		return $_html;
	}
	else $smarty -> assign ( $var, $_html );
}




/*
 * helper functions
 */




?>

<?php
//
// Smarty plugin "thumb_imp"
// Purpose: creates cached thumbnails
//
// Author: Marcus Gueldenmeister (MG)
// Internet: http://www.gueldenmeister.de/marcus/
//
// -----------------------------------------------------------------------------
// The original Smarty plugin "thumb"
// comes from Christoph Erdmann.
// Home: http://www.cerdmann.com/thumb/
// Copyright (C) 2005 Christoph Erdmann
// -----------------------------------------------------------------------------
// This library is free software; you can redistribute it and/or modify it 
// under the terms of the GNU Lesser General Public License as published by 
// the Free Software Foundation; either version 2.1 of the License, or (at 
// your option) any later version.
// 
// This library is distributed in the hope that it will be useful, 
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser 
// General Public License for more details.
// 
// You should have received a copy of the GNU Lesser General Public License 
// along with this library; if not, write to the Free Software Foundation, Inc., 
// 51 Franklin St, Fifth Floor, Boston, MA 02110, USA 
// -----------------------------------------------------------------------------
//
// Author:   Christoph Erdmann (CE)
// Internet: http://www.cerdmann.com
//
// Author: Benjamin Fleckenstein (BF)
// Internet: http://www.benjaminfleckenstein.de
//
// Author: Marcus Gueldenmeister (MG)
// Internet: http://www.gueldenmeister.de/marcus/
//
// Author: Andreas B�sch (AB)
//
// Author: Wolfgang Krane (WK)
//
// Author: xho - ChannelWeb srl
//
// -----------------------------------------------------------------------------
//
// Changelog:/*{{{*/
//
// 2008-03-20 Ability to set different URL and PATH for imgcache
// 2007-11-15 Bugfix transparent index (WK)
// 2007-10-27 thumb_imp + thumb wrapper (MG)
// 2007-10-24 new cached filename format (MG)
// 2007-09-20 Bugfix link/hint parameter (MG)
// 2007-05-06 Bugfix for transparent GIFs (WK)
// 2007-04-19 Added transparent GIF support (MG & WK)
// 2007-04-12 Added legend text (MG)
// 2006-09-24 Added overlay support (CE)
// 2006-09-24 Added support for showing the hint without autolinking the image (CE)
// 2006-09-24 Added frame support.(CE)
// 2005-10-31 Fixed some small bugs (CE)
// 2005-10-09 Rewrote crop-function (CE)
// 2005-10-08 Decreased processing time by prescaling linear and cleaned code (CE)
// 2005-07-13 Set crop=true as standard (CE)
// 2005-07-12 Added crop parameter. Original code by "djneoform at gmail dot com" (AB)
// 2005-07-02 Found a stupid mistake. Should be faster now (CE)
// 2005-06-02 Added file_exists(SOURCE)-trigger (CE)
// 2005-06-02 Added extrapolate parameter (CE)
// 2005-06-12 Bugfix alt/title (MG)
// 2005-06-10 Bugfix (MG)
// 2005-06-02 Added window parameter (MG)
// 2005-06-02 Made grey banner configurable, added possibility to keep format in thumbs
//            made cache path changeable (BF & MG)
// 2004-12-01 New link, hint, quality and type parameter (CE)
// 2004-12-02 Intergrated UnsharpMask (CE)
///*}}}*/
// -----------------------------------------------------------------------------
// 
//
// Preparations
// ------------
// You will have to create the following directory in your Smarty root directory.
// 
// images/cache/
// A public directory (available for browser) the plugin uses for its cached 
// images. Feel free to change the path in the plugin.
// 
// 
// Sample usage
// ------------
// {thumb file="images/visuals/rallyewm02/Rallye142.jpg" 
// width="150" link="false" html='class="img float"'} 
// 
// 
// Parameters/*{{{*/
// ----------
// file [string] (required)
//      The path to your original big-sized image.
// 
// 
// addgreytohint [bool] (Standard: true)
//      Set to �false� to get no lightgrey bottombar.
// 
// cache [string] (Standard: images/cache/)
//      Set to your favorite cache directory.
// 
// cachePATH [string] (default = "")
//      If set, it's used as PATH on the filesystem to image cache dir.
//      If not set, param 'cache' is used for both system PATH and Web URL
//
// crop [bool] (Standard: true)
//      If set to �true�, image will be cropped in the center to destination width and 
//      height params, while keeping aspect ratio. Otherwise the image will get resized.
// 
// dev="dev" - string : default=""
//      for development to ignore caching of the image
//      and generate each time a new image
// 
// extrapolate [bool] (Standard: true)
//      Set to �false� if your source image is smaller than the calculated thumb and 
//      you do not want the image to get extraploated.
// 
// frame [string]
//      A PNG image which is used to create a frame around the thumbnail. This image 
//      will be sliced into 3x3 blocks therefore the image dimensions have to be a 
//      multiplier of 3. An example image (33px x 33px) is 'thumb_border.png'
// 
//      Please note: For performance reasons the frame image will not be checked for 
//      modification.
// 
// height [int] (Standard: 100)
//      The height of your thumbnail. The width (if not set) will be automatically 
//      calculated.
// 
// hint [bool] (Standard: true)
//      If set to �false� the image will get linked but will not have a lens-icon.
// 
// html [string]
//      Will be inserted in the image-tag. Useful, to align text around the thumbnail 
//      or to insert the alt-paramter or to...
// 
// legend="your text" - string : default=""
//      Text which should be printed as a legend at the bottom of the image
// 
// link [bool] (Standard: true)
//      If set to �false� the image will not get linked and not have a lens-icon.
// 
// linkurl [string] (Standard: set to �original image� )
//      Set to your target URL (a href="linkurl").
// 
// longside [int] (Standard: not set)
//      Set the longest side of the image if width, height and shortside is not set.
// 
// overlay [string]
//      A PNG image which is used to create an overlay image. The position will be 
//      determined by �overlay_position�.
//      Please note: For performance reasons the overlay image will not be checked 
//      for modification.
// 
// overlay_position [int] (Standard: 9)
//      The position of the overlay image. Can be an integer from 1 to 9. 
//      Here the positions:
//      1 2 3
//      4 5 6
//      7 8 9
// 
// sharpen [bool] (Standard: true)
//      Set to �false� if you don't want to use the Unsharp-Mask. Thumbnail creation 
//      will be faster, but quality is reduced.
// 
// shortside [int] (Standard: not set)
//      Set the shortest side of the image if width, height and longside is not set.
// 
// type [int]
//      The output file format. Set to 1 for GIF, 2 for JPEG and 3 for PNG. If not 
//      set, the source file format will be used.
// 
// quality="80" - int : default="80"
//      quality of the generated jpg image
// 
// width [int] (Standard: 100)
//      The width of your thumbnail. The height (if not set) will be automatically 
//      calculated.
// 
// window [bool] (Standard: true)
//      Set to �false� if you don�t want to open original image in a new window.
// /*}}}*/
// -----------------------------------------------------------------------------

function smarty_function_thumb_imp($params, &$smarty) {/*{{{*/

	// Set defaults to avoid NOTICES - XHO
	if (@empty($params['dev']))				$params['dev']			= false;
	if (@empty($params['longside']))			$params['longside']		= false;
	if (@empty($params['shortside']))		$params['shortside'] 	= false;
	if (@empty($params['html']))				$params['html']			= false;
	if (@empty($params['sharpen']))			$params['sharpen']		= false;
	if (@empty($params['addgreytohint']))	$params['addgreytohint']= false;

	$_DST['offset_w']	= false;
	$_DST['offset_h']	= false;
//	$returner			= false;


	// Start time measurement/*{{{*/
	if ($params['dev']) {
		if (!function_exists('getmicrotime')) {
			function getmicrotime() {
				list($usec, $sec) = explode(" ",microtime());
				return ((float)$usec + (float)$sec);
            }
        }
		$time['start'] = getmicrotime();
    }/*}}}*/
		
	if (!function_exists('UnsharpMask')) {
		function UnsharpMask($img, $amount, $radius, $threshold) {/*{{{*/
            // Unsharp mask algorithm by Torstein H�nsi 2003 (thoensi_at_netcom_dot_no)
            // Christoph Erdmann: changed it a little, 
            // cause i could not reproduce the darker blurred image, 
            // now it is up to 15% faster with same results
			// Attempt to calibrate the parameters to Photoshop:
            
            if ($amount > 500) $amount = 500;
			$amount = $amount * 0.016;
			if ($radius > 50) $radius = 50;
			$radius = $radius * 2;
			if ($threshold > 255) $threshold = 255;
	
			$radius = abs(round($radius)); 	// Only integers make sense.
			if ($radius == 0) {	return $img; imagedestroy($img); break;	}
			$w = imagesx($img); $h = imagesy($img);
			$imgCanvas = $img;
			$imgCanvas2 = $img;
			$imgBlur = imagecreatetruecolor($w, $h);
	
			// Gaussian blur matrix:
			//	1	2	1		
			//	2	4	2		
			//	1	2	1		

			// Move copies of the image around one pixel at the time and merge them with weight
			// according to the matrix. The same matrix is simply repeated for higher radii.
			for ($i = 0; $i < $radius; $i++) {
				imagecopy	  ($imgBlur, $imgCanvas, 0, 0, 1, 1, $w - 1, $h - 1); // up left
				imagecopymerge ($imgBlur, $imgCanvas, 1, 1, 0, 0, $w, $h, 50); // down right
				imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 1, 0, $w - 1, $h, 33.33333); // down left
				imagecopymerge ($imgBlur, $imgCanvas, 1, 0, 0, 1, $w, $h - 1, 25); // up right
				imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 1, 0, $w - 1, $h, 33.33333); // left
				imagecopymerge ($imgBlur, $imgCanvas, 1, 0, 0, 0, $w, $h, 25); // right
				imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 20 ); // up
				imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 16.666667); // down
				imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 0, $w, $h, 50); // center
            }
			$imgCanvas = $imgBlur;	
				
			// Calculate the difference between the blurred pixels and the original
			// and set the pixels
			for ($x = 0; $x < $w; $x++) { // each row
				for ($y = 0; $y < $h; $y++) { // each pixel
					$rgbOrig = ImageColorAt($imgCanvas2, $x, $y);
					$rOrig = (($rgbOrig >> 16) & 0xFF);
					$gOrig = (($rgbOrig >> 8) & 0xFF);
					$bOrig = ($rgbOrig & 0xFF);
					$rgbBlur = ImageColorAt($imgCanvas, $x, $y);
					$rBlur = (($rgbBlur >> 16) & 0xFF);
					$gBlur = (($rgbBlur >> 8) & 0xFF);
					$bBlur = ($rgbBlur & 0xFF);

					// When the masked pixels differ less from the original
					// than the threshold specifies, they are set to their original value.
					$rNew = (abs($rOrig - $rBlur) >= $threshold) ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig)) : $rOrig;
					$gNew = (abs($gOrig - $gBlur) >= $threshold) ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig)) : $gOrig;
					$bNew = (abs($bOrig - $bBlur) >= $threshold) ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig)) : $bOrig;
					
					if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
						$pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
						ImageSetPixel($img, $x, $y, $pixCol);
                    }
                }
            }
			return $img;
        }/*}}}*/
    }

	$_CONFIG['types'] = array('','.gif','.jpg','.png');

	### check parameters - PATH & URL fork by xho /*{{{*/
	if (empty($params['cache']))
	{
		$_CONFIG['cachePATH']	= 'images/cache/';
		$_CONFIG['cache']		= 'images/cache/';
	}
	else
	{
//		$_CONFIG['cache']		= $params['cache']; // old behaviour (pre-fork) - xho
		$_CONFIG['cachePATH']	= ( empty($params['cachePATH']) )? $params['cache'] : $params['cachePATH'];
		$_CONFIG['cache']		= $params['cache'];
	}
	if (empty($params['file'])) { $smarty->_trigger_fatal_error("thumb: parameter 'file' cannot be empty");return; }
	if (!file_exists($params['file']))
	{
		// XHO: nopn da fatal error se l'immagine non esiste, ma un notice
		$smarty->trigger_error ( $pluginName . ": thumb: image file does not exist '" . $params['file'] . "'", E_USER_NOTICE ) ;
		//$smarty->_trigger_fatal_error("thumb: image file does not exist");return;
	}
	if (empty($params['link'])) $params['link'] = true;
	if (empty($params['window'])) $params['window'] = true;
	if (empty($params['hint'])) $params['hint'] = true;
	if (empty($params['extrapolate'])) $params['extrapolate'] = true;
	if (empty($params['dev'])) $params['crop'] = false;
	if (empty($params['crop'])) $params['crop'] = true;
	if (empty($params['width']) AND empty($params['height']) 
		AND empty($params['longside']) AND empty($params['shortside'])) $params['width'] = 100;
    if (empty($params['overlay_position'])) $params['overlay_position'] = 9;
    /*}}}*/
		
	### Info �ber Source (SRC) holen
	$temp = getimagesize($params['file']);

	$_SRC['file']		= $params['file'];
	$_SRC['width']		= $temp[0];
	$_SRC['height']		= $temp[1];
	$_SRC['type']		= $temp[2]; // 1=GIF, 2=JPG, 3=PNG, SWF=4
	$_SRC['string']		= $temp[3];
	$_SRC['filename'] 	= basename($params['file']);
	$_SRC['modified'] 	= filemtime($params['file']);

	// Hash erstellen
	$_SRC['hash'] 		= md5($_SRC['file'].$_SRC['modified'].implode('',$params));


	### calculate informations for destination (DST)/*{{{*/
	if (is_numeric($params['width'])) $_DST['width'] = $params['width'];
	else $_DST['width'] = round($params['height']/($_SRC['height']/$_SRC['width']));

	if (is_numeric($params['height'])) $_DST['height']	= $params['height'];
	else $_DST['height'] = round($params['width']/($_SRC['width']/$_SRC['height']));
	
	// Das Gr��enverh�ltnis soll erhalten bleiben egal ob das Bild hoch oder querformatig ist.
	if (is_numeric($params['longside'])) {
		if ($_SRC['width'] < $_SRC['height']) {
			$_DST['height']	= $params['longside'];
			$_DST['width']	= round($params['longside']/($_SRC['height']/$_SRC['width']));
        } else {
			$_DST['width']	= $params['longside'];
			$_DST['height']	= round($params['longside']/($_SRC['width']/$_SRC['height']));
        }
    } elseif (is_numeric($params['shortside'])) {
		if ($_SRC['width'] < $_SRC['height']) {
			$_DST['width']	= $params['shortside'];
			$_DST['height']	= round($params['shortside']/($_SRC['width']/$_SRC['height']));
        } else {
			$_DST['height']	= $params['shortside'];
			$_DST['width']	= round($params['shortside']/($_SRC['height']/$_SRC['width']));
        }
    }/*}}}*/

	// check for crop option (default)/*{{{*/
	if($params['crop']) {							
		$width_ratio = $_SRC['width']/$_DST['width'];
		$height_ratio = $_SRC['height']/$_DST['height'];
		
		// crop at width
		if ($width_ratio > $height_ratio) {
			$_DST['offset_w'] = round(($_SRC['width']-$_DST['width']*$height_ratio)/2);
			$_SRC['width'] = round($_DST['width']*$height_ratio);
        }
		// crop at height
		elseif ($width_ratio < $height_ratio) {
			$_DST['offset_h'] = round(($_SRC['height']-$_DST['height']*$width_ratio)/2);
			$_SRC['height'] = round($_DST['height']*$width_ratio);
        }
    }/*}}}*/

    // Wenn das Ursprungsbild kleiner als das Ziel-Bild ist, 
    // soll nicht hochskaliert werden und die neu berechneten Werte werden wieder �berschrieben
	if ($params['extrapolate'] == 'false' && $_DST['height'] > $_SRC['height'] && $_DST['width'] > $_SRC['width']) {
		$_DST['width'] = $_SRC['width'];
		$_DST['height'] = $_SRC['height'];
    }
		
    if (!empty($params['type'])) $_DST['type'] = $params['type']; 
    else $_DST['type'] = $_SRC['type'];


    // Output filename for caching
    // eliminate the dots in the original filename
    $tmp_filename = str_replace(".", "_", $_SRC['filename']);

	// fileURL fork by XHO
    $_DST['file']		= $_CONFIG['cachePATH']	. $tmp_filename."_".$_SRC['hash'].$_CONFIG['types'][$_DST['type']];
    $_DST['fileURL']	= $_CONFIG['cache']		. $tmp_filename."_".$_SRC['hash'].$_CONFIG['types'][$_DST['type']];

	$_DST['string']		= 'width="'.$_DST['width'].'" height="'.$_DST['height'].'"';

    // is a legend available?/*{{{*/
    if (!empty($params['legend'])) {
        //text_legend_height depends on the later used font!
        $text_legend_height = 15;
		$_DST['string'] = 'width="'.($_DST['width']).'" height="'.($_DST['height']+$text_legend_height).'"';
    } else {
        //this must be set to 0, so that the frame size can be set correct
        $text_legend_height = 0;
    }/*}}}*/

	// is a frame available?/*{{{*/
	if (!empty($params['frame'])) {
		// check if valid
		$imagesize = getimagesize($params['frame']);
        if ($imagesize[0] != $imagesize[1] OR $imagesize[0]%3 OR !file_exists($params['frame'])) { 
            $smarty->_trigger_fatal_error("thumb: wrong dimensions of 'frame'-image or width and height is not a multiplier of 3"); return; 
        }
		// Blockgr��e brauche ich schon hier, falls ein gecachtes Bild wiedergegeben werden soll
		$frame_blocksize = $imagesize[0]/3;

		$_DST['string'] = 'width="'.($_DST['width']+2*$frame_blocksize).'" height="'.($_DST['height']+2*$frame_blocksize+$text_legend_height).'"';
    }/*}}}*/

	### generate return string - fileURL fork by XHO/*{{{*/
	if (empty($params['html']))
		$_RETURN['img'] = '<img src="' . $_DST['fileURL'] .'" '.$params['html'].' '.$_DST['string'].' alt="" title="" />';
	else
		$_RETURN['img'] = '<img src="' . $_DST['fileURL'] .'" '.$params['html'].' '.$_DST['string'].' />';

	if ($params['link'] == "true") {
		if (empty($params['linkurl'])) $params['linkurl'] = $_SRC['file'];
		
		if ($params['window'] == "true") $returner = '<a href="'.$params['linkurl'].'" target="_blank">'.$_RETURN['img'].'</a>';
		else $returner = '<a href="'.$params['linkurl'].'">'.$_RETURN['img'].'</a>';
    } else {
		$returner = $_RETURN['img']; //echo $_RETURN['img'];
    }/*}}}*/

    ############################    
	### check for cache file ###
    ############################    
	if (file_exists($_DST['file']) AND !$params['dev']) return $returner;
	
	
    ############################
	###  otherwise proceed   ###
    ############################
	
	// read SRC/*{{{*/
    if ($_SRC['type'] == 1)	{
        $_SRC['image'] = imagecreatefromgif($_SRC['file']);
        $_SRC['gif_colorstotal'] = imagecolorstotal($_SRC['image']);
        $_SRC['gif_transparent_index'] = imagecolortransparent($_SRC['image']);

        //we have a transparent color
        if (($_SRC['gif_transparent_index'] >= 0) 
            && ($_SRC['gif_transparent_index'] < $_SRC['gif_colorstotal'])) {
        //get the actual transparent color
            $gif_rgb = imagecolorsforindex($_SRC['image'], $_SRC['gif_transparent_index']);
            $_SRC['gif_original_transparency_rgb'] = ($gif_rgb['red'] << 16) | ($gif_rgb['green'] << 8) | $gif_rgb['blue'];
            
            //change the transparent color to black, since transparent goes to black anyways (no way to remove transparency in GIF)
            imagecolortransparent($_SRC['image'], imagecolorallocate($_SRC['image'], 0, 0, 0));
        }
    }
	if ($_SRC['type'] == 2)	$_SRC['image'] = imagecreatefromjpeg($_SRC['file']);
	if ($_SRC['type'] == 3)	$_SRC['image'] = imagecreatefrompng($_SRC['file']);
/*}}}*/

    // if the image is very large, scale linear to 4x $_DST size and overwrite the source $_SRC/*{{{*/
	if ($_DST['width']*4 < $_SRC['width'] AND $_DST['height']*4 < $_SRC['height']) {
		// Multiplikator der Zielgr��e
		$_TMP['width'] = round($_DST['width']*4);
		$_TMP['height'] = round($_DST['height']*4);
		
        $_TMP['image'] = imagecreatetruecolor($_TMP['width'], $_TMP['height']);
		imagecopyresized($_TMP['image'], $_SRC['image'], 0, 0, $_DST['offset_w'], $_DST['offset_h'], $_TMP['width'], $_TMP['height'], $_SRC['width'], $_SRC['height']);
		$_SRC['image'] = $_TMP['image'];
		$_SRC['width'] = $_TMP['width'];
		$_SRC['height'] = $_TMP['height'];

		// Wenn vorskaliert wird, darf ja nicht nochmal ein bestimmter Bereich ausgeschnitten werden
		$_DST['offset_w'] = 0;
		$_DST['offset_h'] = 0;
		unset($_TMP['image']);
    }/*}}}*/

	// DST erstellen
    $_DST['image'] = imagecreatetruecolor($_DST['width'], $_DST['height']);

    if (($_SRC['type'] == 1) && ($_SRC['gif_transparent_index'] >= 0)) {
        //only for transparent gif:
        imagealphablending($_SRC['image'], false);
        imagesavealpha($_SRC['image'], true);
	    imagecopyresized($_DST['image'], $_SRC['image'], 0, 0, $_DST['offset_w'], $_DST['offset_h'], $_DST['width'], $_DST['height'], $_SRC['width'], $_SRC['height']);
	} else {
	    imagecopyresampled($_DST['image'], $_SRC['image'], 0, 0, $_DST['offset_w'], $_DST['offset_h'], $_DST['width'], $_DST['height'], $_SRC['width'], $_SRC['height']);
	    if ($params['sharpen'] != "false") $_DST['image'] = UnsharpMask($_DST['image'],80,.5,3);
	}

	// add a magnifier/*{{{*/
	if ( ($params['link'] == "true") && ($params['hint'] == "true") ) {
		// sure to add a white bar?
		if ($params['addgreytohint'] != 'false') {
			$trans = imagecolorallocatealpha($_DST['image'], 255, 255, 255, 25);
			imagefilledrectangle($_DST['image'], 0, $_DST['height']-9, $_DST['width'], $_DST['height'], $trans);
        }
		$magnifier = imagecreatefromstring(gzuncompress(base64_decode("eJzrDPBz5+WS4mJgYOD19HAJAtLcIMzBBiRXrilXA1IsxU6eIRxAUMOR0gHkcxZ4RBYD1QiBMOOlu3V/gIISJa4RJc5FqYklmfl5CiGZuakMBoZ6hkZ6RgYGJs77ex2BalRBaoLz00rKE4tSGXwTk4vyc1NTMhMV3DKLUsvzi7KLFXwjFEAa2svWnGdgYPTydHEMqZhTOsE++1CAyNHzm2NZjgau+dAmXlAwoatQmOld3t/NPxlLMvY7sovPzXHf7re05BPzjpQTMkZTPjm1HlHkv6clYWK43Zt16rcDjdZ/3j2cd7qD4/HHH3GaprFrw0QZDHicORXl2JsPsveVTDz//L3N+WpxJ5Hff+10Tjdd2/Vi17vea79Om5w9zzyne9GLnWGrN8atby/ayXPOsu2w4quvVtxNCVVz5nAf3nDpZckBCedpqSc28WTOWnT7rZNXZSlPvFybie9EFc6y3bIMCn3JAoJ+kyyfn9qWq+LZ9Las26Jv482cDRE6Ci0B6gVbo2oj9KabzD8vyMK4ZMqMs2kSvW4chz88SXNzmeGjtj1QZK9M3HHL8L7HITX3t19//VVY8CYDg9Kvy2vDXu+6mGGxNOiltMPsjn/t9eJr0ja/FOdi5TyQ9Lz3fOqstOr99/dnro2vZ1jy76D/vYivPsBoYPB09XNZ55TQBAAJjs5s</body>")));
		imagealphablending($_DST['image'], true);
		imagecopy($_DST['image'], $magnifier, $_DST['width']-15, $_DST['height']-14, 0, 0, 11, 11);
		imagedestroy($magnifier);
    }/*}}}*/

	// add an overlay image/*{{{*/
	if (!empty($params['overlay'])) {
		// load the "overlay"-image
		$overlay = imagecreatefrompng($params['overlay']);
		$overlay_size = getimagesize($params['overlay']);

		// copy overlay-image to the correct position of the original
		if ($params['overlay_position'] == '1') imagecopy($_DST['image'], $overlay, 0, 0, 0, 0, $overlay_size[0], $overlay_size[1]); // ecke links oben
		if ($params['overlay_position'] == '2') imagecopy($_DST['image'], $overlay, $_DST['width']/2-$overlay_size[0]/2, 0, 0, 0, $overlay_size[0], $overlay_size[1]); // ecke links oben
		if ($params['overlay_position'] == '3') imagecopy($_DST['image'], $overlay, $_DST['width']-$overlay_size[0], 0, 0, 0, $overlay_size[0], $overlay_size[1]); // ecke links oben
		if ($params['overlay_position'] == '4') imagecopy($_DST['image'], $overlay, 0, $_DST['height']/2-$overlay_size[1]/2, 0, 0, $overlay_size[0], $overlay_size[1]); // ecke links oben
		if ($params['overlay_position'] == '5') imagecopy($_DST['image'], $overlay, $_DST['width']/2-$overlay_size[0]/2, $_DST['height']/2-$overlay_size[1]/2, 0, 0, $overlay_size[0], $overlay_size[1]); // ecke links oben
		if ($params['overlay_position'] == '6') imagecopy($_DST['image'], $overlay, $_DST['width']-$overlay_size[0], $_DST['height']/2-$overlay_size[1]/2, 0, 0, $overlay_size[0], $overlay_size[1]); // ecke links oben
		if ($params['overlay_position'] == '7') imagecopy($_DST['image'], $overlay, 0, $_DST['height']-$overlay_size[1], 0, 0, $overlay_size[0], $overlay_size[1]); // ecke links oben
		if ($params['overlay_position'] == '8') imagecopy($_DST['image'], $overlay, $_DST['width']/2-$overlay_size[0]/2, $_DST['height']-$overlay_size[1], 0, 0, $overlay_size[0], $overlay_size[1]); // ecke links oben
		if ($params['overlay_position'] == '9') imagecopy($_DST['image'], $overlay, $_DST['width']-$overlay_size[0], $_DST['height']-$overlay_size[1], 0, 0, $overlay_size[0], $overlay_size[1]); // ecke links oben
    }/*}}}*/

	// Berechnungszeit hinzuf�gen/*{{{*/
	if ($params['dev']) {
		// Zeit anhalten
		$time['end'] = getmicrotime();
		$time = round($time['end'] - $time['start'],2);
		
		// Farben definieren
		$white_trans = imagecolorallocatealpha($_DST['image'], 255, 255, 255, 25);
		$black = ImageColorAllocate ($_DST['image'], 0, 0, 0);

		// Wei�er Balken oben
		imagefilledrectangle($_DST['image'], 0, 0, $_DST['width'], 10, $white_trans);

		// Schrift mit Zeitangabe
		imagestring($_DST['image'], 1, 5, 2, 'time: '.$time.'s', $black);
    }/*}}}*/

    // add a legend text to the image/*{{{*/
    if (!empty($params['legend'])) {
        //text_legend_height depends on the later used font and is defined at above cache relevant section!

		// Neues Bild erstellen und bisher erzeugtes Bild hereinkopieren
		$_LEGEND['image'] = imagecreatetruecolor($_DST['width'], $_DST['height']+$text_legend_height);
		imagecopy($_LEGEND['image'], $_DST['image'], 0, 0, 0, 0, $_DST['width'], $_DST['height']);

        //RBG 230 = lightgrey 90%
        $color = imagecolorallocate($_LEGEND['image'], 230, 230, 230); 
        imagefilledrectangle($_LEGEND['image'], 0, $_DST['height'], $_DST['width'], $_DST['height']+$text_legend_height, $color);

        $font  = 3; //valid values are 1..5
        $text  = $params['legend'];
        //RGB 26 = a little brighter than black, 10%
        $color = imagecolorallocate($_LEGEND['image'], 26, 26, 26); 
        imagestring($_LEGEND['image'], $font, 3, $_DST[height], $text, $color);

        $_DST['image']	= $_LEGEND['image'];
		$_DST['height']	= $_DST['height']+$text_legend_height;
        $_DST['string2']	= 'width="'.$_DST['width'].'" height="'.$_DST['height'].'"';

		$returner = str_replace($_DST['string'], $_DST['string2'], $returner);
        
        $_DST['string'] = 'width="'.($_DST['width']).'" height="'.($_DST['height']).'"';
    }/*}}}*/

	// add a frame to the image/*{{{*/
	if (!empty($params['frame'])) {
		// load "frame"-image and initialize
		$frame = imagecreatefrompng($params['frame']);
		$frame_blocksize = $imagesize[0]/3;

		// craete new image and copy the current image into the new one
		$_FRAME['image'] = imagecreatetruecolor($_DST['width']+2*$frame_blocksize, $_DST['height']+2*$frame_blocksize);
		imagecopy($_FRAME['image'], $_DST['image'], $frame_blocksize, $frame_blocksize, 0, 0, $_DST['width'], $_DST['height']);

		// now draw the frame
		// edge
		imagecopy($_FRAME['image'], $frame, 0, 0, 0, 0, $frame_blocksize, $frame_blocksize); // ecke links oben
		imagecopy($_FRAME['image'], $frame, $_DST['width']+$frame_blocksize, 0, 2*$frame_blocksize, 0, $frame_blocksize, $frame_blocksize); // ecke rechts oben
		imagecopy($_FRAME['image'], $frame, $_DST['width']+$frame_blocksize, $_DST['height']+$frame_blocksize, 2*$frame_blocksize, 2*$frame_blocksize, $frame_blocksize, $frame_blocksize); // ecke rechts unten
		imagecopy($_FRAME['image'], $frame, 0, $_DST['height']+$frame_blocksize, 0, 2*$frame_blocksize, $frame_blocksize, $frame_blocksize); // ecke links unten
		// side
		imagecopyresized($_FRAME['image'], $frame, $frame_blocksize, 0, $frame_blocksize, 0, $_DST['width'], $frame_blocksize, $frame_blocksize, $frame_blocksize); // oben
		imagecopyresized($_FRAME['image'], $frame, $_DST['width']+$frame_blocksize, $frame_blocksize, 2*$frame_blocksize, $frame_blocksize, $frame_blocksize, $_DST['height'], $frame_blocksize, $frame_blocksize); // rechts
		imagecopyresized($_FRAME['image'], $frame, $frame_blocksize, $_DST['height']+$frame_blocksize, $frame_blocksize, 2*$frame_blocksize, $_DST['width'], $frame_blocksize, $frame_blocksize, $frame_blocksize); // unten
		imagecopyresized($_FRAME['image'], $frame, 0, $frame_blocksize, 0, $frame_blocksize, $frame_blocksize, $_DST['height'], $frame_blocksize, $frame_blocksize); // links
	
		$_DST['image']	= $_FRAME['image'];
		$_DST['width']	= $_DST['width']+2*$frame_blocksize;
		$_DST['height']	= $_DST['height']+2*$frame_blocksize;
		$_DST['string2']	= 'width="'.$_DST['width'].'" height="'.$_DST['height'].'"';

        $returner = str_replace($_DST['string'], $_DST['string2'], $returner);
        $color = imagecolorallocate($_FRAME['image'], 26, 26, 26); 
    }/*}}}*/
	
	// store thumbnail/*{{{*/
	if ($_DST['type'] == 1) {
        //remake transparency (if there was transparency)
        if ($_SRC['gif_transparent_index'] >= 0) {
            imagealphablending($_DST['image'], false);
            imagesavealpha($_DST['image'], true);
            for ($x = 0; $x < $_DST['width']; $x++) {
                for ($y = 0; $y < $_DST['height']; $y++) {
                    if (imagecolorat($_DST['image'], $x, $y) == $_SRC['gif_original_transparency_rgb']) {
                        $merkx = $x;
                        $merky = $y;
                        $x=$_DST['width'];
                        $y=$_DST['height'];
                    }
                }
            }
        }
        imagetruecolortopalette($_DST['image'], false, $_SRC['gif_colorstotal']);
        if ($_SRC['gif_transparent_index'] >= 0) {
            $id = imagecolorat($_DST['image'], $merkx, $merky);
            if ($id >= 0) {
                imagecolortransparent($_DST['image'],$id);
            }
        }
		imagegif($_DST['image'], $_DST['file']);
    }
    if ($_DST['type'] == 2) {
		Imageinterlace($_DST['image'], 1);
		if (empty($params['quality'])) $params['quality'] = 80;
		imagejpeg($_DST['image'], $_DST['file'],$params['quality']);
    }
	if ($_DST['type'] == 3) {
		imagepng($_DST['image'], $_DST['file']);
    }/*}}}*/
	
	imagedestroy($_DST['image']);
	imagedestroy($_SRC['image']);
	
	// return now the final image
	return $returner;
	
}/*}}}*/

?>

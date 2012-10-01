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
 * Input:    file      = required, string, string pointer to file (URI),
 *           width     = required if missing height or longside, int number, frame width,
 *           height    = required if missing width or longside, int number, frame height,
 *           longside  = required if missing width or height, int number, frame width or height (longer),
 *           
 *           imageonly     = optional, bool, return only image URI (eventually resampled and cached)
 *           
 *           filepath     = optional, string, path on filesystem to file (if missing assumed relative to URI)
 *           origwidth    = optional, original image width (if available) so to avoid the "time consuming" getimagesize()
 *           origheight   = optional, original image width (if available) so to avoid the "time consuming" getimagesize()
 *           caption      = optional, string, put a caption under the framed image
 *           captionstyle = optional, string, css used to display caption
 *           framestyle   = optional, string, the css style applied to the frame containing image
 *           frameclass   = optional, string, name of the class to apply to the containing frame
 *           imagealign   = optional, string, image alignment into the containing frame
 *           alt          = optional, string, alt attribute for <img> tag
 *           title        = optional, string, title attribute for <img> tag
 *           imagetype    = optional, string, force jpg, png or gif creation during resample
 *           imagequality = optional, int,    quality for jpeg compression (defaults to 80%)
 *           noscale      = optional, bool,   scale image or not (if image bigger than frame overflow hidden)
 *           noresample   = optional, bool,   do not resample image (if noscale true, do client side resize)
 *           var          = optional, string, return HTML into $var generating no output
 *           
 *           inherited from frontend.ini (or bedita.ini) conf
 *           $config['smarty']['framed_images']['framestyle'],   overwrites $_default_framestyle
 *           $config['smarty']['framed_images']['imagealign'],   overwrites $_default_imagealign
 *           $config['smarty']['framed_images']['captionstyle'], overwrites $_default_captionstyle
 *           
 * Returns:  ready XHTML to embed the framed image directly into output or, if present, in $var ie {framed_image var=foo...}
 * ----------------------------------------------------------------------------
 */

function smarty_function_framed_image ($params, &$smarty)
{
	$pluginName  = "framed_image";
	
	$_default_framestyle   = "border: 0; background-color: #FFF;";
	$_default_imagealign   = "left";
	$_default_captionstyle = "color: black; font-size: 0.9em;";
	$_DS = "/";

	// supported image types (used after getimagesize)
	$_imagetype = array ("", "gif", "jpg", "png", "swf");
	$_defaultimagetype = 2; // defaults to 2 [= JPG]

	// empties
	$_html		   = "";
	$_framestyle   = "";
	$_captionstyle = "";
	$_noscale      = false;
	$_resample     = false;
	$_imageInfo	   = array (
						"filename"		=> "",
						"file"			=> "",
						"path"			=> "",
						"filesize"		=> "",
						"w"				=> "",
						"h"				=> "",
						"orientation"	=> "",
						"imagetype"		=> ""
					);

	$_imageTarget   = array (
						"file"			=> "",
						"path"			=> "",
						"w"				=> "",
						"h"				=> "",
						"offsetx"		=> 0,	// OFFSETS SET AT 0 - FUTURE IMPLEMENTATIONS
						"offsety"		=> 0,
						"imagetype"		=> "",
						"mode"			=> 1	// resample mode crop = 0 or fill = 1 (default fill) 
					);


	/*
	 * get parameters or trigger error (notice)
	 */
	extract($params);

	if ( empty($file) )
	{
		throw new SmartyException($pluginName . ": missing 'file' parameter", E_USER_NOTICE);
	}

	if ( empty($width) && empty($height) )
	{
		throw new SmartyException($pluginName . ": missing 'height' or 'width' parameter, at least one is needed", E_USER_NOTICE);
	}


	// compatibility with different syntax (to be removed)
	if ( !@empty($filePATH) ) $filepath = $filePATH;

	// sanitize file path & name
	$_imageInfo['path'] = str_replace (' ','%20', $file); ; // rawurlencode( $file ) does not work, why?!;

	$_path					 = parse_url ( $_imageInfo['path'], PHP_URL_PATH );
	$_imageInfo['filename']	 = end ( explode ( '/', $_path ) );
	$_imageInfo['ext']		 = end ( explode ( '.', $_imageInfo['filename'] ) );
	$_imageInfo['file']		 = $_path;
	$_imageInfo['filepath']	 = (@empty($filepath))? $_SERVER['DOCUMENT_ROOT'] . $_path : $filepath; // if missing parameter filepath assume filesystem path from SERVER env



	/*
	 *  Get data or trigger errors
	 */	
	if ( !file_exists($_imageInfo['filepath']) )
	{
			// don't display errors (DA FARE + MEGLIO)

			if ( !@empty($imageonly) )
			{
				$html = $_imageInfo['filename'];
			}
			else {
				$_html = '<img src="' . $_imageInfo['filename'] . '" width="50" alt="missing image" title="image is missing:' . $_imageInfo['filename'] . '">';
			}

			if ( empty($var) ) return $_html;
			else $smarty -> assign ( $var, $_html );
			return;
	}
	else if ( !is_readable($_imageInfo['filepath']) )
	{
		throw new SmartyException($pluginName . ": unable to read '" . $_imageInfo['path'] . "'", E_USER_NOTICE ) ;
	}


	// build _image_data with getimagesize() or available parameters
	if ( empty($origwidth) || empty($origheight) )
	{
		if ( !$_image_data =@ getimagesize($_imageInfo['path']) )
		{
			throw new SmartyException($pluginName . ": '" . $_imageInfo['path'] . "' is not a valid image file", E_USER_NOTICE ) ;
		}
		
		// set up the rest of image info array
		$_imageInfo["w"]		 = $_image_data [0];
		$_imageInfo["h"]		 = $_image_data [1];
		$_imageInfo['imagetype'] = $_image_data [2]; // 1=GIF, 2=JPG, 3=PNG, SWF=4
	}
	else
	{
		$_imageInfo["w"]		 = $origwidth;
		$_imageInfo["h"]		 = $origheight;

		// since not using getimagesize(), try to get image type from file extension
		if ( !( $_imageInfo['imagetype'] =@ array_isearch($_imageInfo['ext'], $_imagetype) ) )
		{
			$_imageInfo['imagetype'] = $_defaultimagetype; // defaults to 2 [= JPG]
		}
	}



	// destination image type
	if ( !@empty($params['imagetype']) )
	{
		$_imageTarget['imagetype'] = $params['imagetype'];
	} else {
		$_imageTarget['imagetype'] = $_imageInfo['imagetype'];
	}


	// resample mode (fill=1/crop=0)
	if ( isset($mode) )
	{
		$_imageTarget['mode'] = $mode;
	}
	

	// destination scale
	if ( !@empty($noscale) )
	{
		$_noscale = $noscale;
	}
	




	/*********************************
	 * got everything, proceed
	 *********************************
	 */




	/*
	 * image resize strategy
	 */
	if ( $_noscale )
	{
		$_imageTarget['w'] = $_imageInfo["w"];
		$_imageTarget['h'] = $_imageInfo["h"];
	}
	else
	{
		if ( !empty ($width) && !empty ($height) )
		{
			// special -> same ratio or mode croponly/stretch (2/3)
			if ( ( $width / $height ) == ( $_imageInfo["w"] / $_imageInfo["h"] ) || $_imageTarget['mode'] > 1 )
			{
				$_imageTarget['w'] = $width;
				$_imageTarget['h'] = $height;
			}
			else 
			{
				// compare target size ratio vs original size ratio
				if ( ( $width / $height ) < ( $_imageInfo["w"] / $_imageInfo["h"] ) )
					$_temp = 1;
				else
					$_temp = 0;
	
				// invert if fill
				if ( $_imageTarget['mode'] )  $_temp = 1 - $_temp;
	
	
				// set target size
				if ( $_temp )
				{
					$_imageTarget['h'] = $height;
					$_imageTarget['w'] = floor ( $_imageTarget['h'] * ( $_imageInfo["w"] / $_imageInfo["h"] ) );
				}
				else
				{
					$_imageTarget['w'] = $width;
					$_imageTarget['h'] = floor ( $_imageTarget['w'] * ( $_imageInfo["h"] / $_imageInfo["w"] ) );
				}
			}
		}
		else if ( !empty ($width) )
		{
			$_imageTarget['w'] = $width;
			$_imageTarget['h'] = round ( $_imageTarget['w'] * ( $_imageInfo["h"] / $_imageInfo["w"] ) );
			$height = $_imageTarget['h'];
		}
		else if ( !empty ($height) )
		{
			$_imageTarget['h'] = $height;
			$_imageTarget['w'] = round ( $_imageTarget['h'] * ( $_imageInfo["w"] / $_imageInfo["h"] ) );
			$width = $_imageTarget['w'];
		}
	}



	/*
	 * if resample, set cache file name and path
	 */
	if ( is_readable ($_imageInfo['filepath']) && @empty($noresample) )
	{
		// build hash on file modification time
		$_imageInfo['modified'] = filemtime ($_imageInfo['filepath']);
		$_imageInfo['hash']     = md5 ($_imageInfo['file'] . $_imageInfo['modified'] . implode('', $params));


		// destination filename = orig_filename + "_" + w + "x" + h + "_" + hash + "." + ext
		$_imageTarget['filename'] =	pathinfo ( $_imageInfo['filepath'], PATHINFO_FILENAME) . "_" .
									$_imageTarget['w'] . "x" . $_imageTarget['h']      . "_" .
									$_imageInfo['hash'] . "." . $_imagetype[$_imageTarget['imagetype']];


		if ( @empty($cachepath) )
		{
			// path dir
			$_imageTarget['file'] =	dirname ($_imageInfo['filepath']) . $_DS . $_imageTarget['filename'];
		}
		else
		{
			$cachepath = str_replace (' ','\ ', $cachepath);
			if ( substr($cachepath, -1, 1) != "/" ) $cachepath = $cachepath . "/"; // add trailing slash if missing
			$_imageTarget['file'] = $cachepath . $_imageTarget['filename'];
		}

		// if file exist (with same hash it's not modified)
		if ( file_exists ($_imageTarget['file']) )
		{
			// set image path to resampled cached file
			$_imageInfo['path'] = switch_file_in_url ($_imageInfo['path'], $_imageTarget['filename']);

			// and avoid the resample process
			$_resample = false;
		}
		else
		{
			// verify if directory is writable
			if ( is_writable ( dirname ($_imageTarget['file']) ) )
			{
				$_resample = true;
			} else {
				$_resample = false;
			}
		}
	}
	else
	{
		// cannot access file on filesystem so skip caching and resample => switch to simple client side resize
		$_resample = false;
	}



	/*
	 * if $_resample == true; proceed
	 * first create proper image resource
	 */

	if ($_resample)
	{
		// GIF
	    if ($_imageInfo['imagetype'] == 1)
		{
	        $_imageInfo['image']				= imagecreatefromgif    ($_imageInfo['path']);
	        $_imageInfo['gif_colorstotal']		= imagecolorstotal      ($_imageInfo['image']);
	        $_imageInfo['gif_transparent_index']= imagecolortransparent ($_imageInfo['image']);
	
	        // if transparent color in GIF
	        if ( ($_imageInfo['gif_transparent_index'] >= 0) && ($_imageInfo['gif_transparent_index'] < $_imageInfo['gif_colorstotal']) )
			{
				//get the actual transparent color
	            $gif_rgb = imagecolorsforindex ($_imageInfo['image'], $_imageInfo['gif_transparent_index']);
	            $_imageInfo['gif_original_transparency_rgb'] = ($gif_rgb['red'] << 16) | ($gif_rgb['green'] << 8) | $gif_rgb['blue'];
	
	            //change the transparent color to black, since transparent goes to black anyways (no way to remove transparency in GIF)
	            imagecolortransparent ( $_imageInfo['image'], imagecolorallocate($_imageInfo['image'], 0, 0, 0) );
	        }
	    }
	
	
		// JPEG
		if ($_imageInfo['imagetype'] == 2)
		{
			$_imageInfo['image'] = imagecreatefromjpeg ($_imageInfo['path']);
		}
	
	
		// PNG
		if ($_imageInfo['imagetype'] == 3)
		{
			$_imageInfo['image'] = imagecreatefrompng ($_imageInfo['path']);
		}

	
	
	
	    /*
	     * image pre-processing (if the image is very large)
	     * scale linear to 4x target size and overwrite source
	     */
		if ($_imageTarget['w'] * 4 < $_imageInfo['w'] AND $_imageTarget['h'] * 4 < $_imageInfo['h'])
		{
			$_TMP['width']  = round ($_imageTarget['w'] * 4);
			$_TMP['height'] = round ($_imageTarget['h'] * 4);
	        $_TMP['image']  = imagecreatetruecolor ($_TMP['width'], $_TMP['height']);
	
			imagecopyresized ($_TMP['image'], $_imageInfo['image'], 0, 0, $_imageTarget['offsetx'], $_imageTarget['offsety'], $_TMP['width'], $_TMP['height'], $_imageInfo['w'], $_imageInfo['h']);
	
			$_imageInfo['image']  = $_TMP['image'];
			$_imageInfo['w']      = $_TMP['width'];
			$_imageInfo['h']      = $_TMP['height'];
	
			unset ($_TMP['image']);
		}



		// create resampled
		$_imageTarget['image'] = imagecreatetruecolor($_imageTarget['w'], $_imageTarget['h']);
		
		
	
		if ( ($_imageInfo['imagetype'] == 1) && ($_imageInfo['gif_transparent_index'] >= 0) )
		{
			// only for transparent gif:
			imagealphablending ($_imageInfo['image'], false);
			imagesavealpha     ($_imageInfo['image'], true);
			imagecopyresized   ($_imageTarget['image'], $_imageInfo['image'], 0, 0, $_imageTarget['offsetx'], $_imageTarget['offsety'], $_imageTarget['w'], $_imageTarget['h'], $_imageInfo['w'], $_imageInfo['h']);
		} else {
			imagecopyresampled ($_imageTarget['image'], $_imageInfo['image'], 0, 0, $_imageTarget['offsetx'], $_imageTarget['offsety'], $_imageTarget['w'], $_imageTarget['h'], $_imageInfo['w'], $_imageInfo['h']);
			if ( !function_exists('UnsharpMask') )
			{
				$_imageTarget['image'] = UnsharpMask($_imageTarget['image'], 80, .5, 3);
			}
		}



		/*
		 * store file (or reuse)
		 */
	
		// GIF
		if ($_imageTarget['imagetype'] == 1)
		{
	        // rebuild transparency (if there was transparency)
	        if ($_imageInfo['gif_transparent_index'] >= 0)
			{
	            imagealphablending ($_imageTarget['image'], false);
	            imagesavealpha     ($_imageTarget['image'], true);
	
	            for ($x = 0; $x < $_imageTarget['w']; $x++)
				{
	                for ($y = 0; $y < $_imageTarget['h']; $y++)
					{
	                    if (imagecolorat ($_imageTarget['image'], $x, $y) == $_imageInfo['gif_original_transparency_rgb'])
						{
	                        $merkx = $x;
	                        $merky = $y;
	                        $x = $_imageTarget['w'];
	                        $y = $_imageTarget['h'];
	                    }
	                }
	            }
	        }
	
	        imagetruecolortopalette ($_imageTarget['image'], false, $_imageInfo['gif_colorstotal']);
	
	        if ($_imageInfo['gif_transparent_index'] >= 0)
			{
	            $id = imagecolorat ($_imageTarget['image'], $merkx, $merky);
	            if ($id >= 0)
				{
	                imagecolortransparent ($_imageTarget['image'], $id);
	            }
	        }
	
			imagegif($_imageTarget['image'], $_imageTarget['file']);
	    }
	
	
		// JPEG
	    if ($_imageTarget['imagetype'] == 2)
		{
			imageinterlace ($_imageTarget['image'], 1);
			if ( empty($params['quality']) )
			{
				$params['quality'] = 80;
			}
			imagejpeg ($_imageTarget['image'], $_imageTarget['file'], $params['quality']);
	    }
	
	
	
		// PNG
		if ($_imageTarget['imagetype'] == 3)
		{
			imagepng($_imageTarget['image'], $_imageTarget['file']);
	    }



		// free resources
		imagedestroy ($_imageTarget['image']);
		imagedestroy ($_imageInfo['image']);
	

		// set image path to resampled cached file
		$_imageInfo['path'] = switch_file_in_url ($_imageInfo['path'], $_imageTarget['filename']);

	} // end if ( $_resample )



	/*
	 * if $imageonly, return image
	 */
	if ( !@empty($imageonly) )
	{
		// assign to defined var or return
		if ( empty($var) )
		{
			return $_imageInfo['path'];
		}
		else
		{
			return $smarty -> assign ( $var, $_imageInfo['path'] );
		}
	}



	/*
	 * frame style
	 */
	$_framestyle = "position: relative; overflow: hidden; width: " . $width . "px; height: " . $height . "px; ";

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
		$_framestyle .= " text-align: " . $imagealign . "; ";
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
	$_framestyle = 'style ="' . $_framestyle . '"';
	



	/*
	 * finally build up HTML code
	 */
	$_html  = "<div " . $_framestyle . ">" . "\n";
	$_html .= '<img src="' . $_imageInfo['path'] . '" alt="' . $alt . '" title="' . $title . '" width="' . $_imageTarget['w'] . '" height="' . $_imageTarget['h'] . '" />' . "\n";
	if ( !empty($caption) )
	{
		$_html .= '<div style="position: absolute; bottom: -15px; left: 1px; ' . $_captionstyle . '">' . $caption . '</div>' . "\n";
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

// unsharp mask [taken from thumb_imp by Christoph Erdmann (CE), Wolfgang Krane (WK)]
	if (!function_exists('UnsharpMask')) {
		function UnsharpMask($img, $amount, $radius, $threshold) {
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
        }
    }



function switch_file_in_url ($url, $newfile)
{
	$_parsed = parse_url ($url);
	$_parsedplus = parseURLplus ($url);

    if ( !is_array($_parsed) ) return false;

    $uri =  isset ($_parsed['scheme']) ? $_parsed['scheme'] . ':' . ( ( strtolower ($_parsed['scheme']) == 'mailto' ) ? '' : '//' ) : '';
    $uri .= isset ($_parsed['user']) ?   $_parsed['user'] . ( isset($_parsed['pass']) ? ':'.$_parsed['pass'] : '') . '@' : '';
    $uri .= isset ($_parsed['host']) ?   $_parsed['host'] : '';
    $uri .= isset ($_parsed['port']) ?   ':'.$_parsed['port'] : '';

/*  old behaviour
    if ( isset ($parsed['path']) )
    {
        $uri .= (substr($parsed['path'], 0, 1) == '/') ? $parsed['path'] : ('/' . $parsed['path']);
    }
*/
	$uri .= $_parsedplus['dir'] . "/" . $newfile;
	
    $uri .= isset ($parsed['query']) ? '?'.$parsed['query'] : '';
    $uri .= isset ($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

    return $uri;
}


function parseURLplus ($url)
{
	$URLpcs = parse_url ($url);
	$PathPcs = explode ("/", $URLpcs['path']);
	$URLpcs['file'] = end ($PathPcs);
	unset ($PathPcs[key($PathPcs)]);
	$URLpcs['dir'] = implode ("/", $PathPcs);
	return ($URLpcs);
}


// case insensitive array search
function array_isearch ($str, $array)
{
	foreach ($array as $k => $v)
	{
		if (strcasecmp ($str, $v) == 0) return $k;
	}
	return false;
}

?>
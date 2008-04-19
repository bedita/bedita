<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     image_info
 * Version:  1.0
 * Author:   Christiano Presutti - aka xho - ChanelWeb srl
 * Purpose:  get several information from an image file
 * Input:    fileURI = string pointer to file - (yet to be made access file path on filesystem), var = name of the array that will store gathered informations
 * Returns:  filename, w, h, hrtype, attr, mimetype, bits, channels, orientation [ todo: , exif as array () ]
 * -------------------------------------------------------------
 */
function smarty_function_image_info ($params, &$smarty)
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
		"orientation"	=> "",
		"portrait"	=> "",
		"landscape"	=> "",
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


	/*
	 *  File is on remote server, local server or local FileSystem (to do local filesystem recognition)
	 */
    if ( substr($file, 0, 6) == 'http:' || substr($file, 0, 6) == 'https:' )
	{
		$_image_path_type		= "remote";
    } else {
		$_image_path_type		= "local";
    }
	/* FS to do
	{
		$_image_path_type		= "filesystem";
		$_image_path			= rawurlencode( $file ); ?? right ??
		$imageInfo ['filename']	= basename($path);
	}
	*/


	// sanitize file path & name (why does not work?)
	$_image_path			= str_replace(' ','%20', $file); ; // rawurlencode( $file );


	/*
	 *  Get data or trigger errors
	 */	
	if ( !$_image_data = @getimagesize($_image_path) )
	{
		if ( !file_exists($_image_path) )
		{
			$smarty -> trigger_error ( $pluginName . ": unable to find '$_image_path'", E_USER_NOTICE ) ;
			return;
		}
		else if ( !is_readable($_image_path) )
		{
			$smarty -> trigger_error ( $pluginName . ": unable to read '$_image_path'", E_USER_NOTICE ) ;
			return;
		}
		else
		{
			$smarty -> trigger_error ( $pluginName . ": '$_image_path' is not a valid image file", E_USER_NOTICE ) ;
			return;
		}
	}


	/*
	 * EXIF build up
	 */
	if ( _getHumanReadableType ( $_image_data[2] ) != "JPG" )
	{
		$imageInfo["exif"] = false;
	}
	else
	{
		if ( ! $exifRawData	= _raw_extract_exif ( $_image_path ) )
		{
			$exifRawData['main'] = false;
		}

		if ( ! $exifRawData ['XMP'] = _ee_extract_exif_from_pscs_xmp ( $_image_path ) )
		{
			$exifRawData ['XMP'] = false;
		}

		$imageInfo["exif"]		= $exifRawData;
	}

		



	/*
	 * build up array
	 */
	$_path					= parse_url ( $_image_path, PHP_URL_PATH );
	$imageInfo['filename']	= end ( explode ( '/', $_path ) );
	$imageInfo["filesize"]	= ( $_image_path_type == "filesystem" )? _getHumanReadableFileSize ( $_image_path ) : '';
	$imageInfo["w"]			= $_image_data [0];
	$imageInfo["h"]			= $_image_data [1];
	$imageInfo["hrtype"]	= _getHumanReadableType ( ( $_image_data[2] ) );
	$imageInfo["attr"]		= $_image_data [3];
	$imageInfo["mimetype"]	= (!empty($_image_data ['mime']))?$_image_data ['mime']:'';
	$imageInfo["bits"]		= (!empty($_image_data ['bits']))?$_image_data ['bits']:'';
	$imageInfo["channels"]	= (!empty($_image_data ['channels']))?$_image_data ['channels']:'';
	if ($imageInfo["w"] > $imageInfo["h"])
	{
		$imageInfo["orientation"]	= "landscape";
		$imageInfo["landscape"]		= true;
		$imageInfo["portrait"]		= false;
	}
	else
	{
		$imageInfo["orientation"]	= "portrait";
		$imageInfo["landscape"]		= false;
		$imageInfo["portrait"]		= true;
	}
		


	// assign to defined var
 	$smarty -> assign ( $var, $imageInfo );

}




/*
 * helper functions
 */

// system supported image types
function _getSupportedImageTypes ()
{
    $aSupportedTypes = array();

    $aPossibleImageTypeBits = array (
        IMG_GIF		=> 'GIF',
        IMG_JPG		=> 'JPG',
        IMG_PNG		=> 'PNG',
        IMG_WBMP	=> 'WBMP'
    );

    foreach ( $aPossibleImageTypeBits as $iImageTypeBits => $sImageTypeString )
	{
        if (imagetypes() & $iImageTypeBits)
		{
            $aSupportedTypes[] = $sImageTypeString;
        }
    }

    return $aSupportedTypes;
}


// file size
function _getHumanReadableFileSize ( $file )
{
	return $file_size = array_reduce (
		array (" B", " KB", " MB"), create_function (
			'$a, $b', 'return is_numeric ($a)? ($a >= 1024 ? $a / 1024 : number_format ($a, 2) . $b) : $a;'
		), filesize ( $file )
	);
}


// function _getHumanReadableType
function _getHumanReadableType ( $type )
{
    $types = array (
		0 => '',
        1 => 'GIF',
        2 => 'JPG',
        3 => 'PNG',
        4 => 'SWF',
        5 => 'PSD',
        6 => 'BMP',
        7 => 'TIFF (intel byte order)',
        8 => 'TIFF (motorola byte order)',
        9 => 'JPC',
        10 => 'JP2',
        11 => 'JPX',
        12 => 'JB2',
        13 => 'SWC',
        14 => 'IFF',
        15 => 'WBMP',
        16 => 'XBM'
    );
	
	return $types [ $type ] ;	
}



/*
 * EXIF funcs
 */


// raw exif read through PHP exif_read_data
function _raw_extract_exif ( $file )
{
	if(!function_exists('exif_read_data')) {
		// $smarty->trigger_error($pluginName . ": function 'exif_read_data' is missing in your PHP setup");
		return false ;
	}
	$exif = exif_read_data( $file, 'IFD0');
	
	if ( $exif === false ) return false; // "No header data found in image file.";

	$exif = exif_read_data( $file, 0, true);

	$exif["main"] = @ array (
		"FileName"				=> $exif ['FILE']['FileName'],
		"FileSize"				=> $exif ['FILE']['FileSize'],
		"MimeType"				=> $exif ['FILE']['MimeType'],
		"Orientation"			=> ($exif ['IFD0']['Orientation'])? "landscape" : "portrait",
		"Color"					=> ($exif ['COMPUTED']['IsColor'])? "color" : "b/n",
		"Copyright"				=> $exif ['COMPUTED']['Copyright'],
		"Copyright (IFD0)"		=> $exif ['IFD0']['Copyright'],
		"ShotDate"				=> $exif ['IFD0']['DateTime'],
		"CaptureDeviceBrand"	=> $exif ['IFD0']['Make'],
		"CaptureDeviceModel"	=> $exif ['IFD0']['Model'],
		"CCDWidth"				=> $exif ['COMPUTED']['CCDWidth'],
		"ApertureFNumber"		=> $exif ['COMPUTED']['ApertureFNumber'],
		"ExposureTime"			=> $exif ['EXIF']['ExposureTime'],
		"ShutterSpeed"			=> $exif ['EXIF']['ShutterSpeedValue'],
		"Aperture"				=> $exif ['EXIF']['ApertureValue'],
		"FNumber"				=> $exif ['EXIF']['FNumber'],
		"ISO"					=> $exif ['EXIF']['ISOSpeedRatings'],
		"FocalLength"			=> $exif ['EXIF']['FocalLength'],
		"Width"					=> $exif ['EXIF']['ExifImageWidth'],
		"Height"				=> $exif ['EXIF']['ExifImageLength'],
	);
	
	return $exif;

	/*
	 * printout
	 * 
	echo "exif:<br />\n";
	foreach ($exif as $key => $section) {
		foreach ($section as $name => $val) {
			echo "$key.$name: $val<br />\n";
		}
	}
	*/
}


// EXIF from Photoshop CS XMP
// example: $xmp_parsed = ee_extract_exif_from_pscs_xmp ("CRW_0016b_preview.jpg");
function _ee_extract_exif_from_pscs_xmp ( $file ) {

	// very straightforward one-purpose utility function which
	// reads image data and gets some EXIF data (a few) out from its XMP tags (by Adobe Photoshop CS)
	// returns an array with values
	// inspired by code by Pekka Saarinen http://photography-on-the.net

	ob_start();
	readfile( $file );
	$source = ob_get_contents();
	ob_end_clean();

	$xmpdata_start = strpos($source,"<x:xmpmeta");
	$xmpdata_end = strpos($source,"</x:xmpmeta>");
	$xmplenght = $xmpdata_end - $xmpdata_start;
	
	if ( empty( $xmplenght ) ) return false;
	
	$xmpdata = substr($source, $xmpdata_start, $xmplenght+12);
	$xmp_parsed = array();

	$regexps = array(
		array ("name" => "DC creator", "regexp" => "/<dc:creator>\s*<rdf:Seq>\s*<rdf:li>.+<\/rdf:li>\s*<\/rdf:Seq>\s*<\/dc:creator>/"),
		array ("name" => "TIFF camera model", "regexp" => "/<tiff:Model>.+<\/tiff:Model>/"),
		array ("name" => "TIFF maker", "regexp" => "/<tiff:Make>.+<\/tiff:Make>/"),
		array ("name" => "EXIF exposure time", "regexp" => "/<exif:ExposureTime>.+<\/exif:ExposureTime>/"),
		array ("name" => "EXIF f number", "regexp" => "/<exif:FNumber>.+<\/exif:FNumber>/"),
		array ("name" => "EXIF aperture value", "regexp" => "/<exif:ApertureValue>.+<\/exif:ApertureValue>/"),
		array ("name" => "EXIF exposure program", "regexp" => "/<exif:ExposureProgram>.+<\/exif:ExposureProgram>/"),
		array ("name" => "EXIF iso speed ratings", "regexp" => "/<exif:ISOSpeedRatings>\s*<rdf:Seq>\s*<rdf:li>.+<\/rdf:li>\s*<\/rdf:Seq>\s*<\/exif:ISOSpeedRatings>/"),
		array ("name" => "EXIF datetime original", "regexp" => "/<exif:DateTimeOriginal>.+<\/exif:DateTimeOriginal>/"),
		array ("name" => "EXIF exposure bias value", "regexp" => "/<exif:ExposureBiasValue>.+<\/exif:ExposureBiasValue>/"),
		array ("name" => "EXIF metering mode", "regexp" => "/<exif:MeteringMode>.+<\/exif:MeteringMode>/"),
		array ("name" => "EXIF focal lenght", "regexp" => "/<exif:FocalLength>.+<\/exif:FocalLength>/"),
		array ("name" => "AUX lens", "regexp" => "/<aux:Lens>.+<\/aux:Lens>/")
	);

    foreach ( $regexps as $key => $k )
	{
		$name	= $k["name"];
		$regexp	= $k["regexp"];
		unset ($r);
		preg_match ($regexp, $xmpdata, $r);
		$xmp_item = "";
		$xmp_item = @trim ( strip_tags( $r[0] ) );
		array_push ( $xmp_parsed, array ("item" => $name, "value" => $xmp_item ) );
	}

	/*
	 * printout
	 * 
		echo "XML lenght: " . $xmplenght; exit;

		foreach ($xmp_parsed as $key => $k)
		{
			$item	= $k["item"];
			$value	= $k["value"];
			print "<br><b>" . $item . ":</b> " . $value;
		}
		exit;
	 */

	return ($xmp_parsed);

}


?>

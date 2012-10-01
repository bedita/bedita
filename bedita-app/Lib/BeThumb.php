<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2012 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

/**
 * thumbnail helper class
 *
 *
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */

class BeThumb {


	// supported image types (order is important)
	private $_imagetype = array ("", "gif", "jpg", "png", "jpeg");
	private $_defaultimagetype = 2; // defaults to 2 [= JPG]

	private $_mimeType = array("image/gif", "image/jpeg", "image/pjpeg", "image/png");

	// empties (see private method _resetObjects)
	private $_resample    = false;
	private $_conf        = array ();
	private $_imageInfo   = array ();
	private $_imageTarget = array ();

	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	// var $helpers = array('Html');

	function __construct() {
		// get configuration parameters and defaults
		$this->_conf = Configure::read('media') ;
		$this->_conf['root']  = Configure::read('mediaRoot');
		$this->_conf['url']   = Configure::read('mediaUrl');
		$this->_conf['tmp']   = Configure::read('tmp');
		$this->_conf['imgMissingFile'] = Configure::read('imgMissingFile');
		if (defined("BACKEND_APP") && !BACKEND_APP) {
			if (!file_exists(WWW_ROOT . $this->_conf['imgMissingFile'])) {
				$this->_conf['imgMissingFile'] = Configure::read('beditaUrl') . $this->_conf['imgMissingFile'];
			}
		}
	}

	public function getValidImplementations() {
		App::import ('Vendor', 'phpthumb', array ('file' => 'php_thumb' . DS . 'ThumbLib.inc.php') );
		return PhpThumbFactory::getValidImplementations();
	}


	/**
	 * image public method: embed an image after resample and cache
	 *
	 * @param: array $be_obj, required, object, BEdita Multimedia Object
	 * @param: array $params
	 * extra info about $params:
	 *
	 *         width, height, longside, optional: [integer]
	 *         if no parameters is set, use default width & height in bedita.ini.php (['image']['thumbWidth'], ['image']['thumbHeight'])
	 *         if longside is set, width & height are ignored and mode is forced to resize.
	 *         if only width or only height is specified, the mode and modeparam are ignored and forced to 'resize'.
	 *
	 *         mode, optional: [crop, croponly, resize, 'fill', 'stretch']
	 *         if not specified default bedita.ini.php (['image']['thumbMode']) is used (but always overrided by the rules above).
	 *         'fill' is legacy options: it will set mode always to 'resize' but modeparam to 'fill' only if longside is not set.
	 *         'stretch' is legacy options: it will set mode always to 'resize' and modeparam to 'stretch'.
	 *
	 *         modeparam, optional: [fill, stretch, 'C', 'T', 'B', 'L', 'R', 'TL', 'TR', 'BL', 'BR']
	 *         depends on mode:
	 *             if resize: 'fill' or 'stretch' and if left empty do simple resize.
	 *             if crop, a string describing crop zone 'C', 'T', 'B', 'L', 'R', 'TL', 'TR', 'BL', 'BR' (default: ['image']['thumbCrop'] )
	 *
	 *         cache, optional: [true, false]
	 *         allow the caching of images, default in bedita.ini (['image']['cache'] )
	 *
	 *         bgcolor, optional: [string]
	 *         string representing hex color, i.e. 'FFFFFF' for the background (only on mode=resize and modeparam=fill)
	 *         default in bedita.ini (['image']['background'] )
	 *
	 *         upscale, optional: [true, false]
	 *         allow or not upscale. default bedita.ini.php (['image']['thumbUpscale'])
	 *
	 *         type, optional: ['gif'/'png'/'jpg']
	 *         force image target type (default the same as original)
	 *
	 *         watermark, optional: watermark?
	 *
	 *         NB: optionally the second argument may be the associative array of said parameters
	 *
	 * @return: string, resampled and cached image URI (using $html helper)
	 *
	 */
	public function image ($be_obj, $params = null) {
		// defaults?
		// $width = false, $height = false, $longside = null, $mode = null, $modeparam = null, $type = null, $upscale = null, $cache = true
		// this method is for image only, check bedita object type
		if ( strpos($be_obj['mime_type'], "image") === false ) {
			$this->_triggerError("'" . $be_obj['name'] . "' is not a valid image object (object type is " . $be_obj['mime_type'] . ")");
			return $this->_conf['imgMissingFile'];
		} elseif (!in_array($be_obj["mime_type"], $this->_mimeType)) {
			return false;
		} else {
			$this->_resetObjects();
		}

		// read params as an associative array or multiple variable
		$expectedArgs = array ('width', 'height', 'longside', 'mode', 'modeparam', 'type', 'upscale', 'cache', "watermark");
		if ( func_num_args() == 2 && is_array( func_get_arg(1) ) ) {
			extract ($params);
		}

		// filepath & name
		$this->_imageInfo['path']		= $be_obj['uri'];
		$this->_imageInfo['filename']	= $be_obj['name'];
		$this->_imageInfo['ext']		= end ( explode ( '.', $this->_imageInfo['filename'] ) );
		$this->_imageInfo['filepath']	= $this->_conf['root'] . $this->_imageInfo['path'];  // absolute
		if (DS != "/") {
			$this->_imageInfo['filepath'] = str_replace("/", DS, $this->_imageInfo['filepath']);
		}
		$this->_imageInfo['filenameBase'] = pathinfo($this->_imageInfo['filepath'], PATHINFO_FILENAME);
		$this->_imageInfo['filenameMD5'] = md5($this->_imageInfo['filename']);
		$this->_imageInfo['cacheDirectory'] = dirname($this->_imageInfo['filepath']) . DS .
											  substr($this->_imageInfo['filenameBase'],0,5) . "_" .
											  $this->_imageInfo['filenameMD5'];

		// test source file
		if (!$this->_testForSource()) {
			return $this->_conf['imgMissingFile'];
		}

		//Setting params:
		//cache
		if (isset($cache))	{
			$this->_imageTarget['cacheImages'] = $cache;
		} else {
			$this->_imageTarget['cacheImages'] = $this->_conf['image']['cache'];
		}

		// upscale
		if (isset($upscale))	{
			$this->_imageTarget['upscale'] = $upscale;
		} else {
			$this->_imageTarget['upscale'] = $this->_conf['image']['thumbUpscale'];
		}

		//background
		if ( isset ($bgcolor) ) {
			$this->_imageTarget['fillcolor'] = $bgcolor;
		} else {
			$this->_imageTarget['fillcolor'] = $this->_conf['image']['background'];
		}

		// cropmode
		$this->_imageTarget['cropmode'] = $this->_conf['image']['thumbCrop'];

		// default mode, if not specified
		if ( !isset ($mode) ) {
			$mode = $this->_conf['image']['thumbMode'];
		}



		// build _image_info with getimagesize() or available parameters
		if ( empty($be_obj['width']) || empty($be_obj['height']) ) {

			if ( !$_image_data =@ getimagesize($this->_imageInfo['filepath']) ) {
				$this->_triggerError("'" . $this->_imageInfo['path'] . "' is not a valid image file");
				return $this->_conf['imgMissingFile'];
			}

			// set up the rest of image info array
			$this->_imageInfo["w"]		= $_image_data [0];
			$this->_imageInfo["h"]		= $_image_data [1];
			$this->_imageInfo['type']	= $this->_imagetype[$_image_data [2]]; // 1=GIF, 2=JPG, 3=PNG
			unset ($_image_data);
		} else {

			$this->_imageInfo["w"] = $be_obj['width'];
			$this->_imageInfo["h"] = $be_obj['height'];

			// since not using getimagesize(), try to get image type from object or extension
			if ( !($this->_imageInfo['ntype'] =@ $this->_array_isearch ( substr (strrchr ($be_obj['mime_type'], "/"), 1), $this->_imageInfo['ext'] ) ) ) {

                if ( !( $this->_imageInfo['ntype'] =@ $this->_array_isearch ($this->_imageInfo['ext'], $this->_imagetype) ) ) {
					$this->_imageInfo['ntype'] = $this->_defaultimagetype; // defaults to 2 [= JPG]
				}

			}

			if ($this->_imageInfo['ntype'] == 4) {
				$this->_imageInfo['ntype'] = 2; // JPEG == JPG
			}
			// set string type
			$this->_imageInfo['type'] = $this->_imagetype[ $this->_imageInfo['ntype'] ];
		}


		// target image type
		if ( !@empty($type) ) {
			$this->_imageTarget['type'] = $type;
		} else {
			$this->_imageTarget['type'] = $this->_imageInfo['type'];
		}

		// Target image size: _imageTarget['w'] & _imageTarget['h']
		// [unused $this->_targetSize ($width, $height)]
		if ( empty($width) && empty($height) && !isset($longside) ) { //no parameter set, use default
			$width  = $this->_conf['image']['thumbWidth'];
			$height = $this->_conf['image']['thumbHeight'];

		}else if ( !empty($longside) && is_numeric($longside) ) { // if is set longside, ignore the others parameters
			if ($this->_imageInfo["w"] > $this->_imageInfo["h"]) {
				$width  = $longside;
				$height = 0;
			}else {
				$width  = 0;
				$height = $longside;
			}
			//forcing the mode to "resize" if longside
			$mode = "resize";
			$modeparam = null;
		}else if ( empty($width) || empty($height) ) { // case with only one parameter w/h

			if ( empty($width) ){
				$width  = 0;
			}
			if ( empty($height) ){
				$height = 0;
			}
			//forcing the mode to "resize" if one coordinate is empty
			$mode = "resize";
			$modeparam = null;
		}

		$this->_imageTarget['w'] = $width;
		$this->_imageTarget['h'] = $height;

		//Set the embed mode
		switch ($mode) {
			case "croponly":  //general crop
				$this->_imageTarget['mode'] = 0;
				if ( isset ($modeparam) ) {
					$this->_imageTarget['cropmode'] = $modeparam; // overwrite crop mode
				}
				break;

			case "crop": //adaptive crop
				$this->_imageTarget['mode'] = 1;
				//if ( isset ($modeparam) )	$this->_imageTarget['cropmode'] = $modeparam; // overwrite crop mode
				break;

			case "resize":
			default:
				$this->_imageTarget['mode'] = 2;
				if ( isset ($modeparam) ) {
					$this->_imageTarget['resizetype'] = $modeparam;
				}

				break;

			// legacy methods
			case "fill":
				$this->_imageTarget['mode'] = 2;

				if (empty($longside)) {
					$this->_imageTarget['resizetype'] = 'fill';
					if ( isset ($modeparam) )	{
						$this->_imageTarget['fillcolor'] = $modeparam;
					}else {
						$this->_imageTarget['fillcolor'] = $this->_conf['image']['thumbFill'];
					}
				}
				break;

			case "stretch":
				$this->_imageTarget['mode'] = 2;
				$this->_imageTarget['resizetype'] = 'stretch';
				break;
		}



		// target filename, filepath, uri
		$this->_imageTarget['filename'] = $this->_targetFileName ();
		$this->_imageTarget['filepath'] = $this->_targetFilePath ();
		$this->_imageTarget['uri']      = $this->_targetUri ();

		// Manage cache and resample if caching option is true
		// and the image it's not alredy cached
		$this->_imageTarget['cached']   = $this->_testForCache ();

		if ( !$this->_imageTarget['cached'] || (!$this->_imageTarget['cacheImages']) ) {
			if ( !$this->_resample() ) {
				return $this->_conf['imgMissingFile'];
			}
		}
		else {
			// skip resample, it's cached
		}

		// return HTML <img> tag
		return $this->_imageTarget['uri'];
	}

	/**************************************************************************
	** private methods follow
	**************************************************************************/

	/**
	 * resample image using PhpThumb
	 *
	 * @return boolean
	 */
	private function _resample () {

		App::import ('Vendor', 'phpthumb', array ('file' => 'php_thumb' . DS . 'ThumbLib.inc.php') );
		$thumbnail = PhpThumbFactory::create($this->_imageInfo['filepath'], $this->_conf['image']);

		$thumbnail->setDestination ( $this->_imageTarget['filepath'], $this->_imageTarget['type'] );

		//set upscale
		if ($this->_imageTarget['upscale']) {
			$thumbnail->setOptions(array("resizeUp" => true));
		}

		// more params about resample mode
		switch ( $this->_imageTarget['mode'] ) {
			// croponly
			case 0:
				list ($starX, $startY)  = $this->_getCropCoordinates ( $this->_imageInfo['w'], $this->_imageInfo['h'],$this->_imageTarget['w'], $this->_imageTarget['h'], $this->_imageTarget['cropmode'] );
				$thumbnail->crop($starX, $startY, $this->_imageTarget['w'], $this->_imageTarget['h']);

				break;
			//crop: adaptive crop
			case 1:
			default:
				$thumbnail->adaptiveResize($this->_imageTarget['w'], $this->_imageTarget['h']);
				break;

			// resize
			case 2:

				//stretch or fill of simple resize
				if (empty($this->_imageTarget['resizetype'])){

					$thumbnail->resize($this->_imageTarget['w'], $this->_imageTarget['h']);

				}else if ($this->_imageTarget['resizetype'] == 'stretch') {

					$thumbnail->resizeStretch($this->_imageTarget['w'], $this->_imageTarget['h']);

				} else if ($this->_imageTarget['resizetype'] == 'fill') {

					$thumbnail->resizeFill($this->_imageTarget['w'], $this->_imageTarget['h'],  $this->_imageTarget['fillcolor']);
				}
				break;

		}

		if ($thumbnail->save($this->_imageTarget['filepath'], $this->_imageTarget['type'])) {
			return true;
		} else {
			$this->_triggerError("phpThumb error");
			return $this->_conf['imgMissingFile'];
		}

	}
	// end _resample

	/**
	 * test source file for existance and correctness
	 *
	 * @return boolean
	 */
	private function _testForSource() {
		if ( !file_exists($this->_imageInfo['filepath']) ) {
			// file does not exist
			$this->_triggerError("file '" . $this->_imageInfo['filepath'] . "' does not exist");
			return false;
		}
		elseif ( !is_readable ($this->_imageInfo['filepath']) ) {
			// cannot access source file on filesystem
			$this->_triggerError("cannot read file '" . $this->_imageInfo['filepath'] . "' on filesystem");
			return false;
		}
		else return true;
	}

	/**
	 * build target filename
	 *
	 * @return string
	 */
	private function _targetFileName () {
		// build hash on file path, modification time and mode
		$this->_imageInfo['modified'] = filemtime ($this->_imageInfo['filepath']);
		$this->_imageInfo['hash']     = md5 ( $this->_imageInfo['filename'] . $this->_imageInfo['modified'] . join($this->_imageTarget) );

		// destination filename = orig_filename + "_" + w + "x" + h + "_" + hash + "." + ext
		return $this->_imageInfo['filenameBase'] . "_" .
							$this->_imageTarget['w'] . "x" . $this->_imageTarget['h'] . "_" .
							$this->_imageInfo['hash'] . "." . $this->_imageTarget['type'];
	}

	/**
	 * build target filepath
	 *
	 * @return string
	 */
	private function _targetFilePath () {
		// cached file is in the same folder as original
		if ( $this->_imageTarget['filename']) {
			if (!file_exists($this->_imageInfo['cacheDirectory'])) {
				if (!mkdir($this->_imageInfo['cacheDirectory'])) {
					return false;
				}
			}
			elseif (!is_dir($this->_imageInfo['cacheDirectory'])) {
				return false;
			}

			return $this->_imageInfo['cacheDirectory'] . DS . $this->_imageTarget['filename'];
		}
		else {
			return false;
		}
	}

	/**
	 * build target uri
	 *
	 * @return string
	 */
	private function _targetUri () {
		// set target image uri to resampled cached file (also urlencode filename here)
		return $this->_conf['url'] . $this->_change_file_in_url ($this->_imageInfo['path'], rawurlencode($this->_imageTarget['filename']));
	}

	/**
	 * verify existence of cached file, build and set target filename and filepath
	 *
	 * @return boolean
	 */
	private function _testForCache () {
		// if file exist (with same hash it's not been modified)
		if ( file_exists ($this->_imageTarget['filepath']) ) {
			return true;
		}
		else return false;
	}

	/**
	 * calculate cropping coordinates (top, left)
	 *
	 * @param int $origW, original width
	 * @param int $origH, original height
	 * @param int $targetW, target object width
	 * @param int $targetH, target object height
	 * @param string $position: TL (top left), T (top), TR (top right), L (left), R (right), BL (bottom left), B (bottom), BR (bottom right), C (center), default center
	 * @return array
	 */
	private function _getCropCoordinates ( $origW, $origH, $targetW, $targetH, $position ) {
		$coordinates = array ();

		switch ($position) {
			case "TL":
				$coordinates['x']  = 0;
				$coordinates['y']  = 0;
				break;

			case "T":
				$coordinates['x']  = ($origW / 2) - ($targetW / 2);
				$coordinates['y']  = 0;
				break;

			case "TR":
				$coordinates['x']  = $origW - $targetW;
				$coordinates['y']  = 0;
				break;

			case "L":
				$coordinates['x']  = 0;
				$coordinates['y']  = ($origH / 2) - ($targetH / 2);
				break;

			case "R":
				$coordinates['x']  = $origW - $targetW;
				$coordinates['y']  = ($origH / 2) - ($targetH / 2);
				break;

			case "BL":
				$coordinates['x']  = 0;
				$coordinates['y']  = $origH - $targetH;
				break;

			case "B":
				$coordinates['x']  = ($origW / 2) - ($targetW / 2);
				$coordinates['y']  = $origH - $targetH;
				break;

			case "BR":
				$coordinates['x']  = $origW - $targetW;
				$coordinates['y']  = $origH - $targetH;
				break;

			case "C":
			case 1:
			default:
				$coordinates['x']  = ($origW / 2) - ($targetW / 2);
				$coordinates['y']  = ($origH / 2) - ($targetH / 2);
				break;
		}

		return array ($coordinates['x'], $coordinates['y']);
	}

	/**
	 * reset internal objects to empty defaults
	 */
	private function _resetObjects() {
		$this->_imageInfo = array (
									"filename"		=> "",
									"path"			=> "", // path without file
									"filepath"		=> "", // file + path
									"ext"			=> "",
									"filesize"		=> "",
									"w"				=> "",
									"h"				=> "",
									"orientation"	=> "",
									"type"			=> "",
									"ntype"			=> 0,
									"modified"		=> false,
									"hash"			=> ""
								);

		$this->_imageTarget = array (
									"filename"		=> "",
									"filepath"		=> "",
									"uri"			=> "",
									"w"				=> "",
									"h"				=> "",
									"offsetx"		=> 0,
									"offsety"		=> 0,
									"type"			=> "",
									"mode"			=> "",
									"fillcolor"		=> "",
									"cropmode"		=> "",
									"upscale"		=> false,
									"cached"		=> false
								);

		$this->_resample = false;
	}

	/**
	 * error reporting
	 *
	 * @param string $errorMsg
	 */
	private function _triggerError($errorMsg) {
		CakeLog::write('error', get_class($this) . ": " . $errorMsg);
		return;
	}

	/***************************/
	/* minor private functions */
	/***************************/

	/**
	 * substitute file part only in a given url
	 *
	 * @param string $url
	 * @param string $newfile
	 * @return string uri
	 */
	private function _change_file_in_url ($url, $newfile) {
		$_parsed = parse_url ($url);
		$_parsedplus = $this->_parseURLplus ($url);

	    if ( !is_array($_parsed) ) return false;

	    $uri =  isset ($_parsed['scheme']) ? $_parsed['scheme'] . ':' . ( ( strtolower ($_parsed['scheme']) == 'mailto' ) ? '' : '//' ) : '';
	    $uri .= isset ($_parsed['user']) ?   $_parsed['user'] . ( isset($_parsed['pass']) ? ':'.$_parsed['pass'] : '') . '@'            : '';
	    $uri .= isset ($_parsed['host']) ?   $_parsed['host']       : '';
	    $uri .= isset ($_parsed['port']) ?   ':' . $_parsed['port'] : '';

		$uri .= $_parsedplus['dir'] . "/" . $newfile;

	    $uri .= isset ($parsed['query']) ? '?'.$parsed['query'] : '';
	    $uri .= isset ($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

	    return $uri;
	}

	//
	/**
	 * improved version of parse_url (returns also 'file' and 'dir')
	 *
	 * @param string $url
	 * @return array
	 */
	private function _parseURLplus ($url) {
		$URLpcs  = parse_url ($url);
		$PathPcs = explode ("/", $URLpcs['path']);
		$URLpcs['file'] = end ($PathPcs);
		unset ($PathPcs[key($PathPcs)]);
		$URLpcs['dir'] = implode ("/", $PathPcs);
		if (file_exists($this->_imageInfo['cacheDirectory']) && is_dir($this->_imageInfo['cacheDirectory']))
		{
			$URLpcs['dir'] .= "/" . substr($this->_imageInfo['filenameBase'],0,5) . "_" . $this->_imageInfo['filenameMD5'];;
		}
		return ($URLpcs);
	}

	/**
	 * case insensitive array search
	 *
	 * @param string $str
	 * @param array $array
	 */
	private function _array_isearch ($str, $array) {
		foreach ($array as $k => $v) {
			if (strcasecmp ($str, $v) == 0) return $k;
		}
		return false;
	}

}

?>
<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeEmbedMediaHelper extends AppHelper {

	private $_helpername = "BeEmbedMedia Helper";

	// private
	private $_objects = array ("image", "audio", "video", "flash"); // supported
  //  private $_output  = false;
	private $_conf    = array ();

	private $defaultPresentation = array(
		"Image" => "thumb",
		"Video" => "full",
		"Audio" => "full",
		"Application" => "full",
		"BEFile" => "link"
	);

	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html', 'BeThumb', 'MediaProvider', 'BeEmbedFlash');
	
	
	function __construct()
	{
		// get configuration parameters
		$this->_conf = Configure::read('media') ;
		$this->_conf['root']  = Configure::read('mediaRoot');
		$this->_conf['url']   = Configure::read('mediaUrl');
		$this->_conf['tmp']   = Configure::read('tmp');
		$this->_conf['imgMissingFile'] = Configure::read('imgMissingFile');
	}





	/**
	 * object public method: embed a generic bedita multimedia object
	 * 
	 * @param array obj, BEdita Multimedia Object
	 * @param array params, optional, parameters used by external helpers such as BeThumb->image
	 * 		   possible value for params:
	 * 			"presentation" => "thumb", "full", "link" (default defined by $defaultPresentation attribute)
	 * 			"URLonly" => if setted return only url
	 * 
	 * 			## USED only for image thumbnail on filesystem ##
	 * 			width, height, longside, at least one required, integer (if longside, w&h are ignored)
	 *         	mode, optional, 'crop'/'fill'/'croponly'/'stretch'
	 *         	modeparam, optional, depends on mode:
	 *             if fill, string representing hex color, ie 'FFFFFF'
	 *             if croponly, string describing crop zone 'C', 'T', 'B', 'L', 'R', 'TL', 'TR', 'BL', 'BR'
	 *             if stretch, bool to allow upscale (default false)
	 *         	type, optional, 'gif'/'png'/'jpg', force image target type
	 *         	upscale, optional, bool, allow or not upscale
	 * 
	 * @param array htmlAttributes, html attributes
	 *         
	 * @return string, output complete html tag
	 * 
	 */
	public function object ( $obj, $params = null, $htmlAttributes=array() )
	{
		// get object type
		$model = $this->getType ($obj);
		$params["presentation"] = (!empty($params["presentation"]))? $params["presentation"] : $this->defaultPresentation[$model];
		$method = "show" . ucfirst($params["presentation"]) . $model;
		
		if (method_exists($this, "show" . ucfirst($params["presentation"]) . $model)) {
			$output = $this->{"show" . ucfirst($params["presentation"]) . $model}($obj, $params, $htmlAttributes);
		} elseif (method_exists($this, "show" . $model)) {
			$output = $this->{"show" . $model}($obj, $params, $htmlAttributes);
		} else {
			$output = "unknown type: " . $model;
		}
				
		// output HTML
		return $output;
	}



	/******************************
	 * private functions
	 *****************************/


	/*
	 * return object model
	 */
	private function getType ($obj)
	{
		$model = Configure::read("objectTypes." . $obj['object_type_id'] . ".model");
		return (!empty($model))? $model : "";
	}



	/*
	 * produce html tag
	 */
	private function showImage ($obj, $params, $htmlAttributes)
	{
		$src = $this->getImageSrc($obj, $params);
		
		if (!$src) {
			$src = $this->getMediaTypeImage($obj);
		}
		
		if (!empty($params["URLonly"]))
			return $src;
		elseif ($params["presentation"] == "link")
			return $this->Html->link($obj['title'],$src, $htmlAttributes);
		else
			return $this->Html->image($src, $htmlAttributes);
	}
	
	
	private function getImageSrc($obj, $params) {
		// not local file
		if(preg_match(Configure::read("validate_resource.URL"), $obj["path"])) {
			$src = $obj['path'];
		//local file
		} else {
			$src = ($params["presentation"] == "thumb")? $this->BeThumb->image ($obj, $params) : $this->_conf['url'] . $obj['path'];
		}
		return $src;
	}

	
	/**
	 * html video output
	 *
	 * @param array $obj, object
	 * @param array $params, specific parameters
	 * @param array $htmlAttributes, html attributes
	 * @return string, html
	 */
	private function showVideo($obj, $params, $htmlAttributes)
	{
	
		if (!preg_match(Configure::read("validate_resource.URL"), $obj["path"])) {
			$obj['path'] = $this->_conf["url"] . $obj["path"]; 
		}
		$URLonly = (!empty($params["URLonly"]))? true : false;
		if ($params["presentation"] == "thumb") {
			if (!empty($obj["thumbnail"]) && preg_match(Configure::read("validate_resource.URL"), $obj["thumbnail"])) {
				$output = ($URLonly)? $obj["thumbnail"] : $this->Html->image($obj["thumbnail"], $htmlAttributes); 
			} else {
				$output = $this->MediaProvider->thumbnail($obj, $htmlAttributes, $URLonly);
				if (empty($output))	{
					$img = $this->getMediaTypeImage($obj);
					$output = $this->Html->image($img, $htmlAttributes);
				}
			}
		} elseif ($params["presentation"] == "full") {
			$output = $this->MediaProvider->embed($obj, $params, $htmlAttributes);
		} elseif ($params["presentation"] == "link") {
			$src = $this->MediaProvider->sourceEmbed($obj);
			$output = (!empty($URLonly))? $src : $this->Html->link($obj['title'],$src, $htmlAttributes);
		}
		
		if (empty($output)) {
			$output = $obj['path'];
		}
		
		return $output;
	}

	
	/**
	 * html audio output
	 *
	 * @param array $obj, object
	 * @param array $params, specific parameters
	 * @param array $htmlAttributes, html attributes
	 * @return string, html
	 */
	private function showAudio($obj, $params, $htmlAttributes)
	{
		if (!preg_match(Configure::read("validate_resource.URL"), $obj["path"])) {
			$obj['path'] = $this->_conf["url"] . $obj["path"]; 
		}
		
		if (!empty($params["URLonly"]))
			return $obj['path'];

		if ($params["presentation"] == "link") {
			return $this->Html->link($obj['title'],$obj['path'], $htmlAttributes);
		} elseif ($params["presentation"] == "full") {
			$output = $this->MediaProvider->embed($obj, $params, $htmlAttributes);
			if (empty($output)) {
				$output = $obj['path'];
			}
			return $output;
		} else {
			$img = $this->getMediaTypeImage($obj);
			return $this->Html->image($img, $htmlAttributes);
		}
	}
	
	
	/**
	 * html befile output
	 *
	 * @param array $obj, object
	 * @param array $params, specific parameters
	 * @param array $htmlAttributes, html attributes
	 * @return string, html
	 */
	private function showBEFile($obj, $params, $htmlAttributes) {
		if (!preg_match(Configure::read("validate_resource.URL"), $obj["path"])) {
			$obj['path'] = $this->_conf["url"] . $obj["path"]; 
		}
		
		if (!empty($params["URLonly"]))
			return $obj['path'];
		
		if ($params["presentation"] == "thumb") {
			$img = $this->getMediaTypeImage($obj);
			return $this->Html->image($img, $htmlAttributes);
		} else {
			return $this->Html->link($obj['title'],$obj['path'], $htmlAttributes);
		}
	}
	
	private function showApplication($obj, $params, $htmlAttributes) {		

		
		if ($params["presentation"] == "full") {
			
			if ($obj["application_name"] == "flash") {
				
				if (!preg_match(Configure::read("validate_resource.URL"), $obj["path"])) {
					$obj['path'] = $this->_conf["url"] . $obj["path"]; 
				}
				
				if (empty($htmlAttributes["width"]) && !empty($obj["width"])) {
					$htmlAttributes["width"] = $obj["width"];
				} elseif (empty($htmlAttributes["width"])) {
					$htmlAttributes["width"] = 320;
				}
				
				if (empty($htmlAttributes["height"]) && !empty($obj["height"])) {
					$htmlAttributes["height"] = $obj["height"];
				} elseif (empty($htmlAttributes["height"])) {
					$htmlAttributes["height"] = 200;
				}
				
				if (empty($htmlAttributes["application_version"]) && !empty($obj["application_version"])) {
					$htmlAttributes["application_version"] = $obj["application_version"];
				}
		
				if (empty($htmlAttributes["dir"]) && !empty($obj["text_dir"])) {
					$htmlAttributes["dir"] = $obj["text_dir"];
				}
				
				if (empty($htmlAttributes["lang"]) && !empty($obj["text_lang"])) {
					$htmlAttributes["lang"] = $obj["text_lang"];
				}
				
				$output = $this->BeEmbedFlash->embed($obj, $params, $htmlAttributes);
			}
		} elseif ($params["presentation"] == "thumb") {
			
			$imgThumb = $this->getMediaTypeImage($obj);
			$output = $this->Html->image($imgThumb, $htmlAttributes);
		}
		
		return $output;
		
	}
	
	
	
	protected function getMediaTypeImage($obj) {
		
		$img = "iconset/88px/";
		
		if ( defined("BEDITA_CORE_PATH") ) {
			if (is_dir(APP."webroot".DS."img".DS."iconset") ) {
				$img = "iconset".DS;
			}else {
				$img = Configure::read("beditaUrl")."/img/".$img;
			}
		}
		if (!empty($obj["mediatype"])) {
			$img = $img . $obj["mediatype"] . ".png";
		} elseif (!empty($obj["Category"])) {
			$imgname = (!is_array($obj["Category"]))? $obj["Category"] : $obj["Category"][0]["name"];
			$img = $img . $imgname . ".png";
		}else {
			$img = "iconset/88px/notype.png";
		}
		return $img;
	}

}
?>

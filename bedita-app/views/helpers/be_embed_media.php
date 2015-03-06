<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 * Helper class to embed media contents
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

    private $beThumb = null;

	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html', 'BeEmbedFlash', 'BeEmbedHtml5');

	function __construct() {
		// get configuration parameters
		$this->_conf = Configure::read('media') ;
		$this->_conf['root']  = Configure::read('mediaRoot');
		$this->_conf['url']   = Configure::read('mediaUrl');
		$this->_conf['tmp']   = Configure::read('tmp');
		$this->_conf['imgMissingFile'] = Configure::read('imgMissingFile');
        $this->beThumb = BeLib::getObject("BeThumb");
	}
	
	public function getValidImplementations() {
		return $this->beThumb->getValidImplementations();
	}

	/**
	 * object public method: embed a generic bedita multimedia object
	 * return html for object $obj with options $params and html attributes $htmlAttributes
	 * 
	 * @param array obj, BEdita Multimedia Object
	 * @param array params, optional, parameters used by external libraries such as BeThumb->image
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
	public function object ( $obj, $params = null, $htmlAttributes=array() ) {
		
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
	
	/**
	 * get img tag for thumbnail
	 * 
	 * @param array $obj
	 * @param array $htmlAttributes
	 * @param boolean $URLonly
	 * @return string html
	 */
	private function thumbnail(&$obj, $htmlAttributes = array(), $URLonly=false ) {
		if (!empty($obj["thumbnail"]) && preg_match(Configure::read("validate_resource.URL"), $obj["thumbnail"]))
			return (!$URLonly)? $this->Html->image($obj["thumbnail"], $htmlAttributes) : $obj["thumbnail"];
		
		if (!$helper = $this->getProviderHelper($obj))
			return "";
		
		return $helper->thumbnail($obj, $htmlAttributes, $URLonly);
	}

	/**
	 * get embed video
	 * 
	 * @param array $obj
	 * @param array $params
	 * @param array $attributes
	 * @return string html
	 */
	private function embed(&$obj, $params = array(), $attributes = array() ) {
		
		if (!$helper = $this->getProviderHelper($obj)){
			$obj['uri'] = ($this->checkURL($obj['uri'])) ? $obj['uri'] : Configure::read('mediaUrl').$obj['uri'];
			$BeEmbedHtml5 = $this->getHelper("BeEmbedHtml5");
			return  $BeEmbedHtml5->embed($obj, $params, $attributes);
		}


		/* provider helper to manage video/audio type don't exists
		if (!$helper = $this->getProviderHelper($obj)){
			$obj['uri'] = ($this->checkURL($obj['uri'])) ? $obj['uri'] : Configure::read('mediaUrl').$obj['uri'];
			$beEmbedFlash = $this->getHelper("BeEmbedFlash");
			return  $beEmbedFlash->embed($obj, $params, $attributes);
		}
		
		// provider helper exists and it's setted to use provider helper 
		if (!empty($params['useProviderPlayer'])) {
			return $helper->embed($obj, $attributes);
		} else {
			// try to use internal player
			$obj['uri'] = $this->sourceEmbed($obj);
			$beEmbedFlash = $this->getHelper("BeEmbedFlash");
			$res = $beEmbedFlash->embed($obj, $params, $attributes);
			if ( $res === false ) {
				$res =  $helper->embed($obj, $attributes) ;
			}
			return $res;
		}*/
	}
	
	/**
	 * get source url
	 * 
	 * @param array $obj
	 * @return string
	 */
	private function sourceEmbed(&$obj) {
		if (!$helper = $this->getProviderHelper($obj))
			return "";
			
		return $helper->sourceEmbed($obj);
	}


	/**
	 * produce html tag for image
	 * 
	 * if present $params["URLonly"], return uri of $obj
	 * if present $params["presentation"]=="link", return html link for $obj uri
	 * else return html image for $obj uri (@see HtmlHelper)
	 * 
	 * @param array $obj, object
	 * @param array $params, specific parameters
	 * @param array $htmlAttributes, html attributes
	 * @return string
	 */
	private function showImage ($obj, $params, $htmlAttributes) {

		$src = $this->getImageSrc($obj, $params);
		if (!$src) {
			$src = $this->getMediaTypeImage($obj);
		}
		
		if (!empty($params["URLonly"])) {
			return $src;
		} elseif ($params["presentation"] == "link") {
			return $this->Html->link($obj['title'],$src, $htmlAttributes);
		} else {
			if (empty($htmlAttributes["alt"])) {
				$htmlAttributes["alt"] = $obj["title"];
			}
			return $this->Html->image($src, $htmlAttributes);
		}
	}



	/**
	 * html video output
	 * return html or uri for video $obj, with options $params and html attributes $htmlAttributes
	 * if $params["presentation"] == "thumb" => return html thumb ($params["URLonly"] not present) or thumb uri ($params["URLonly"] is present)
	 * if $params["presentation"] == "full" => return embed object 
	 * if $params["presentation"] == "link" => return object link 
	 *
	 * @param array $obj, object
	 * @param array $params, specific parameters
	 * @param array $htmlAttributes, html attributes
	 * @return string
	 */
	private function showVideo($obj, $params, $htmlAttributes) {
		if (!preg_match(Configure::read("validate_resource.URL"), $obj["uri"])) {
			$obj['uri'] = $this->_conf["url"] . $obj["uri"];
		}
		$URLonly = (!empty($params["URLonly"]))? true : false;
		if ($params["presentation"] == "thumb") {
			if (empty($htmlAttributes["alt"])) {
				$htmlAttributes["alt"] = $obj["title"];
			}
			if (!empty($obj["thumbnail"]) && preg_match(Configure::read("validate_resource.URL"), $obj["thumbnail"])) {
				$output = ($URLonly)? $obj["thumbnail"] : $this->Html->image($obj["thumbnail"], $htmlAttributes); 
			} else {
				$output = $this->thumbnail($obj, $htmlAttributes, $URLonly);
				if (empty($output))	{
					$img = $this->getMediaTypeImage($obj);
					$output = $this->Html->image($img, $htmlAttributes);
				}
			}
		} elseif ($params["presentation"] == "link" || $URLonly) {
			$src = $this->MediaProvider->sourceEmbed($obj);
			$output = (!empty($URLonly))? $src : $this->Html->link($obj['title'], $src, $htmlAttributes);
		} elseif ($params["presentation"] == "full") {
			$output = $this->embed($obj, $params, $htmlAttributes);
		}
		
		if (empty($output)) {
			$output = $obj['uri'];
		}
		
		return $output;
	}

	/**
	 * html audio output
	 * return html or uri for audio $obj, with options $params and html attributes $htmlAttributes
	 * if present $params["URLonly"], return $obj uri
	 * if $params["presentation"] == "link" => return html link for $obj
	 * if $params["presentation"] == "full" => return embed object 
	 * else return html image for $obj (@see HtmlHelper)
	 *
	 * @param array $obj, object
	 * @param array $params, specific parameters
	 * @param array $htmlAttributes, html attributes
	 * @return string
	 */
	private function showAudio($obj, $params, $htmlAttributes) {
		if (!preg_match(Configure::read("validate_resource.URL"), $obj["uri"])) {
			$obj['uri'] = $this->_conf["url"] . $obj["uri"];
		}
		
		if (!empty($params["URLonly"]))
			return $obj['uri'];

		if ($params["presentation"] == "link") {
			return $this->Html->link($obj['title'],$obj['uri'], $htmlAttributes);
		} elseif ($params["presentation"] == "full") {
			$output = $this->embed($obj, $params, $htmlAttributes);
			if (empty($output)) {
				$output = $obj['uri'];
			}
			return $output;
		} else {
			$img = $this->getMediaTypeImage($obj);
			return $this->Html->image($img, $htmlAttributes);
		}
	}

	/**
	 * html befile output
	 * return html or uri for file $obj, with options $params and html attributes $htmlAttributes
	 * if present $params["URLonly"], return $obj uri
	 * if $params["presentation"] == "thumb" => return html image for $obj (@see HtmlHelper)
	 * else return html link for $obj (@see HtmlHelper)
	 *
	 * @param array $obj, object
	 * @param array $params, specific parameters
	 * @param array $htmlAttributes, html attributes
	 * @return string, html
	 */
	private function showBEFile($obj, $params, $htmlAttributes) {
		if (!preg_match(Configure::read("validate_resource.URL"), $obj["uri"])) {
			$obj['uri'] = $this->_conf["url"] . $obj["uri"];
		}
		
		if (!empty($params["URLonly"]))
			return $obj['uri'];
		
		if ($params["presentation"] == "thumb") {
			$img = $this->getMediaTypeImage($obj);
			return $this->Html->image($img, $htmlAttributes);
		} else {
			return $this->Html->link($obj['title'],$obj['uri'], $htmlAttributes);
		}
	}

	/**
	 * application show for $obj
	 * if $params["presentation"] == "full" && $obj["application_name"] == "flash" => return embed flash (@see BeEmbedFlashHelper)
	 * if $params["presentation"] == "thumb" => return html image for $obj (@see HtmlHelper)
	 * 
	 * @param array $obj, object
	 * @param array $params, specific parameters
	 * @param array $htmlAttributes, html attributes
	 * @return string
	 */
	private function showApplication($obj, $params, $htmlAttributes) {
		if ($params["presentation"] == "full") {
			if ($obj["application_name"] == "flash") {
				if (!preg_match(Configure::read("validate_resource.URL"), $obj["uri"])) {
					$obj['uri'] = $this->_conf["url"] . $obj["uri"];
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


	/**
	 * return object model for $obj, getting it from configuration, by object_type_id
	 *
	 * @param array $obj, object
	 * @return string
	 */
	private function getType ($obj) {
		$model = Configure::read("objectTypes." . $obj['object_type_id'] . ".model");
		return (!empty($model))? $model : "";
	}

	
	/**
	 * Return image $obj uri
	 * if $params["presentation"] == "thumb", return image thumb (@see BeThumb Lib)
	 * 
	 * @param array $obj, object
	 * @param array $params, specific parameters
	 * @return string
	 */
	private function getImageSrc($obj, $params) {

		if ($params["presentation"] == "thumb") {
		    $src = $this->beThumb->image($obj, $params);
		} else {
			$src = $this->_conf['url'] . $obj['uri'];
		}
		return $src;
	}


	/**
	 * get provider, if set
	 * 
	 * @param array $obj
	 * @return mixed string|boolean
	 */
	private function getProviderHelper(&$obj) {
		if(empty($obj["provider"])) 
			return false ;
		$helperName = Inflector::camelize($obj["provider"]);
		return $this->getHelper($helperName);
	}

	/**
	 * check whether url is valid
	 * 
	 * @param string $url
	 * @return boolean
	 */
	private function checkURL($url) {
		foreach (Configure::read('validate_resource.allow') as $reg) {
			if(preg_match($reg, $url)) 
				return true;
		}
		return false;
	}


	/**
	 * media type image
	 * return path for object type image
	 * if BACKEND_APP => try to find image in /webroot/img/iconset, then /webroot/img/
	 * 
	 * @param array $obj, object
	 * @return string, object type image
	 */
	protected function getMediaTypeImage($obj) {
		$img = "iconset/88px/";
		if ( BACKEND_APP == false ) {
			if (is_dir(APP."webroot".DS."img".DS."iconset") ) {
				$img = "iconset".DS;
			} else {
				$img = Configure::read("beditaUrl")."/bedita-app/webroot/img/".$img;
			}
		}
		if (!empty($obj["mediatype"])) {
			$img.= $obj["mediatype"] . ".png";
		} elseif (!empty($obj["Category"])) {
			$imgname = (!is_array($obj["Category"]))? $obj["Category"] : $obj["Category"][0]["name"];
			$img.= $imgname . ".png";
		}else {
			$img.= "notype.png";
		}
		return $img;
	}
}
?>
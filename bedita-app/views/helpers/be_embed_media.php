<?php
	
/**
 * BEdita Thumbnail helper
 * ------------------------------------------------------------------------------------
 * Name:     be_embed_media
 * Version:  1.0
 * Author:   Christiano Presutti - aka xho - ChannelWeb srl - Bedita staff
 * Purpose:  Insert BEdita multimedia objects in HTML page
 * 
 * Public methods:  image()
 *                  Returns: HTML <img> tag pointing to URI of cached image file
 * 
 * ------------------------------------------------------------------------------------
 */


class BeEmbedMediaHelper extends AppHelper {

	private $_helpername = "BeEmbedMedia Helper";

	// private
	private $_objects = array ("image", "audio", "video", "flash"); // supported
  //  private $_output  = false;
	private $_conf    = array ();


	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html', 'BeThumb', 'MediaProvider');
	
	
	function __construct()
	{
		// get configuration parameters
		$this->_conf = Configure::read('media') ;
		$this->_conf['root']  = Configure::read('mediaRoot');
		$this->_conf['url']   = Configure::read('mediaUrl');
		$this->_conf['cache'] = Configure::read('imgCache');
		$this->_conf['tmp']   = Configure::read('tmp');
		$this->_conf['imgMissingFile'] = Configure::read('imgMissingFile');
	}





	/**
	 * object public method: embed a generic bedita multimedia object
	 * 
	 * @param array obj, BEdita Multimedia Object
	 * @param array params, optional, parameters used by external helpers such as BeThumb->image
	 * 		   possible value for params:
	 * 			"presentation" => "thumb" (default), "full", "link"
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
		$params["presentation"] = (!empty($params["presentation"]))? $params["presentation"] : "thumb";
		
		// get object type
		$model = $this->getType ($obj);
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
			return $this->Html->link($src, $obj["title"], $htmlAttributes);
		else
			return $this->Html->image($src, $htmlAttributes);
	}
	
	
	private function getImageSrc($obj, $params) {
		// not local file
		if(preg_match(Configure::read("validate_resorce.URL"), $obj["path"])) {
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
		if (!preg_match(Configure::read("validate_resorce.URL"), $obj["path"])) {
			$obj['path'] = $this->_conf["url"] . $obj["path"]; 
		}
		$URLonly = (!empty($params["URLonly"]))? true : false;
		if ($params["presentation"] == "thumb") {
			$output = $this->MediaProvider->thumbnail($obj, $htmlAttributes, $URLonly);
			if (empty($output))	{
				$img = $this->getMediaTypeImage($obj);
				$output = $this->Html->image($img, $htmlAttributes);
			}
		} elseif ($params["presentation"] == "full") {
			$output = $this->MediaProvider->embed($obj, $htmlAttributes);
		} elseif ($params["presentation"] == "link") {
			$src = $this->MediaProvider->sourceEmbed($obj);
			$output = (!empty($URLonly))? $src : $this->Html->link($src, $obj['title'], $htmlAttributes);
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
		if (!preg_match(Configure::read("validate_resorce.URL"), $obj["path"])) {
			$obj['path'] = $this->_conf["url"] . $obj["path"]; 
		}
		
		if (!empty($params["URLonly"]))
			return $obj['path'];

		if ($params["presentation"] == "link") {
			return $this->Html->link($obj['path'], $obj['title'], $htmlAttributes);
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
		if (!preg_match(Configure::read("validate_resorce.URL"), $obj["path"])) {
			$obj['path'] = $this->_conf["url"] . $obj["path"]; 
		}
		
		if (!empty($params["URLonly"]))
			return $obj['path'];
		
		if ($params["presentation"] == "thumb") {
			$img = $this->getMediaTypeImage($obj);
			return $this->Html->image($img, $htmlAttributes);
		} else {
			return $this->Html->link($obj['path'], $obj['title'], $htmlAttributes);
		}
	}

	protected function getMediaTypeImage($obj) {
		$img = "iconset/88px/notype.png";
		if (!empty($obj["mediatype"]) && file_exists("img/iconset/88px/" . $obj["mediatype"] . ".png")) {
			$img = "iconset/88px/" . $obj["mediatype"] . ".png";
		} elseif (!empty($obj["Category"])) {
			$imgname = (!is_array($obj["Category"]))? $obj["Category"] : $obj["Category"][0]["name"];
			if (file_exists("img/iconset/88px/" . $imgname . ".png")) {
				$img = "iconset/88px/" . $imgname . ".png";
			} 
		}
		return $img;
	}

}
?>

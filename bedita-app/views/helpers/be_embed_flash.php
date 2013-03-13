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
 * Flash embed helper
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

class BeEmbedFlashHelper extends AppHelper {
	
	public $helpers = array("Javascript");
	private $heightDef = ""; 
	private $widthDef = "";
	private $appVerDef = "9.0.0";
	
	/**
	 * embed SWF flash object using swfobject
	 * 
	 * @param string $swfUrl
	 * @param array $attributes
	 * @param array $flashvars
	 * @param array $params
	 * @return string html code
	 */
	public function embedSwf ($swfUrl, $attributes = array(), $flashvars = array(), $params = array()) {
		$width = (!empty($attributes['width'])) ? $attributes['width'] : $this->widthDef;
		$height = (!empty($attributes['height'])) ? $attributes['height'] : $this->heightDef;
		$app_ver = (!empty($attributes['application_version']))? $attributes['application_version'] : $this->appVerDef;
		if (empty($attributes['id'])) {
			$attributes['id'] = "be_id_" . rand(10000, 11000) . rand(1, 10000);
		}
		if (!empty($attributes['src'])) {
			unset($attributes['src']);
		}
		
		$fv  = json_encode($flashvars);
		$par = json_encode($params);
		$att = json_encode($attributes);
		
		if ( defined("BEDITA_CORE_PATH") && !file_exists(APP . "webroot/js/swfobject.js")) {
			$output = $this->Javascript->link(Configure::read('beditaUrl') . "/js/swfobject.js",false);
		} else {
			$output = $this->Javascript->link("swfobject",false);
		}
		$output .= '<div id="'.$attributes['id'].'"></div><script type="text/javascript">swfobject.embedSWF("'.$swfUrl.'","'.$attributes['id'].'","'.$width.'","'.$height.'","'.$app_ver.'","expressInstall.swf",'.$fv.','.$par.','.$att.');</script>';
		return $output;
	}
	
	/**
	 * embed flv video
	 * 
	 * @param string $flvUrl
	 * @param array $attributes
	 * @param array $flashvars
	 * @param array $params
	 * mixed string|boolean, html code of embed media, or false if file extension is not supported
	 */
	public function embedFlv($flvUrl, $attributes = array(), $flashvars = array(), $params = array()) {
		return $this->embedPlayer($flvUrl, $attributes, $flashvars, $params, "video");	
	}

	/**
	 * embed audio
	 * 
	 * @param string $audioFile path or url
	 * @param array $attributes
	 * @param array $flashvars
	 * @param array $params
	 * mixed string|boolean, html code of embed media, or false if file extension is not supported
	 */
	public function embedAudio($audioFile, $attributes = array(), $flashvars = array(), $params = array()) {
		return $this->embedPlayer($audioFile, $attributes, $flashvars, $params, "audio");	
	}

	/**
	 * generate code to embed video/audio player
	 * file extension supported: mp3, flv, m4v
	 * 
	 * @param string $fileToPlay
	 * @param array $attributes
	 * @param array $flashvars
	 * @param array $params
	 * @param string $fileType
	 * @return mixed string|boolean, html code of embed media, or false if file extension is not supported
	 */
	public function embedPlayer($fileToPlay, $attributes = array(), $flashvars = array(), $params = array(), $fileType=null) {
		if (empty($fileType)) {
			$extension = $this->getFileExtension($fileToPlay);
			if ($extension == "mp3") {
				$fileType = "audio";
			} elseif ($extension == "flv" || $extension == 'm4v') {
				$fileType = "video";
			} else {
				return false;
			}
		}
		
		$beditaUrl = Configure::read('beditaUrl');
		$swfUrl = empty($attributes['src']) ? $beditaUrl."/swf/".Configure::read("media." . $fileType . ".player") : $beditaUrl."/swf/".$attributes['src'] ;
		
		if (empty($attributes["width"]))
			$attributes["width"] = Configure::read("media." . $fileType . ".width");
		if (empty($attributes["height"]))
			$attributes["height"] = Configure::read("media." . $fileType . ".height");
		
		$pathParts = pathinfo($swfUrl);
		$methodName = "embed".Inflector::camelize($pathParts['filename']);
		
		if (method_exists($this, $methodName ) ) {
			return $this->$methodName($swfUrl, $fileToPlay, $attributes, $flashvars, $params, $fileType);
		} 		
		return $this->embedSwf( $swfUrl , $attributes, $flashvars, $params );
	}

	/**
	 * embed generic flash object (video, swf, audio mp3)
	 * file extension supported: mp3, flv, m4v
	 * 
	 * @param array $obj BEdita multimedia object
	 * @param array $params contains flashvars, params <param> tag
	 * @param array $htmlAttributes
	 * @return mixed string|boolean, html code of embed media, or false if file extension is not supported
	 */
	public function embed($obj , $params, $htmlAttributes ) {
		if (empty($obj['uri'])) {
			return __("No file to embed");
		}
		$flashvars = empty($params['flashvars']) ? array() : $params['flashvars'];	
		$flashParams = empty($params['params']) ? array() : $params['params'];	
		
		$extension = $this->getFileExtension($obj['uri']);
			
		if ($obj["object_type_id"] == Configure::read("objectTypes.audio.id") && $extension == 'mp3') {
			return $this->embedAudio($obj['uri'], $htmlAttributes, $flashvars, $flashParams);
		} elseif ($extension == 'flv' || $extension == 'm4v' || $extension == 'mp4') {
			if (!empty($obj['thumbnail'])) {
				$flashvars['thumbnail'] = $obj['thumbnail']; 
			}			
			return $this->embedFlv($obj['uri'], $htmlAttributes, $flashvars, $flashParams);
		} else if ($extension == 'swf') {
			 return $this->embedSwf($obj['uri'], $htmlAttributes, $flashvars, $flashParams);
		} else {
			return false;
		}
		
	}

	/**
	 * get file extension
	 * 
	 * @param string $filePath
	 * @return mixed string|boolean, file extension or false (if extension is not recognized through pathinfo)
	 */
	private function getFileExtension($filePath) {
		$path_parts = pathinfo($filePath);
		if (empty($path_parts['extension']))
			return false;
		
		return strtolower($path_parts['extension']);
	}

	/**
	 * generate code for embed Flowplayer
	 * default behavior: generate <div></div> and flowplayer javascript call
	 * 			$params[htmlEmbed] = true => generate directly HTML code (<object....><param>...<embed/>...</object>) no javascript 
	 * 
	 * @param string $swfUrl
	 * @param string $mediaUrl, audio/video file (mp3/flv)
	 * @param array $attributes, html attributes
	 * @param array $flashvars
	 * @param array $params
	 * @param string $fileType, video/audio
	 * @return string html code for views
	 */
	public function embedFlowplayer($swfUrl, $mediaUrl, $attributes, $flashvars, $params, $fileType) {
		$defaultAudioPlayer = array("controls" => array("fullscreen" => false));
		$defaultVideoPlayer = array("controls" => array());
		$timeDisabled = array("controls" => array("time" => false));
		
		if (empty($flashvars['clip'])) {
			$flashvars['clip'] = array("autoPlay" => false);
		}
		
		if (empty($flashvars['playlist'])) {
			$flashvars['playlist'] = array();
		}
		if (!empty($flashvars['thumbnail'])) {
			array_unshift($flashvars['playlist'], 
						  array("url" => $flashvars['thumbnail'], "autoPlay" => "true"), 
						  $mediaUrl
			);
			unset($flashvars['thumbnail']);
		} else {
			array_unshift($flashvars['playlist'], $mediaUrl);
		}
		
		if (!empty($flashvars['plugins'])) {
			if ($fileType == "audio" && empty($flashvars['plugins']['controls'])) {
				$flashvars['plugins'] = array_merge($flashvars['plugins'], $defaultAudioPlayer);
			}
			if ($attributes['width'] < 280 && empty($flashvars['plugins']['controls']['time']) ) {
				$flashvars['plugins'] = array_merge($flashvars['plugins'], $timeDisabled);
			}
			
		} else {
			if ($fileType == "audio") {
				$flashvars['plugins'] = $defaultAudioPlayer;
			}elseif ($fileType == "video") {
				$flashvars['plugins'] = $defaultVideoPlayer;
			}
			if ($attributes['width'] < 280) {
				$flashvars['plugins'] = array_merge($flashvars['plugins'], $timeDisabled);
			}
		} 
		
		if (empty($attributes['id'])) {
			$attributes['id'] = preg_replace("/[\s\.]/", "", "be_id_" . microtime());
		}
		
		if (!empty($params["htmlEmbed"])) {
			unset($params["htmlEmbed"]);
			$flashvars = "config=" . json_encode($flashvars);
			$output = $this->embedHtml($swfUrl, $attributes, $flashvars, $params);
		} else {
			$params = array_merge($params, array('src' => $swfUrl));
			if (defined("BEDITA_CORE_PATH") && !file_exists(APP . "webroot/js/flowplayer.min.js")) {
				$output = $this->Javascript->link(Configure::read('beditaUrl') . "/js/flowplayer.min.js",false);
			} else {
				$output = $this->Javascript->link("flowplayer.min",false);
			}
			
			$output .= "<script type='text/javascript'>" . 
				"flowplayer('".$attributes['id']."', ".json_encode($params).", ".json_encode($flashvars).");".
			"</script>";
			
			$width = $attributes["width"];
			$height = $attributes["height"];
			unset($attributes["width"]);
			unset($attributes["height"]);
			$output .= "<div";
			foreach ($attributes as $key => $val) {
				$output .= " " . $key . "=\"" . $val . "\"";
			}
			$output .= " style=\"width: ". $width ."px; height: ". $height ."px; z-index: 200;\"";
			$output .= "></div>";
		}
		return $output;
	}
	
	public function embedWpaudioplayer($swfUrl, $audioFileUrl, $attributes, $flashvars, $params) {
		$flashvars["soundFile"] = $audioFileUrl;
		return $this->embedSwf( $swfUrl , $attributes, $flashvars, $params );				 
	}
	
	/**
	 * generate html code for embedding flash object
	 * 
	 * @param string $swfUrl
	 * @param array $attributes
	 * @param array $flashvars
	 * @param array $params
	 * @return string html code
	 */
	public function embedHtml($swfUrl, $attributes, $flashvars=array(), $params=array()) {
		$flashvars = (is_array($flashvars))? json_encode($flashvars) : $flashvars;
		$output = "<object id='".$attributes["id"]."' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'". 
				 	 " width='".$attributes["width"]."' height=".$attributes["height"]."'>".
				  "<param name='movie' value='".$swfUrl."' />". 
				  "<param name='flashvars' value='".$flashvars."' />";
		foreach ($params as $key => $value) {
			$output .= "<param name='".$key."' value='".$value."' />";
		}
		$output.= "<embed type='application/x-shockwave-flash'" .
					" width='".$attributes["width"]."' height=".$attributes["height"]."'". 
					" src='".$swfUrl."'".
					" flashvars='".$flashvars."'/>".
				  "</object>";
		return $output;
	}
}

?>
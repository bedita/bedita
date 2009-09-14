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
	
	public function embedSwf ($swfUrl, $attributes = array(), $flashvars = array(), $params = array()) {
		$width = (!empty($attributes['width'])) ? $attributes['width'] : $this->widthDef;
		$height = (!empty($attributes['height'])) ? $attributes['height'] : $this->heightDef;
		$app_ver = (!empty($attributes['application_version']))? $attributes['application_version'] : $this->appVerDef;
		if (empty($attributes['id'])) {
			$attributes['id'] = preg_replace("/[\s\.]/", "", "be_id_" . microtime());
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
		$output .= '<script type="text/javascript">swfobject.embedSWF("'.$swfUrl.'","'.$attributes['id'].'","'.$width.'","'.$height.'","'.$app_ver.'","expressInstall.swf",'.$fv.','.$par.','.$att.');</script><div id="'.$attributes['id'].'"></div>';
		return $output;
	}
	
	
	public function embedFlv($flvUrl, $attributes = array(), $flashvars = array(), $params = array()) {
		return $this->embedPlayer($flvUrl, $attributes, $flashvars, $params, "video");	
	}
	
	public function embedAudio($audioFile, $attributes = array(), $flashvars = array(), $params = array()) {
		return $this->embedPlayer($audioFile, $attributes, $flashvars, $params, "audio");	
	}
	
	public function embedPlayer($fileToPlay, $attributes = array(), $flashvars = array(), $params = array(), $fileType=null) {
		if (empty($fileType)) {
			$extension = $this->getFileExtension($fileToPlay);
			if ($extension == "mp3") {
				$fileType = "audio";
			} elseif ($extension == "flv") {
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
	
	
	
	public function embed($obj , $params, $htmlAttributes ) {
		
		$flashvars = empty($params['flashvars']) ? array() : $params['flashvars'];	
		$flashParams = empty($params['params']) ? array() : $params['params'];	
		
		$extension = $this->getFileExtension($obj['path']);
			
		if ($obj["object_type_id"] == Configure::read("objectTypes.audio.id") && $extension == 'mp3') {
			return $this->embedAudio($obj['path'], $htmlAttributes, $flashvars, $flashParams);	
		} elseif ($extension == 'flv') {
			if (!empty($obj['thumbnail'])) {
				$flashvars['thumbnail'] = $obj['thumbnail']; 
			}			
			return $this->embedFlv($obj['path'], $htmlAttributes, $flashvars, $flashParams);	
		} else if ($extension == 'swf') {
			 return $this->embedSwf($obj['path'], $htmlAttributes, $flashvars, $flashParams);
		} else {
			return false;
		}
		
	}
	
	private function getFileExtension($filePath) {
		$path_parts = pathinfo($filePath);
		if (empty($path_parts['extension']))
			return false;
		
		return strtolower($path_parts['extension']);
	}
	
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
		$output .= " style=\"width: ". $width ."px; height: ". $height ."px;\"";
		$output .= "></div>";
		return $output;
	}
	
	public function embedWpaudioplayer($swfUrl, $audioFileUrl, $attributes, $flashvars, $params) {
		$flashvars["soundFile"] = $audioFileUrl;
		return $this->embedSwf( $swfUrl , $attributes, $flashvars, $params );				 
	}
}













?>
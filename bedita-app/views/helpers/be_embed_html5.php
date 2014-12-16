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
 * HTML5 embed helper
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */


class BeEmbedHtml5Helper extends AppHelper {


public $helpers = array("Html");



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

		$params = empty($params['params']) ? array() : $params['params'];	
		$extension = $this->getFileExtension($obj['uri']);
		$output = "";
		
		if ($obj["object_type_id"] == Configure::read("objectTypes.audio.id") && $extension == 'mp3') {
			$fileType = "audio";

			return $this->embedAudio($obj['uri'], $htmlAttributes, $flashvars, $flashParams);

		} else {
			
			$fileType = "video";	
			$beditaUrl = Configure::read('beditaUrl');
			
			//$swfUrl = empty($attributes['src']) ? $beditaUrl."/swf/".Configure::read("media." . $fileType . ".player") : $beditaUrl."/swf/".$attributes['src'] ;
			//$html5PlayerUrl = empty($attributes['src']) ? $beditaUrl.

			if (empty($attributes["width"]))
				$attributes["width"] = Configure::read("media." . $fileType . ".width");
			if (empty($attributes["height"]))
				$attributes["height"] = Configure::read("media." . $fileType . ".height");
			
			$width = (!empty($attributes['width'])) ? $attributes['width'] : $this->widthDef;
			$height = (!empty($attributes['height'])) ? $attributes['height'] : $this->heightDef;

			//$app_ver = (!empty($attributes['application_version']))? $attributes['application_version'] : $this->appVerDef;
			
			if (empty($attributes['id'])) {
				$attributes['id'] = "be_id_" . rand(10000, 11000) . rand(1, 10000);
			}
			if (!empty($attributes['src'])) {
				unset($attributes['src']);
			}
			
			//$fv  = json_encode($flashvars);
			$par = json_encode($params);
			$att = json_encode($attributes);
			
			//if ( defined("BEDITA_CORE_PATH") && !file_exists(APP . "/webroot/js/libs/mediaelement/mediaelement-and-player.min.js")) {
				$output .= $this->Html->script(Configure::read('beditaUrl') . "/js/libs/mediaelement/mediaelement-and-player.min.js",false);
				$output .= $this->Html->css(Configure::read('beditaUrl') . "/js/libs/mediaelement/mediaelementplayer.css",false);
			//} 
			//pr($output);exit;
			
			//else {
			//	$output = $this->Javascript->link("swfobject",false);
			//}
			//$output .= '<div id="'.$attributes['id'].'"></div>
			//	<script type="text/javascript">swfobject.embedSWF("'.$swfUrl.'","'.$attributes['id'].'","'.$width.'","'.$height.'","'.$app_ver.'","expressInstall.swf",'.$fv.','.$par.','.$att.');</script>';


			$output .= '<video src="'.$obj['uri'].'" width="'.$width.'" height="'.$height.'" controls="controls" ></video>';
			$output .= '<script>jQuery(document).ready(function($) {
							$("video").mediaelementplayer({      					
        						features: ["playpause","loop","current","progress","duration","volume"]
    						});
						});</script>';
			return $output;

			//$pathParts = pathinfo($swfUrl);
			//$methodName = "embed".Inflector::camelize($pathParts['filename']);
			
			//if (method_exists($this, $methodName ) ) {
			//	return $this->$methodName($swfUrl, $fileToPlay, $attributes, $flashvars, $params, $fileType);
			//} 		
			//return $this->embedSwf( $swfUrl , $attributes, $flashvars, $params );
		} 


		/*($extension == 'flv' || $extension == 'm4v' || $extension == 'mp4') {
			if (!empty($obj['thumbnail'])) {
				$flashvars['thumbnail'] = $obj['thumbnail']; 
			}			
			return $this->embedFlv($obj['uri'], $htmlAttributes, $flashvars, $flashParams);
		} else if ($extension == 'swf') {
			 return $this->embedSwf($obj['uri'], $htmlAttributes, $flashvars, $flashParams);
		} else {
			return false;
		}*/
		
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








}
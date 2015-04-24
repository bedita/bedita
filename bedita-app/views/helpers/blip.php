<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2015 ChannelWeb Srl, Chialab Srl
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

App::import(array(
	'type' => 'File',
	'name' => 'MediaProviderInterface',
	'search' => array(BEDITA_CORE_PATH . DS .'views' . DS . 'helpers')
));

/**
 * Blip tv helper class
 */
class BlipHelper extends AppHelper implements MediaProviderInterface {

	public $helpers = array('Html');

	private $blipComponent;

	/**
	 * Is Blip tv video source available?
	 *
	 * @param array $obj a representation of Video BEdita object
	 * @return boolean
	 */
	public function isSourceAvailable(array $obj) {
		return true;
	}

	/**
	 * Return the thumbnail supplied by Blip tv as <img> tag
	 * If $URLonly is true return only the url to img
	 *
	 * @param array $obj a representation of Video BEdita object
	 * @param array $htmlAttributes HTML attributes to set in <img> tag
	 * @param boolean $URLonly
	 * @return string
	 */
	public function thumbnail(array $obj, array $htmlAttributes, $URLonly) {
		$this->initBlipComponent();
		$this->blipComponent->getInfoVideo($obj['video_uid']);
		$src = sprintf($Component->info['thumbnailUrl'], $obj['video_uid']);
		return (!$URLonly) ? $this->Html->image($src, $htmlAttributes) : $src;
	}

	/**
	 * Embed Blip tv video
	 *
	 * @param array $obj a representation of Video BEdita object
	 * @param array $attributes HTML attributes
	 * @return string|boolean
	 */
	public function embed(array $obj, array $attributes) {
		$this->conf = Configure::getInstance() ;
		if (!isset($this->conf->media_providers["blip"]["params"])) {
			return "" ;
		}

		$url = rawurlencode($obj["uri"]);
		$url .= "&format=json";
		$url = sprintf($this->conf->media_providers["blip"]["params"]["urlembed"], $url);
		if (!$oEmbed = $this->oEmbedInfo($url)) {
			return false;
		}

		if (empty($attributes["width"]) && empty($attributes["height"])) {
			$attributes["width"] = $this->conf->media_providers["blip"]["params"]["width"];
			$attributes["height"] = $this->conf->media_providers["blip"]["params"]["height"];
		} else {
			$ratio = $oEmbed["width"]/$oEmbed["height"];
			// calculate height
			if (!empty($attributes["width"])) {
				$attributes["height"] = $attributes["width"]* (1/$ratio);
			// calculate width
			} else {
				$attributes["width"] = $attributes["height"]* ($ratio);
			}
		}

		$oEmbed["html"] = preg_replace('/width="\d*"/', 'width="'. $attributes["width"] . '"', $oEmbed["html"]);
		$oEmbed["html"] = preg_replace('/height="\d*"/', 'height="'. $attributes["height"] . '"', $oEmbed["html"]);

		return $oEmbed["html"];
	}

	/**
	 * Return the url to Blip tv video source if it's available else it returns an empty string
	 *
	 * @param array $obj a representation of Video BEdita object
	 * @return string
	 */
	public function source(array $obj) {
		$this->initBlipComponent();
		$info = $this->blipComponent->getInfoVideo($obj['video_uid']);
		if(preg_match("/^http:\/\/blip.tv\/file\/get\/.*\.m4v|^http:\/\/blip.tv\/file\/get\/.*\.flv/",$info["mediaUrl"],$matched)) {
			return $matched[0] ;
		} elseif (!empty($info["additionalMedia"])) {
			foreach ($info["additionalMedia"] as $media) {
				if(preg_match("/^http:\/\/blip.tv\/file\/get\/.*\.m4v|^http:\/\/blip.tv\/file\/get\/.*\.flv/",$media["url"],$matched)) {
					return $matched[0] ;
				}
			}
		}

		return '';
	}

	/**
	 * create new instance of BlipComponent if not instanced yet
	 */
	private function initBlipComponent() {
		if (empty($this->blipComponent) || !($this->blipComponent instanceof BlipComponent)) {
			if(!class_exists("BeBlipComponent")){
				App::import('Component', "BeBlip");
			}
			$this->blipComponent = new BeBlipComponent();
		}
	}
}

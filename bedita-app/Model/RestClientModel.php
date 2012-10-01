<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2011 ChannelWeb Srl, Chialab Srl
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

App::uses("BEAppModel", "Model");

/**
 * simple REST client model
 * uses internally "curl" or cake HttpSocket if "curl" module not available
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class RestClientModel extends BEAppModel {

	public $useTable = false;

	public $httpReady = false;
	public $client;
	public $useCurl = false;
	public $curlOptions = array();

	/**
	 * setup rest client
	 * if curl available setup RestClientModel to use it
	 * else setup RestClientModel to use CakePHP HttpSocket class
	 */
	public function setup() {
		if(!$this->httpReady) {

			if(function_exists("curl_init")) {
				$this->client = curl_init();
				$this->curlOptions = array(
					CURLOPT_HEADER => false,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_USERAGENT => "BEdita agent",
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_MAXREDIRS => 100,
				);
				if(Configure::read("proxyOptions") != null) {
					$proxyOpts = Configure::read("proxyOptions");
					$this->curlOptions[CURLOPT_PROXY] = $proxyOpts["host"];
					if($proxyOpts["type"] === "socks5") {
						$this->curlOptions[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
					}
				}
				curl_setopt_array($this->client, $this->curlOptions);
				$this->useCurl = true;
			} else {
				App::import('Core', 'HttpSocket');
				$this->client = new HttpSocket();
				$this->useCurl = false;
			}
			App::import('Core', 'Xml');
			$this->httpReady = true;
		}
	}

	/**
	 * Do a HTTP GET request and returns output response.
	 * Output may be parsed (only xml/json) using $outType argument ("xml" or "json").
	 *
	 * @param string $uri URL to GET
	 * @param array $params, URL query parameters
	 * @param string $outType, can be "xml" or "json", if present output will be parsed
	 *			if "xml" => php array, if "json" => json_decode is called
	 * @param boolean $camelize, used if $outType = 'xml'
	 *			true (default) camelize array keys corresponding to xml items that contain other xml items (CakePHP default behavior)
	 *			false leave array keys equal to xml items
	 */
	public function get($uri, array $params = array(), $outType = null, $camelize = true) {
		if(Configure::read('debug') > 0) {
			$this->log("HTTP REQUEST:\nuri " . $uri . "\nparams " . print_r($params, true), LOG_DEBUG);
		}

		if(!$this->useCurl) {
			$out = $this->client->get($uri, $params);
		} else {
			curl_setopt($this->client, CURLOPT_HTTPGET, true);
			$queryParms = (empty($params)) ? "" : "?" . http_build_query($params);
			curl_setopt($this->client, CURLOPT_URL, $uri . $queryParms);
			$out = curl_exec($this->client);
			if(curl_errno($this->client)) {
				$err = curl_error($this->client);
				$this->log("Error: " . $err);
			}
		}
		if(Configure::read('debug') > 0) {
			$this->log("HTTP RESPONSE:\n" . $out . "\n", LOG_DEBUG);
		}

		if($outType != null) {
			if($outType === "xml") {
				$xml = new Xml($out);
				$out = $xml->toArray($camelize);
			} else if ($outType === "json") {
				$out = json_decode($out);
			}
		}
		return $out;
	}

	/**
	 * Do a HTTP POST request and returns output response.
	 * Output may be parsed (only xml/json) using $outType argument ("xml" or "json").
	 *
	 * @param string $uri, HTTP POST URL
	 * @param array $params, POST query parameters
	 * @param string $outType, can be "xml" or "json", if present output will be parsed
	 * 	if "xml" => php array, if "json" => json_decode is called
	 * @param boolean $camelize, used if $outType = 'xml'
	 *			true (default) camelize array keys corresponding to xml items that contain other xml items (CakePHP default behavior)
	 *			false leave array keys equal to xml items
	 */
	public function post($uri, array $params = array(), $outType = null, $camelize = true) {
		if(Configure::read('debug') > 0) {
			$this->log("HTTP REQUEST:\nuri " . $uri . "\nparams " . print_r($params, true), LOG_DEBUG);
		}
		if(!$this->useCurl) {
			$out = $this->client->post($uri, $params);
		} else {
			curl_setopt($this->client, CURLOPT_POST, true);
			curl_setopt($this->client, CURLOPT_POSTFIELDS, http_build_query($params));
			curl_setopt($this->client, CURLOPT_URL, $uri);
			$out = curl_exec($this->client);
			if(curl_errno($this->client)) {
				$err = curl_error($this->client);
				$this->log("Error: " . $err);
			}
		}

		if(Configure::read('debug') > 0) {
			$this->log("HTTP RESPONSE:\n" . $out . "\n", LOG_DEBUG);
		}
		if($outType != null) {
			if($outType === "xml") {
				$xml = new Xml($out);
				$out = $xml->toArray($camelize);
			} else if ($outType === "json") {
				$out = json_decode($out);
			}
		}
		return $out;
	}
}
?>

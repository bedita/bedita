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
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Link extends BEAppObjectModel {

	var $actsAs = array();

	public $searchFields = array();

	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"RelatedObject",
															"Category")),

				"default" => array("BEObject" => array("ObjectType", "RelatedObject", "Category")),

				"minimum" => array("BEObject" => array("ObjectType"))
		);

	public function beforeSave() {
		if(!empty($this->data['Link']['url'])) { // when saveField() is called, no url checks should be done
			$url = $this->data['Link']['url'];
			if(!$this->isHttp($url) && !$this->isHttps($url)) {
				$url = "http://" . $url;
				$this->data['Link']['url'] = $url;
			}
			if(empty($this->data['Link']['id']) || !empty($this->data['Link']['noduplicate'])) {
				$link = $this->find('all', array('conditions' => array('url' => $this->data['Link']['url'] ,'title' => $this->data['Link']['title'])));
				if(!empty($link)) {// do not save twice the same link
					throw new BeditaException(__("Error saving link: duplicated link", true), $this->validationErrors);
					return false;
				}
			}
			$date = new DateTime();
			$this->data['Link']['http_code'] = $this->responseForUrl($url);
			$this->data['Link']['http_response_date'] = $date->format(DATE_RFC3339);
		}
		return true;
	}

	public function responseForUrl($url) {
		$result = @get_headers($url);
		return (empty($result) || !$result) ? "Invalid url" : $result[0];
	}
	
	public function isHttp($url) {
		if(strlen($url)<10) return false;
		return (substr($url,0,7) == "http://");
	}
	
	public function isHttps($url) {
		if(strlen($url)<10) return false;
		return (substr($url,0,8) == "https://");
	}
}
?>

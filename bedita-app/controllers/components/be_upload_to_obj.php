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
 * Upload component: common file, multimedia file, third party multimedia url (bliptv, vimeo, youtube, etc.)
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeUploadToObjComponent extends Object {
	var $components	= array('BeFileHandler', 'BeBlip', 'BeVimeo', 'BeYoutube') ;

	function startup(&$controller) {
		$this->params = $controller->params;
		$this->BeFileHandler->startup($controller) ;
	}

	/**
	 * Uploads a file to location and create stream object.
	 
	 * @return object_id if upload was successful, false otherwise.
	 */
	function upload($dataStream=null, $formFileName="Filedata") {
		$result = false ;
		if (empty($this->params["form"][$formFileName]["name"]))
			throw new BEditaException(__("No file in the form", true));

		if ($this->params['form'][$formFileName]['error'])
			throw new BEditaUploadPHPException($this->params['form'][$formFileName]['error']);
			
		// Prepare data
		if (!empty($dataStream)) {
			$data = array_merge($dataStream, $this->params['form'][$formFileName]);
		} else {
			$data = $this->params['form'][$formFileName];
		}
		$data['mime_type'] = $this->BeFileHandler->getMimeType($data);
		unset($data['type']);
		
		if (!empty($this->params['form']['mediatype'])) {
			$data['mediatype'] = $this->params['form']['mediatype'];
		}
		
		$override = (isset($this->params['form']['override'])) ? ((boolean)$this->params['form']['override']) : false ;

		if (empty($data['title']))
			$data['title'] = $data['name'];

		$data['path']	= $data['tmp_name'] ;

		if (empty($data["status"]))
			$data["status"] = "on";

		unset($data['tmp_name']) ;
		unset($data['error']) ;

		$result = $this->BeFileHandler->save($data) ;
		
		return $result;
	}
	
	/**
	 * Create obj stream from URL.
	 * Form must to have: url, title, lang.
	 * @return boolean true if upload was successful, int $id otherwise.
	 */
	function uploadFromURL($dataURL, $clone=false) {

		$result = false ;
		$getInfoURL = false;
		
		$mediaProvider = ClassRegistry::init("Stream")->getMediaProvider($dataURL['url']);
		
		if(empty($dataURL['title'])) {
			$link = ClassRegistry::init("Link");
			$dataURL['title'] = $link->readHtmlTitle($dataURL['url']);
		}
		
		if (!empty($mediaProvider)) {
			$dataURL['provider']	= $mediaProvider["provider"];
			$dataURL['uid']  	 	= $mediaProvider["uid"];
		
			$componentName = Inflector::camelize("be_" . $mediaProvider["provider"]);
			if (isset($this->{$componentName}) && method_exists($this->{$componentName}, "setInfoToSave")) {
				if (!$this->{$componentName}->setInfoToSave($dataURL)) {
					throw new BEditaMediaProviderException(__("Multimedia Provider not found or error preparing data to save",true)) ;
				}
			} else {
				throw new BEditaMediaProviderException(__("Multimedia provider is not managed",true)) ;
			}
			
		} else {
			$dataURL['provider'] = null;
			$dataURL['uid'] = null;
			$dataURL['path'] = $dataURL["url"];
			$getInfoURL = true;
		}
		
		if (empty($dataURL["status"]))
			$dataURL['status'] = "on";
		
		if (!empty($this->params['form']['mediatype'])) {
			$dataURL['mediatype'] = $this->params['form']['mediatype'];
		}
		
		$id = $this->BeFileHandler->save($dataURL, $clone, $getInfoURL) ;
		
		return $id;
		
	}
	
	function cloneMediaObject($data) {
		if (!empty($data["id"]))
			unset($data["id"]);
			
		if(preg_match(Configure::read("validate_resource.URL"), $data["path"])) {
			$data['url'] = $data["path"];
			return $this->uploadFromURL($data, true);
		} else {
			$data['path'] = Configure::read("mediaRoot") . $data["path"];
			if (empty($data["size"])) {
				$data["size"] = filesize($data["path"]);
			}
			if (!empty($this->params['form']['mediatype'])) {
				$data['mediatype'] = $this->params['form']['mediatype'];
			}
			return $this->BeFileHandler->save($data, true);
		}
	}
	
	
	function getThumbnail($data) {
		if (!empty($data["thumbnail"]) && preg_match(Configure::read("validate_resource.URL"), $data["thumbnail"])) {
			$thumbnail = $data["thumbnail"]; 	
		} else {
			if (empty($data["provider"]) || empty($data["uid"])) {
				$url = (!empty($data['url']))? $data['url'] : $data['path'];
				$mediaProvider = ClassRegistry::init("Stream")->getMediaProvider($url);
				if (!empty($mediaProvider)) {
					$provider = $mediaProvider["provider"];
					$uid = $mediaProvider["uid"];
				}
			} else {
				$provider = $data["provider"];
				$uid = $data["uid"];
			}
			
			$thumbnail = null;
			
			if (!empty($provider)) {
				$componentName = Inflector::camelize("be_" . $provider);
				if (isset($this->{$componentName}) && method_exists($this->{$componentName}, "getThumbnail")) {
					if (!$thumbnail	= $this->{$componentName}->getThumbnail($uid)) {
						throw new BEditaMediaProviderException(__("Multimedia Provider not found or error getting thumbnail",true)) ;
					}
				} else {
					throw new BEditaMediaProviderException(__("Multimedia provider is not managed",true)) ;
				}
			}
		}
		return $thumbnail;
	}
		
}
?>
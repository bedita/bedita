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
 * Upload component: common file, multimedia file, third party multimedia url (vimeo, youtube, etc.)
 *
 */
class BeUploadToObjComponent extends Object {
	var $components	= array('BeFileHandler', 'BeVimeo', 'BeYoutube') ;

	function startup(&$controller) {
		$this->params = &$controller->params;
		$this->BeFileHandler->startup($controller) ;
	}

	/**
	 * Uploads a file to location and create stream object.
	 * 
	 * @param array $dataStream
	 * @param string $formFileName
	 * @return mixed int|boolean, object_id if upload was successful, false otherwise.
	 */
	public function upload($dataStream=null, $formFileName="Filedata") {
		$result = false ;
		if (empty($this->params["form"][$formFileName]["name"])) {
			throw new BEditaException(__("No file in the form", true));
		}
		if ($this->params['form'][$formFileName]['error']) {
			throw new BEditaUploadPHPException($this->params['form'][$formFileName]['error']);
		}
		// Prepare data
		if (!empty($dataStream)) {
			$data = array_merge($dataStream, $this->params['form'][$formFileName]);
		} else {
			$data = $this->params['form'][$formFileName];
		}
		$data['original_name'] = $data['name'];
		$data['name'] = $this->BeFileHandler->buildNameFromFile($data['name']);
		$data['mime_type'] = $this->BeFileHandler->getMimeType($data);
		unset($data['type']);
		$data['file_size'] = $this->params['form'][$formFileName]['size'];
		unset($data['size']);
		
		if (!empty($this->params['form']['mediatype'])) {
			$data['mediatype'] = $this->params['form']['mediatype'];
		}
		
		$forceupload = (isset($this->params['form']['forceupload'])) ? ((boolean)$this->params['form']['forceupload']) : false ;

		if (empty($data['title']))
			$data['title'] = $data['original_name'];

		$data['uri']	= $data['tmp_name'] ;

		if (empty($data["status"]))
			$data["status"] = "on";

		unset($data['tmp_name']) ;
		unset($data['error']) ;

		$result = $this->BeFileHandler->save($data, $forceupload) ;
		
		return $result;
	}

	/**
	 * Create obj stream from URL. Form must have: url, title, lang.
	 * 
	 * @param string $dataURL
	 * @param boolean $clone
	 * @return mixed boolean|int, false if upload was unsuccessful, int $id otherwise
	 * @throws BEditaMediaProviderException
	 */
	public function uploadFromURL($dataURL, $clone=false) {
		$result = false ;
		$getInfoURL = false;
		$mediaProvider = ClassRegistry::init("Stream")->getMediaProvider($dataURL['url']);
		if(empty($dataURL['title'])) {
			$link = ClassRegistry::init("Link");
			$dataURL['title'] = $link->readHtmlTitle($dataURL['url']);
		}
		if (!empty($mediaProvider)) {
			$dataURL['provider'] = $mediaProvider["provider"];
			$dataURL['video_uid'] = $mediaProvider["video_uid"];
			$dataURL['uri'] = $mediaProvider["uri"];
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
			$dataURL['video_uid'] = null;
			$dataURL['uri'] = $dataURL["url"];
			$getInfoURL = true;
		}
		if (empty($dataURL["status"])) {
			$dataURL['status'] = "on";
		}
		if (!empty($this->params['form']['mediatype'])) {
			$dataURL['mediatype'] = $this->params['form']['mediatype'];
		}

		$dataURL['hash_file'] = null;
		$dataURL['original_name'] = null;

		unset($dataURL["url"]);
		$id = $this->BeFileHandler->save($dataURL, $clone, $getInfoURL) ;
		return $id;
	}

	/**
	 * Clone data for media
	 * This method is not intended to upload file!
	 *
	 * Its purpose is to clone $data into another multimedia object (or in the same if $cloneOnlyFile = true)
	 * If $cloneOnlyFile = true file data and file on filesystem will be cloned and $data will be saved on the same multimedia object
	 *
	 * If $data['uri'] is filled and it's not an url
	 * the original file located in Configure::read("mediaRoot") . $data["uri"] will be duplicate with different name and attached to cloned object
	 *
	 * @param array $data
	 * @param bool $cloneOnlyFile, true to clone only file (no new multimedia object will be created if $data['id'] is populated)
	 * @return mixed boolean|int, false if cloning was unsuccessful, int $id otherwise
	 */
	public function cloneMediaObject(&$data, $cloneOnlyFile = false) {
		if (!$cloneOnlyFile) {
			if (!empty($data["id"])) {
				unset($data["id"]);
			}
			if (!empty($data["nickname"])) {
				unset($data["nickname"]);
			}
		}
		if (preg_match(Configure::read("validate_resource.URL"), $data["uri"])) {
			$data["url"] = $data["uri"];
			return $this->uploadFromURL($data, true);
		} else {
		    if (!empty($data['uri'])) {
    			$data['uri'] = Configure::read("mediaRoot") . $data["uri"];
    			if (empty($data["file_size"])) {
    				$data["file_size"] = filesize($data["uri"]);
    			}
		    }
			if (!empty($this->params['form']['mediatype'])) {
				$data['mediatype'] = $this->params['form']['mediatype'];
			}
			if (!empty($data['original_name'])) {
				$data['name'] = $this->BeFileHandler->buildNameFromFile($data['original_name']);
			}
			return $this->BeFileHandler->save($data, true);
		}
	}

	/**
	 * Get thumbnail for media
	 * 
	 * @param array $data
	 * @return array
	 * @throws BEditaMediaProviderException
	 */
	public function getThumbnail($data) {
		if (!empty($data["thumbnail"]) && preg_match(Configure::read("validate_resource.URL"), $data["thumbnail"])) {
			$thumbnail = $data["thumbnail"]; 	
		} else {
			if (empty($data["provider"]) || empty($data["video_uid"])) {
				$url = (!empty($data['url']))? $data['url'] : $data['uri'];
				$mediaProvider = ClassRegistry::init("Stream")->getMediaProvider($data['url']);
				if (!empty($mediaProvider)) {
					$provider = $mediaProvider["provider"];
					$uid = $mediaProvider["video_uid"];
				}
			} else {
				$provider = $data["provider"];
				$uid = $data["video_uid"];
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
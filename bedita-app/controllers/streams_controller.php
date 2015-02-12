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
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class StreamsController extends AppController {
	
	var $components = array('BeTree','BeFileHandler', 'BeSecurity');
	var $uses = array('BEObject', 'Stream');
	var $helpers 	= array('BeTree', 'BeToolbar');
	
	/**
	 * get all or not related streams to an object 
	 *
	 * @param int $obj_id, if it's null get all streams else exclude related streams to $obj_id
	 */
	public function showStreams($obj_id=null, $text=null, $page = 1, $dim = 20) {
		$conf = Configure::getInstance();
		$ot  = array($conf->objectTypes['image']["id"],
					$conf->objectTypes['audio']["id"],
					$conf->objectTypes['video']["id"],
					$conf->objectTypes['b_e_file']["id"],
					$conf->objectTypes['application']["id"]
				);
		
		$relations_id = array();
		if (!empty($obj_id)) {
			$relations_id = $this->getRelatedStreamIDs($obj_id);
		}
		
		$filter["object_type_id"] = $ot;
		$filter["mediatype"] = true;
		if (!empty($text)) {
			$text = urldecode($text);
			$filter["query"] = $text;
		}
		
		$bedita_items = $this->BEObject->findObjects(null, null, null, $filter, $order=null, $dir=true, $page, $dim, false, $relations_id)  ;

		foreach($bedita_items['items'] as $key => $value) {
			$modelLoaded = $this->loadModelByObjectTypeId($value['object_type_id']);
			
			$modelLoaded->containLevel("minimum");

			if(($details = $modelLoaded->findById($value['id']))) {
				$details['filename'] = substr($details['uri'],strripos($details['uri'],"/")+1);
				$bedita_items['items'][$key] = array_merge($bedita_items['items'][$key], $details);	
			}
		}
		
		$this->layout = "ajax";
		$this->set("bedita_items",$bedita_items['items']);
		$this->set('toolbar', 		$bedita_items['toolbar']);
		$this->set("object_id", $obj_id);
		$this->set("streamSearched", $text);
	}
	
		
	/* Called by Ajax.
	 * Show multimedia object in the form page
	 * @param string $filename	File to show in the form page
	 */
	public function get_item_form($filename = null) {
		$filename = urldecode($this->params['form']['filename']) ;
		if(!($id = $this->Stream->getIdFromFilename($filename))) throw new BeditaException(sprintf(__("Error getting id object: %d", true), $id));
		$this->_get_item_form($id) ;
	}
	 
	/**
	 * Called by Ajax.
	 * Show multimedia object in the form page
	 * @param integer $id	Id dell'oggetto da linkare
	 */
	public function get_item_form_by_id($id =null) {
		$this->_get_item_form($this->params['form']['id']);
		if (!empty($this->params['form']['relation'])) {
			$this->set("relation", $this->params['form']['relation']);
		}
		if (!empty($this->params['form']['template'])) {
			$this->render($this->params['form']['template']);
		}
	}
	
	private function _get_item_form($id) {
		$conf  = Configure::getInstance() ;
		foreach ($this->params['form'] as $k =>$v) {
			$$k = $v ;
		}
		$rec = $this->BEObject->recursive ;
		$this->BEObject->recursive = -1 ;
		if(!($ret = $this->BEObject->read('object_type_id', $id))) 
			throw new BeditaException(sprintf(__("Error getting object: %d", true), $id));
		$this->BEObject->recursive = $rec ;
		$modelClass = $conf->objectTypes[$ret["BEObject"]["object_type_id"]]["model"];

		$model = $this->loadModelByType($modelClass);
		$model->containLevel("minimum");
		if(!($obj = $model->findById($id))) {
			 throw new BeditaException(sprintf(__("Error loading object: %d", true), $id));
		}
		
		$imagePath 	= $this->BeFileHandler->path($id) ;
		$imageURL 	= $this->BeFileHandler->url($id) ;
		// data for template
		$this->set('item',	$obj);
		$this->set('imagePath',	$imagePath);
		$this->set('imageUrl',	$imageURL);
		$this->layout = "ajax" ;
	}
	
	/**
	 * get related streams to an object and return an array of ids
	 *
	 * @param int $obj_id
	 * @param array $ot object types
	 * @return array of related streams to $obj_id
	 */
	private function getRelatedStreamIDs($obj_id, $ot=null) {
		$conf = Configure::getInstance();
		$relations_id = array();
		$object_type_id = $this->BEObject->findObjectTypeId($obj_id);
		
		$modelLoaded = $this->loadModelByObjectTypeId($object_type_id);
		$objRel = $modelLoaded->find("first",array(
												"contain" => array("BEObject" => "RelatedObject"),
												"conditions" => array("BEObject.id" => $obj_id)
											)
										);
		if (!empty($objRel["RelatedObject"])) {
			foreach ($objRel["RelatedObject"] as $rel) {
				$relations_id[] = $rel["object_id"];
			}
		}
		
		return $relations_id;
	}
	
	
}
?>
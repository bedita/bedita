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
 * Tags handling
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class TagsController extends ModulesController {

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'BeSecurity');
	var $uses = array('Category') ;
	
	protected $moduleName = 'tags';

	public function index($order = "label", $dir = 1) {
		$data = $this->Category->getTags(array(
			"cloud" => true,
			"order" => $order,
			"dir" => $dir
		));
		$this->set("numTags", count($data));
		$this->set('tags', $data);
		$this->set("order", $order);
		$this->set("dir", (($dir)? 0 : 1) );
	}

	public function view($id = null) {
		$tag = array();
		$referenced = array();
		
		if(isset($id)) {
			$tag = $this->Category->findById($id);
			if($tag == null || $tag === false) {
				throw new BeditaException(__("Error loading tag: ", true).$id);
			}
		
			$referenced = $this->Category->getContentsByTag($tag["name"]);
			$tag["weight"] = count($referenced);
		}
		
		$this->set('tag',	$tag);
		$this->set("referenced", $referenced);		
	 }

	public function save() {
		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		$exclude_id = (empty($this->data['id']))? null : $this->data['id'];
		if($this->Category->tagLabelPresent($this->data["label"],$exclude_id)) {
			throw new BeditaException(__("Tag already present", true)." - ".$this->data["label"]);
		}
		
		// format custom properties
		$this->Transaction->begin() ;
		if(!$this->Category->save($this->data)) {
			throw new BeditaException(__("Error saving tag", true), $this->Category->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Tag saved", true)." - ".$this->data["label"]);
		$this->eventInfo("tag [". $this->data["label"]."] saved");
	}

	public function delete() {
		$this->checkWriteModulePermission();
		
		if(empty($this->params["form"]["tags_selected"])) 
			throw new BeditaException( __("No tag selected", true));
			
		$this->Transaction->begin();
		foreach ($this->params["form"]["tags_selected"] as $id) {
			$this->Category->delete($id);
		}
		$this->Transaction->commit();
		
		$tagsListDeleted = implode(",", $this->params["form"]["tags_selected"]);
		
		$this->userInfoMessage(__("Tag deleted", true) . " -  " . $tagsListDeleted);
		$this->eventInfo("Tag $tagsListDeleted deleted");
	}

	public function deleteSelected() {
		$this->checkWriteModulePermission();
		if(empty($this->params["form"]["tags_selected"])) 
			throw new BeditaException( __("No tag selected", true));
		$this->Transaction->begin();
		foreach ($this->params["form"]["tags_selected"] as $id) {
			$this->Category->delete($id);
		}
		$this->Transaction->commit();
		$tagsListDeleted = implode(",", $this->params["form"]["tags_selected"]);
		$this->userInfoMessage(__("Tag deleted", true) . " -  " . $tagsListDeleted);
		$this->eventInfo("Tag $tagsListDeleted deleted");
	}

	public function listAllTags($href=false) {
		$this->layout = "ajax";
		$this->set("listTags",$this->Category->getTags(array("cloud" => true)));
		if ($href) 
			$this->set("href", true);
	}
	
	/**
	 * save tags from text area
	 *
	 */
	public function addMultipleTags() {
		$this->checkWriteModulePermission();
		
		if(empty($this->params["form"]["addtaglist"])) 
			throw new BeditaException( __("No tag in text area", true));
			
		$this->Transaction->begin();
		$tag_ids = $this->Category->saveTagList($this->params["form"]["addtaglist"]);
		$this->Transaction->commit();
		$listTagIds = implode(",", $tag_ids);
		$this->userInfoMessage(__("Tags saved", true)." - " . $listTagIds);
		$this->eventInfo("tags [". $listTagIds ."] saved");
		
	}
	
	public function changeStatus() {
		$this->checkWriteModulePermission();
		
		if(empty($this->params["form"]["tags_selected"])) 
			throw new BeditaException( __("No tag selected", true));
			
		$this->Transaction->begin();
		foreach ($this->params["form"]["tags_selected"] as $id) {
			$this->Category->id = $id;
			$this->Category->saveField("status", $this->params["form"]["newStatus"]); 
		}
		$this->Transaction->commit();
	}

    protected function forward($action, $result) {
        $moduleRedirect = array(
            'addMultipleTags' => array(
                'OK' => $this->referer(),
                'ERROR' => $this->referer()
            )
        );
        return $this->moduleForward($action, $result, $moduleRedirect);
    }

}

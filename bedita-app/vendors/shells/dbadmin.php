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

App::import('Core', 'String');
App::import('Core', 'Controller');
App::import('Controller', 'App'); // BeditaException

/**
 * Dbadmin shell: generic methods to check/fix some db data, for example translations, multimedia.
 * Some other methods to insert test objects data.
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class DbadminShell extends Shell {

	public function rebuildIndex() {
		
		$conf = Configure::getInstance();
		$searchText = ClassRegistry::init("SearchText");
		$beObj = ClassRegistry::init("BEObject");
		$beObj->contain();
		$res = $beObj->find('all',array("fields"=>array('id')));
		$this->hr();
		$this->out("Objects:");
		$this->hr();
		
		foreach ($res as $r) {
			$id = $r['BEObject']['id'];
			$type = $beObj->getType($id);
			$model = ClassRegistry::init($type);
			$model->{$model->primaryKey}=$id;
			$this->out("id: $id - type: $type");
			$searchText->deleteAll("object_id=".$id);
			$searchText->createSearchText($model);
		}
		// lang texts
		$this->hr();
		$this->out("Translations:");
		$this->hr();
		$langText = ClassRegistry::init("LangText");
		$res = $langText->find('all',array("fields"=>array('DISTINCT LangText.object_id, LangText.lang')));	
		foreach ($res as $r) {
			
			$lt = $langText->find('all',array("conditions"=>array("LangText.object_id"=>$r['LangText']['object_id'], 
												"LangText.lang" => $r['LangText']['lang'])));	
			$dataLang = array();
			foreach ($lt as $item) {
				$dataLang[] = $item['LangText'];
			}
			$this->out("object_id: " . $r['LangText']['object_id'] . " - lang: " . $r['LangText']['lang']);
			$searchText->saveLangTexts($dataLang);
		}
	}

	/**
	 * update lang texts 'status' using master object status....
	 * parameter 'lang' mandatory
	 */
	public function checkLangStatus() {
		
		$lang = $this->params['lang'];
		if(empty($lang)) {
			$this->out("Language parameter -lang mandatory");
			return;
		}
		$this->out('Checking language: '.$lang);
		$langText = ClassRegistry::init("LangText");
		$objTrans = $langText->find('all', 
			array('conditions'=> array("LangText.lang = '$lang'","LangText.name = 'title'")
			,'fields'=>array('BEObject.id', 'BEObject.status')));
		if(empty($objTrans)) {
			$this->out("No translations found");
			return;
		}
		foreach ($objTrans as $obj) {
			$objId = $obj['BEObject']['id'];
			$status = $langText->find(array("LangText.name = 'status'", "LangText.lang = '$lang'", 
				"LangText.object_id = $objId"));
			if($status === false) {
				$newStatus = $obj['BEObject']['status'];
				$l = array(
		                'object_id' => $objId,
		                'lang'      => $lang, 
		                'name'   => 'status',
						'text' => $newStatus
	                );
	            $langText->create();
	            if(!$langText->save($l)) 
	                    throw new BeditaException("Error saving lang text");
				$this->out("Added lang status for obj: $objId - $lang");
			}
		}
		$this->out("Lang texts status updated");
	}
	
	/**
	 * build hash_file for media objects
	 *
	 */
	public function buildHashMedia() {
		
		$conf = Configure::getInstance() ;
		$streamModel = ClassRegistry::init("Stream");
		$conditions = array();
		$countOperations = 0;
		
		if (!isset($this->params['all'])) {
			$conditions[] = "hash_file IS NULL"; 
		}
		
		$streams = $streamModel->find("all", array(
					"conditions" => $conditions
				)
			);
		
		$this->hr();
		$this->out("Build hash for media file:");
		$this->hr();
		
		if (!empty($streams)) {
			foreach ($streams as $s) {
				
				// if it's not an url build hash
				if (!preg_match($conf->validate_resource['URL'], $s["Stream"]["path"])) {
					$hash = hash_file("md5", $conf->mediaRoot . $s["Stream"]["path"]);
					if ($hash === false)
						 throw new BeditaException(__("Building Hash file failed", true));
					$streamModel->id = $s["Stream"]["id"];
					if (!$streamModel->saveField("hash_file", $hash))
						throw new BeditaException(__("Error saving hash_file field", true));
					$this->out("file: " . $conf->mediaRoot . $s["Stream"]["path"] . ", hash: " . $hash);
					$countOperations++;
				}
				
			}
		}
		$this->out($countOperations . " rows on db updated");
		$this->out("done");
	}
	
	public function setImageDimensions() {
		
		$conf = Configure::getInstance() ;
		$imageModel = ClassRegistry::init("Image");
		$conditions = array();
		$countOperations = 0;
		
		if (!isset($this->params['all'])) {
			$conditions[] = "width IS NULL OR height IS NULL"; 
		}
		
		$images = $imageModel->find("all", array(
					"conditions" => $conditions,
					"contain" => array("Stream")
				)
			);
		
		$this->hr();
		$this->out("Check image size and write in db");
		$this->hr();
		
		if (!empty($images)) {
			foreach ($images as $i) {
				
				// if it's not an url get image size
				if (!preg_match($conf->validate_resource['URL'], $i["path"])) {
					
					if ( !$imageSize =@ getimagesize($conf->mediaRoot . $i['path']) )
						throw new BeditaException(__("Get image size failed", true));
					
					if ($imageSize[0] == 0  || $imageSize[1] == 0)
						throw new BeditaException(__("Can't get dimension for " . $i['path'], true));
						
					$imageModel->id = $i["id"];
					if (!$imageModel->saveField("width", $imageSize[0]))
						throw new BeditaException(__("Error saving width field", true));
					if (!$imageModel->saveField("height", $imageSize[1]))
						throw new BeditaException(__("Error saving height field", true));
					$this->out("file: " . $conf->mediaRoot . $i["path"] . ", dimension: " . $imageSize[0] . "x" . $imageSize[0] ." pixels");
					$countOperations++;
				}
				
			}
		}
		$this->out($countOperations . " rows on db updated");
		$this->out("done");
	}

	public function annotate() {
		if (!isset($this->params['id'])) {
			$id = $this->in("Select object id to annotate: ");
		} else {
			$id = $this->params['id'];
		}
		$objModel = ClassRegistry::init("BEObject");
		$objData = $objModel->findById($id);
		if(empty($objData)) {
			$this->out("Object id " . $id . " not found. Bye.");
			return;
		}
		if (!isset($this->params['type'])) {
			$type = $this->in("Select annotation type: editor[n]ote/[c]omment");
		} else {
			$type = $this->params['type'];		
		}
		
		if(!in_array($type, array("editornote", "comment"))) {
			if($type === "c")    {
				$type = "comment";
			} else if($type === "e" || $type === "n") {
				$type = "editornote";
			} else {
				$this->out("Wrong annotation type: ". $type);
				return;
			}
		}
		
		$inputs = array("comment" => array("title", "description", "author", "email", "url"),
						"editornote" => array("title", "description"));
		
		$this->hr();
		$this->out("please provide [$type] input data");
		$this->hr();
		$data = array("object_id" => $id, "ip_created" => "127.0.0.1", "user_created" => 1, "user_modified" => 1);
		foreach ($inputs[$type] as $req) {
			$resp = $this->in("[$req]:");
			$data[$req]=$resp;			
		}
		$data["status"] = "on";
		$conf = Configure::getInstance() ;
		$modelClass = $conf->objectTypes[$type]['model'];
		$model = ClassRegistry::init($modelClass);
		if(!$model->save($data)) {
			$this->out("Error saving data for model ".$modelClass);
			return;
		}
		$this->out("$type saved. Bye");
	}
	
	public function updateVideoThumb() {
		
		$conf = Configure::getInstance() ;
		App::import('Component', 'BeBlip');
		$this->BeBlip = new BeBlipComponent();
		
		$videoModel = ClassRegistry::init("Video");
		$conditions = array();
		$countOperations = 0;
		
		if (!isset($this->params['all'])) {
			$conditions[] = "thumbnail IS NULL"; 
		}
		
		$videos = $videoModel->find("all", array(
					"conditions" => $conditions,
					"contain" => array("Stream")
				)
			);
		
		$this->hr();
		$this->out("Update video thumbnail");
		$this->hr();
		
		if (!empty($videos)) {
			foreach ($videos as $v) {
				if ($v["provider"] == "youtube") {
					$thumbnail	= sprintf($conf->provider_params["youtube"]["urlthumb"], $v['uid']);
				} elseif ($v["provider"] == "blip") {
					if(!($this->BeBlip->getInfoVideo($v["uid"]) )) {
						throw new BEditaMediaProviderException(__("Multimedia  not found",true)) ;
					}
					$thumbnail = $this->BeBlip->info['thumbnailUrl'];
				}
				
				if (!empty($v["provider"])) {
					$videoModel->id = $v["id"];
					if (!$videoModel->saveField("thumbnail", $thumbnail))
						throw new BeditaException(__("Error saving thumbnail field", true));
					
					$this->out("video: " . $v["path"] . ", thumbnail: " . $thumbnail);
					$countOperations++;
				}
				
			}
		}
		$this->out($countOperations . " rows on db updated");
		$this->out("done");
	}

	public function orphans() {
		if (!isset($this->params['type'])) {
			$this->out("object type is mandatory");
		}
		$type = $this->params['type'];
		$objTypeId = Configure::read("objectTypes." . $type . ".id");
		if(empty($objTypeId)) {
			$this->out("object type " . $type . " not found");
		}
		$modelType = Configure::read("objectTypes." . $type . ".model");
		$model = ClassRegistry::init($modelType);
		$objModel = ClassRegistry::init("BEObject");
		$objects = $objModel->find("all", array(
		 						"contain" 	=> array(),
		 						"fields"	=> array("id", "title", "nickname"),
 								"conditions" => array("object_type_id" => $objTypeId)));
		$treeModel = ClassRegistry::init("Tree");
		$found = false;
		foreach ($objects as $o) {
			$obj = $o["BEObject"];
			$id = $treeModel->field("id", array("id" => $obj["id"]));
			if(empty($id)) {
				if(!$found) {
					$this->out("orphans found");
					$found = true;
				}
				$this->out("orphan id: " . $obj["id"] . " - title: '" . $obj["title"] . 
					"' - nickname: " . $obj["nickname"]);
				$res = $this->in("delete object? [y/n]");
				if($res != "y") {
					$this->out("$type not deleted, id: " . $obj["id"]);
				} else {
					if(!$model->delete($obj["id"])) {
						throw new BeditaException("Error deleting object: " . $obj["id"]);
					}
					$this->out("$type with id: " . $obj["id"] . " deleted");
				}
				
			}
		}
		if(!$found) {
			$this->out("No orphans found of type ". $type);
		}
	}
	
	function help() {
		$this->out('Available functions:');
        $this->out('1. rebuildIndex: rebuild search texts index');
  		$this->out(' ');
 		$this->out("2. checkLangStatus: update lang texts 'status' using master object status");
        $this->out(' ');
        $this->out('    Usage: checkLangStatus -lang <lang>');
        $this->out(' ');
        $this->out("3. buildHashMedia: insert 'hash_file' for media file.");
        $this->out(' ');
        $this->out('    Usage: buildHashMedia [-all]');
        $this->out(' ');
        $this->out("    -all \t rebuild all 'hash_file'");
        $this->out(' ');
        $this->out("4. setImageDimensions: get images size and update db");
        $this->out(' ');
        $this->out('    Usage: setImageDimensions [-all]');
        $this->out(' ');
        $this->out("    -all \t set dimension for all images, otherwise only for image with dimensions no defined in db");
        $this->out(' ');
        $this->out("5. updateVideoThumb: update video thumbnail from external provider");
        $this->out(' ');
        $this->out('    Usage: updateVideoThumb [-id]');
        $this->out(' ');
        $this->out("    -all \t update all video, otherwise only video with no thumbnail defined in db");
        $this->out(' ');
        $this->out("6. annotate: add comment/editor note to object");
        $this->out(' ');
        $this->out('    Usage: annotate [-id <object-it>] [-type <annotation-type>] ');
        $this->out(' ');
        $this->out("    -id \t object id to annotate");
        $this->out("    -type \t 'editornote' or 'comment'");
        $this->out(' ');
        $this->out("7. orphans: searche and remove orphaned objects (not in tree)");
        $this->out(' ');
        $this->out('    Usage: orphans -type <model-type> ');
        $this->out(' ');
        $this->out("    -type \t model type like 'section' or 'document'");
        $this->out(' ');
	}
	
}

?>
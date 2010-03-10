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

// load cached configurations
App::import("File", "BeLib", true, array(BEDITA_LIBS), "be_lib.php");
BeLib::getObject("BeConfigure")->initConfig();

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
class DbadminShell extends BeditaBaseShell {

	public function rebuildIndex() {
		
		$conf = Configure::getInstance();
		$searchText = ClassRegistry::init("SearchText");
		$beObj = ClassRegistry::init("BEObject");
		$beObj->contain();
		$res = $beObj->find('all',array("fields"=>array('id')));
		$this->hr();
		$this->out("Objects:");
		$this->hr();

		$failed = array();

		foreach ($res as $r) {
			$id = $r['BEObject']['id'];
			$type = $beObj->getType($id);
			$model = ClassRegistry::init($type);
			$model->{$model->primaryKey}=$id;
			$this->out("id: $id - type: $type");
			$searchText->deleteAll("object_id=".$id);
			try {
				$searchText->createSearchText($model);
			} catch (BeditaException $ex) {
				$this->out("ERROR: " . $ex->getMessage());
				$this->out("Probably there is an inconsistency in the tables that involve object with id " . $id);
				$this->out("");
				$failed[] = array("id" => $id, "error" => $ex->getMessage(), "check" => "Check that exists a row with id " . $id . " in all tables that involve the object.");
			}
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

		if (!empty($failed)) {
			$this->out("");
			$this->hr();
			$this->hr();
			$this->out("ERRORS occured rebuilding index");
			$this->hr();
			$this->hr();
			foreach($failed as $f) {
				$this->out("id: " . $f["id"]);
				$this->out("error: " . $f["error"]);
				$this->out("suggestion: " . $f["check"]);
				$this->hr();
			}
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

				if ($imageModel->setImageDimArray($i)) {
					$this->out("file: " . $conf->mediaRoot . $i["path"] . 
						", dimension: " . $i["width"] . "x" . $i["height"] ." pixels");
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
		
		if(!in_array($type, array("editor_note", "comment"))) {
			if($type === "c")    {
				$type = "comment";
			} else if($type === "e" || $type === "n") {
				$type = "editor_note";
			} else {
				$this->out("Wrong annotation type: ". $type);
				return;
			}
		}
		
		$inputs = array("comment" => array("title", "description", "author", "email", "url"),
						"editor_note" => array("title", "description"));
		
		$this->hr();
		$this->out("please provide [$type] input data");
		$this->hr();
		$data = array("object_id" => $id, "ip_created" => "127.0.0.1", "user_created" => 1, "user_modified" => 1);
		foreach ($inputs[$type] as $req) {
			$resp = $this->in("[$req]:");
			$data[$req]=$resp;			
		}
		$data["status"] = "on";
		//$conf = Configure::getInstance() ;
		//pr($conf->objectTypes);exit;
		//$modelClass = $conf->objectTypes[$type]['model'];
		$modelClass = Inflector::camelize($type);
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
					$thumbnail	= sprintf($conf->media_providers["youtube"]["params"]["urlthumb"], $v['uid']);
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
	
	public function importCsv() {
		if (!isset($this->params['f'])) {
			$this->out("file to import is mandatory");
			return;
		}
		if(!file_exists($this->params['f'])) {
			$this->out("csv file " . $this->params['f'] . " not found");
			return;
		}
		
		if (!isset($this->params['type'])) {
			$this->out("object type is mandatory");
			return;
		}
		$type = $this->params['type'];
		$objTypeId = Configure::read("objectTypes." . $type . ".id");
		if(empty($objTypeId)) {
			$this->out("object type " . $type . " not found");
		}
		$modelType = Configure::read("objectTypes." . $type . ".model");
		$model = ClassRegistry::init($modelType);

		$defaults = array( 
			"status" => "on",
			"user_created" => "1",
			"user_modified" => "1",
			"ip_created" => "127.0.0.1",
		);
		
		$this->out("Importing from " . $this->params['f'] . " objects of type $type");
		$this->out("........ ");
		$row = 1;
		$handle = fopen($this->params['f'], "r");
		// read header
		$keys = fgetcsv($handle, 1000, ",");
		$numKeys = count($keys);
		$data = array();
		while (($fields = fgetcsv($handle, 1000, ",")) !== FALSE) {
	    	$row++;
			for ($c=0; $c < $numKeys; $c++) {
				$data[$keys[$c]] = empty($fields[$c]) ? null : $fields[$c]; 
			}
			$model->create();
			$d = array_merge($defaults, $data);
			if(!$model->save($d)) {
				throw new BeditaException("Error saving object: " . print_r($data, true) . " \nrow: $row \nvalidation: " . print_r($model->validationErrors, true));
			}
		}
		fclose($handle);
		$nObj = $row-1;
		$this->out("Done. $nObj objects of type " . $type . " inserted.");
	}
	
	public function updateStreamFields() {
		$streamModel = ClassRegistry::init("Stream");
		$streams = $streamModel->find("all");
		if (!empty($streams)) {
			foreach ($streams as $s) {
				if ($streamModel->updateStreamFields($s["Stream"]["id"])) {
					$this->out("stream ".$s["Stream"]["id"]. " updated");
				}
			}
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
        $this->out("    -type \t 'editor_note' or 'comment'");
        $this->out(' ');
        $this->out("7. orphans: searche and remove orphaned objects (not in tree)");
        $this->out(' ');
        $this->out('    Usage: orphans -type <model-type> ');
        $this->out(' ');
        $this->out("    -type \t model type like 'section' or 'document'");
        $this->out(' ');
        $this->out("8. importCsv: import objects from csv file");
        $this->out(' ');
        $this->out('    Usage: importCsv -f <csv-file> -type <model-type> ');
        $this->out(' ');
        $this->out("    -f \t csv file path");
        $this->out("    -type \t model type like 'document' or 'event' to import");
        $this->out(' ');
		$this->out('9. updateStreamFields: update name (if empty), mime_type (if empty), size and hash_file fields of streams table');
  		$this->out(' ');
	}
	
}

?>
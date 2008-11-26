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
 * 
 * @link			http://www.bedita.com
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
				if (!preg_match($conf->validate_resorce['URL'], $s["Stream"]["path"])) {
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
	}
	
}

?>
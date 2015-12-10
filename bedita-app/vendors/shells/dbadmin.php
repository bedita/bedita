<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2011 ChannelWeb Srl, Chialab Srl
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

App::import('Core', 'String');
App::import('Core', 'Controller');
App::import('Model', 'CakeSchema');

require_once 'bedita_base.php';

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

		$options = array();
		$engine = Configure::read("searchEngine");
		if (!empty($this->params['engine'])) {
		    $engine = $this->params['engine'];
		    Configure::write("searchEngine", $engine);
		}
        if (!empty($engine)) {
		    $indexModel = ClassRegistry::init($engine);
			if(!$indexModel) {
				$this->out("Engine not found: " . $engine);
				return;
			} else {
				$this->out("Using search engine: " . $engine);
			}
		}
        // if single id is selected, index only this id
        if (!empty($this->params['id'])) {
            $id = $this->params['id'];
            $this->out("Indexing object $id");
            $type = ClassRegistry::init("BEObject")->getType($id);
            $model = ClassRegistry::init($type);
            $model->{$model->primaryKey} = $id;
            $searchText = ClassRegistry::init("SearchText");
            if (!$searchText->deleteAll("object_id=".$id)) {
                throw new BeditaException(__("Error deleting all search text indexed for object", true) . " " . $id);
            }
            $searchText->createSearchText($model);
            $this->out("Index for object '$id' created.");
            return;
        }
        if (!empty($this->params['type'])) {
            $t = trim(strtolower($this->params['type']));
            $objTypeId = Configure::read('objectTypes.' . $t . '.id');
            if (empty($objTypeId)) {
                $this->out('Object type not found: ' . $t);
                return;
            }
            $options['type'] = $objTypeId;
        }
        
		if (!empty($this->params['delete'])) {
		    $options['delete'] = true;
		}
		$options['returnOnlyFailed'] = (!isset($this->params['verbose']))? true : false;
		$options['log'] = (!empty($this->params['log']))? true : false;
		$this->hr();
		$this->out("Rebuilding indexes... the operation could be slow");
		$this->hr();
		$response = ClassRegistry::init("Utility")->call('rebuildIndex', $options);
		$result = $response['results'];
		$this->out("");
		$this->out("");

		if (!empty($result['success'])) {
			$this->out('Index rebuilt successfully');
			$this->hr();
			foreach ($result['success'] as $v) {
				$this->out("id: " . $v['id']);
			}
			$this->hr();
			$this->out("");
			$this->out("");
		}

		if (!empty($result['langTextSuccess'])) {
			$this->out('Index rebuilt successfully for translations');
			$this->hr();
			foreach ($result['langTextSuccess'] as $failed) {
				foreach ($failed as $k => $v) {
					$this->out($k . ": " . $v);
				}
			}
			$this->hr();
			$this->out("");
			$this->out("");
		}

		if (!empty($result['failed'])) {
			$this->out('ERRORS occured rebuilding index');
			$this->hr();
			foreach ($result['failed'] as $v) {
				$this->out("id: " . $v['id'] . " - error:" . $v['error']);
			}
			$this->hr();
			$res = $this->in("Do you want to check consistency in all tables that involve objects? [y/n]");
			if($res == "y") {
				$this->hr();
				$this->out("CHECKING CONSISTENCY...");
				$this->checkConsistency();
			}
			$this->out("");
			$this->out("");
		}

		if (!empty($result['langTextFailed'])) {
			$this->out('ERRORS occured rebuilding index for lang_texts table (object translations fields)');
			$this->hr();
			foreach ($result['langTextFailed'] as $failed) {
				foreach ($failed as $k => $v) {
					$this->out($k . ": " . $v);
				}
			}
			$this->hr();
			$this->out("");
			$this->out("");
		}

		$this->out($response['message']);
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
				if (!preg_match($conf->validate_resource['URL'], $s["Stream"]["uri"])) {
					$hash = hash_file("md5", $conf->mediaRoot . $s["Stream"]["uri"]);
					if ($hash === false)
						 throw new BeditaException(__("Building Hash file failed", true));
					$streamModel->id = $s["Stream"]["id"];
					if (!$streamModel->saveField("hash_file", $hash))
						throw new BeditaException(__("Error saving hash_file field", true));
					$this->out("file: " . $conf->mediaRoot . $s["Stream"]["uri"] . ", hash: " . $hash);
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
					$this->out("file: " . $conf->mediaRoot . $i["uri"] .
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
					$thumbnail	= sprintf($conf->media_providers["youtube"]["params"]["urlthumb"], $v['video_uid']);
				}

				if (!empty($v["provider"])) {
					$videoModel->id = $v["id"];
					if (!$videoModel->saveField("thumbnail", $thumbnail))
						throw new BeditaException(__("Error saving thumbnail field", true));

					$this->out("video: " . $v["uri"] . ", thumbnail: " . $thumbnail);
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

	public function importXml() {
		if (!isset($this->params['f'])) {
			$this->out("file to import is mandatory");
			return;
		}
		if(!file_exists($this->params['f'])) {
			$this->out("XML file " . $this->params['f'] . " not found");
			return;
		}

		$secId = null;
		if (isset($this->params['s'])) {
			$secId = $this->params['s'];
		}
		$defaults = array(
			"user_created" => "1",
			"user_modified" => "1",
			"ip_created" => "127.0.0.1",
		);

		$this->out("Importing from " . $this->params['f']);
		$this->hr();

		App::import("Core", "Xml");
		$xml = new XML(file_get_contents($this->params['f']));
		$treeModel = ClassRegistry::init("Tree");
		$nObj = 0;
		$parsed = set::reverse($xml);
		$objs = array();
		if(!empty($parsed["Section"]["ChildContents"])) {
			$objs = $parsed["Section"]["ChildContents"];
		} else if(!empty($parsed["Bedita"]["Objects"])) {
			$objs = $parsed["Bedita"]["Objects"];
		} else {
			$this->out("No contents found.");
			return;
		}
		if(!is_int(key($objs))) {
			$objs = array($objs);
		}
		foreach ($objs as $data) {

			$objTypeId = isset($data['ObjectType']['name']) ?
				Configure::read("objectTypes." . $data['ObjectType']['name'] . ".id") : $data['object_type_id'];
			$modelType = Configure::read("objectTypes." . $objTypeId . ".model");
			$model = ClassRegistry::init($modelType);
			$data = array_merge($data, $defaults);
			unset($data["id"]);
			$data["object_type_id"] = $objTypeId;
			$model->create();
			if(!$model->save($data)) {
				throw new BeditaException("Error saving object - " . print_r($data, true) .
					" - validation: " . print_r($model->validationErrors, true));
			}
			if(!empty($secId)) {
				$treeModel->appendChild($model->id, $secId);
			}
			$this->out($modelType . " created - id " . $model->id . " - title '" . $data["title"] . "'");
			$nObj++;
		}
		$this->out("Done. $nObj objects inserted.");
	}

	public function exportXml() {
		if (!isset($this->params['o'])) {
			$this->out("output file is mandatory");
			return;
		}
		if (!isset($this->params['t'])) {
			$this->out("object type is mandatory");
			return;
		}
		$conf = Configure::getInstance() ;
		$type = $this->params['t'];
		$model = ClassRegistry::init($conf->objectTypes[$type]['model']);
		$this->out("Exporting to " . $this->params['o']);
		$this->hr();
		$model->containLevel("minimum");
		$res = $model->find('all', array('conditions' => array(
						'BEObject.object_type_id' => $conf->objectTypes[$type]['id']
						)
					)
			);
		$this->out("Found " . count($res) . " objects of type " . $this->params['t']);
		$out = array();
		$options = array('attributes' => false, 'format' => 'attributes', 'header' => false);
		$out["Bedita"]["Objects"] = $res;
		App::import("Core", "Xml");
		$xml =& new Xml($out, $options);
		$xmlOut = $xml->toString();
		file_put_contents($this->params['o'], $xmlOut);
		$this->out("Done.");
	}



	public function updateStreamFields() {
		$options = array();
		if (!empty($this->params['id'])) {
			$options['id'] = $this->params['id'];
		}
		$response = ClassRegistry::init("Utility")->call('updateStreamFields', $options);
		$streamsUpdated = $response['results'];
		foreach ($streamsUpdated as $s) {
			$this->out("stream ".$s["Stream"]["id"]. " updated");
		}
	}

	public function checkConsistency() {
		$beObj = ClassRegistry::init("BEObject");
		$beObj->contain();
		$all_objects = $beObj->find('all',array("fields" => array('id')));
		$this->hr();
		$this->out("Checking objects");
		$this->out(sizeof($all_objects) . " objects found");
		$deleted = 0;
		$inconsistent = 0;
		foreach($all_objects as $k => $data) {
			$type = $beObj->getType($data['BEObject']['id']);
			$model = ClassRegistry::init($type);
			$count = $model->find("count",
				array("conditions"=>array("$type.id"=>$data['BEObject']['id']))
			);
			if($count === 0) {
				$inconsistent++;
				$this->out("INCONSISTENCY found");
				$res = $this->in("0 count for obj " . $data['BEObject']['id'] . " ($type). Do you want to delete object " . $data['BEObject']['id'] . "? [y/n]");
				if($res == "y") {
					$this->out("Deleting object " . $data['BEObject']['id'] . " ...");
					$beObj->delete($data['BEObject']['id']);
					$this->out("Object " . $data['BEObject']['id'] . " deleted");
					$deleted++;
				}
			}
		}
		$this->out("Objects not consistent: $inconsistent; objects deleted: $deleted");
		// check
		$this->hr();
		$this->out("Checking schema");
		$beSchema = ClassRegistry::init("BeSchema");
		$db = ConnectionManager::getDataSource('default');
		$beSchema->checkSequences($db);
		$this->hr();
		$this->out("Done");
	}

	function updateTreeRoot() {
		$treeModel = ClassRegistry::init("Tree");
		$conditions = array();
		$rows = $treeModel->find("all", array("conditions" => $conditions));
		if (!empty($rows)) {
			foreach ($rows as $r) {
				$treeModel->create();
				$r["Tree"]["area_id"] = $treeModel->getAreaIdByPath($r["Tree"]["parent_path"]);
				if (!$treeModel->save($r)) {
					$this->out("Error updating row with object_path " . $r["Tree"]["object_path"]);
				} else {
					$this->out("Row with object_path " . $r["Tree"]["object_path"] . " updated");
				}
			}
		}
	}

	public function checkDbNames() {
		// load reserved keywords
		$sqlCfgPath = APP . DS . "config" . DS. "sql";
		$sqlReservedFile = $sqlCfgPath . DS  . "reserved_words.php";
		$this->out("Using SQL reserved keywords file: $sqlReservedFile");
		require_once($sqlReservedFile);

		// load schema data
		$tables = array();
		$badNames = array();
		if(isset($this->params['schema'])) {
			$schemaFile = $sqlCfgPath . DS  . "schema.php";
			$this->out("Using BEdita schema file: $schemaFile");
			require_once($schemaFile);
			$schema = new BeditaAppSchema();
			$tables = $schema->tables;
		} else {
			$db = ConnectionManager::getDataSource('default');
			$this->out("Reading from database: ". $db->config['driver'] . " [host=" .
				$db->config['host'] .", database=". $db->config['database']."]");
			$schema = new CakeSchema();
			$schemaTabs = $schema->read();
			$tables = $schemaTabs['tables'];
		}
		$this->hr();
		foreach ($tables as $tab => $cols) {
			if(is_array($cols)) {
				if(in_array($tab, $reserved_words)) {
					$this->out("bad table name: $tab");
					if(!in_array($n, $badNames)) {
						$badNames[]=$n;
					}
				}
				foreach ($cols as $n => $attr) {
					if($n != "indexes" && in_array($n, $reserved_words)) {
						$this->out("bad column name: $tab.$n");
						if(!in_array($n, $badNames)) {
							$badNames[]=$n;
						}
					}
				}
			}
		}
		if(!empty($badNames)) {
			sort($badNames);
			$bad = "";
			foreach ($badNames as $b) {
				$bad .= " " . $b;
			}
			$this->hr();
			$this->out("bad names found: $bad");
		}
	}

	public function schemaDoc() {

		$db = ConnectionManager::getDataSource('default');
		$this->out("Reading from database: ". $db->config['driver'] . " [host=" .
			$db->config['host'] .", database=". $db->config['database']."]");
		$beSchema = ClassRegistry::init("BeSchema");
		$tables = $beSchema->tableList();
		$dbName = $db->config['database'];
		$db2 = ConnectionManager::getDataSource('informationSchema');
		$doc = array();
		foreach ($tables as $t) {
		  	$res = $db2->query("SELECT column_name, column_comment from columns where table_name='$t' and table_schema='$dbName'");
			foreach ($res as $r) {
				if(!empty($r["columns"]["column_comment"])) {
					$colName = $r["columns"]["column_name"];
					$doc[$t][$colName] = $r["columns"]["column_comment"];
				}
			}

			$res2 = $db2->query("SELECT table_comment from tables where table_name='$t' and table_schema='$dbName'");
			$tComm = explode(';', $res2[0]['tables']['table_comment']);
			$doc["tables"][$t] = $tComm[0];
		}

		$sqlPath = APP ."config" . DS . "sql";
		$h = fopen($sqlPath . DS . "schema_doc.php", 'w');
		fwrite($h, "<?php\n// BEdita DB schema documentation\n\$doc = array();\n\n");
		foreach ($tables as $t) {
			if(strpos($t, "cake_") === false) {
				fwrite($h, "// table: $t\n");
				fwrite($h, "\$doc['tables']['$t'] = '" . $doc["tables"][$t]. "';");
				$colDoc = empty($doc[$t]) ? array() : $doc[$t];
				fwrite($h, "\n\$doc['$t'] = " . var_export($colDoc, true) . ";");
				fwrite($h, "\n\n");
			}
		}
		fwrite($h, "?>");
		fclose($h);
	}

	function schemaComments() {

		$schemaDocPath = APP ."config" . DS . "sql" . DS . "schema_doc.php";
		include_once ($schemaDocPath);

		$schema = file(APP ."config" . DS . "sql" . DS . "bedita_mysql_test.sql");
		$currTable = null;
		foreach ($schema as $line) {
			$line = trim($line);
			$out = $line;
			if(empty($currTable)) {
				$f = "CREATE TABLE";
				$p = strpos($line, $f);
				if($p !== false) {
					$t = explode(" ", substr($line, $p+strlen($f)));
					$currTable = $t[0];
				}
			} else {
				$p = strpos($line, ")");
				$p1 = strpos($line, ";");
				if($p !== false && $p1 !== false) {
					$t = explode(";", $line);
					if(!empty($doc["tables"][$currTable])) {
						$out = $t[0] . " COMMENT='" . $doc["tables"][$currTable] . "';";
					}
					$currTable = null;
				} else {
					$t = explode(" ", trim($line));
					if(!empty($doc[$currTable][$t[0]])) {
						$c = $doc[$currTable][$t[0]];
						$t1 = explode(",", $line);
						$out = $t1[0] . " COMMENT '$c',";
					}
				}
			}
			$this->out($out);
		}

		// POSTGRES
/*		foreach ($doc["tables"] as $name => $comm) {

			$c = "COMMENT ON TABLE $name IS '$comm';";
			$this->out($c);
			foreach($doc[$name] as $col => $colComm) {
				$c = "COMMENT ON COLUMN $name.$col IS '$colComm';";
				$this->out($c);
			}
		}
*/

	}


	/**
	 * Cleanup old items (log/job tables)
	 */
	public function cleanup() {

		if(empty($this->params['days'])) {
			$this->out("Parameter -days mandatory");
			return;
		}
		$days = $this->params['days'];
		$tsLimit = time() - ($days * 24 * 60 * 60);
        $dateLimit = date('Y-m-d', $tsLimit);
		$this->out("Removing items older than $days days, preserving from $dateLimit");

		// remove event logs
		$this->out("Removing from event_logs");
		$eventLog = ClassRegistry::init("EventLog");
		$res = $eventLog->deleteAll("created <= '$dateLimit'");
		if($res == false) {
			$this->out("Error removin items");
			return;
		}
		$this->out("Removing from mail_jobs");
		$mailJob = ClassRegistry::init("MailJob");
		$res = $mailJob->deleteAll("created <= '$dateLimit'");
		if($res == false) {
			$this->out("Error removing items");
			return;
		}
		$this->out("Removing from mail_logs");
		$mailLog = ClassRegistry::init("MailLog");
		$res = $mailLog->deleteAll("created <= '$dateLimit'");
		if($res == false) {
			$this->out("Error removing items");
			return;
		}

		$this->out("Done");

	}

	/**
	 * Clears media cache
	 */
	public function clearMediaCache() {
		$options['log'] = (!empty($this->params['log']))? true : false;
		$response = ClassRegistry::init("Utility")->call('clearMediaCache', $options);
		$results = $response['results'];
		if ($results === false) {
			$this->out("No streams found");
			return;
		}
		if (!empty($results['failed'])) {
			foreach ($results['failed'] as $item) {
				$this->out($item['error']);
			}
		}
		$this->out($response['message']);
	}

	/**
	 * Massive removal of object type
	 */
	public function massRemove() {

		if(empty($this->params['type'])) {
			$this->out("Parameter -type mandatory [object type]");
			return;
		}

		$type = $this->params['type'];
		$objTypeId = Configure::read("objectTypes." . $type . ".id");
		if(empty($objTypeId)) {
			$this->out("object type " . $type . " not found");
		}

		$model = ClassRegistry::init("BEObject");

		$modelType = Configure::read("objectTypes." . $type . ".model");
		$this->out("Removing all " . $modelType ." from your instance");
		$ans = $this->in("Proceed??? [y/n]");
		if($ans != "y") {
			$this->out("Bye");
			return;
		}
		$res = $model->deleteAll("BEObject.object_type_id = '$objTypeId'");
		if($res == false) {
			$this->out("Error removing items");
			return;
		}
		$this->out("Done");
	}


	public function updateCategoryName() {
		$categoryModel = ClassRegistry::init("Category");
		$categoryModel->Behaviors->disable("CompactResult");
		$conditions = array();
		if (!empty($this->params["objectType"])) {
			$conditions["object_type_id"] = Configure::read("objectTypes." . $this->params["objectType"] . ".id");
		}
		$categories = $categoryModel->find("all", array(
			"order" => "Category.id ASC",
			"conditions" => $conditions
		));

		if (!empty($categories)) {
			$this->out("Updating category unique name:");
			$this->hr();
			foreach ($categories as $cat) {
				$text = "update unique name of ";
				$text .= (!empty($cat["Category"]["object_type_id"]))? "category" : "tag";
				$text .= " id:" . $cat["Category"]["id"] . " label:\"" . $cat["Category"]["label"] . "\"";
				$categoryModel->create();

				if ($categoryModel->save($cat)) {
					$this->out($text . " done");
				} else {
					$this->out($text . " failed");
				}
			}
		} else {
			$this->out("No categories or tags found.");
		}
		$this->out("Done.");
	}

	function clonePublication() {
		if (empty($this->params["id"])) {
			$this->out("ERROR: Missing Publication id.");
			return;
		}
		$options = array("keepUserCreated" => true);
		if (!empty($this->params["nicknameSuffix"])) {
			$options["nicknameSuffix"] = "-" . $this->params["nicknameSuffix"];
		}
		if (!empty($this->params["keepTitle"])) {
			$options["keepTitle"] = true;
		}
		$this->out("Start to clone Publication with id " . $this->params["id"]);
		$this->out("WARNING: the operation can take several minutes");

		$dbCfg = "default";
		App::import('Component', 'Transaction');
		$transaction = new TransactionComponent($dbCfg);
		$transaction->begin();
		$Tree = ClassRegistry::init("Tree");
		$idConversion = $Tree->cloneStructure($this->params["id"], $options);

		foreach ($idConversion as $originalId => $cloneId) {
			$objectType = ClassRegistry::init("BEObject")->getType($originalId);
			$originalChildrenCount = $Tree->find("count", array(
				"conditions" => array("parent_id" => $originalId)
			));
			$cloneChildrenCount = $Tree->find("count", array(
				"conditions" => array("parent_id" => $cloneId)
			));
			$this->out($objectType . " with id = " . $originalId . " (" . $originalChildrenCount . " children) cloned to id = " . $cloneId . " (" . $cloneChildrenCount . " children)");
			if ($originalChildrenCount != $cloneChildrenCount) {
				$this->hr();
				// log errors
				$Tree->bindModel(array("belongsTo" => array("BEObject" => array("foreignKey" => "id"))));
				$cloneChildren = $Tree->find("all", array(
					"conditions" => array("parent_id" => $cloneId)
				));
				$this->log("Error: original id = " . $originalId . ", cloned id = " . $cloneId . "\nThe number of cloned " . $objectType . "'s children doesn't match the original one. Cloned children are:\n" . var_export($cloneChildren, true), "clone_publication");
				$this->out("Ooops... something seems went wrong. The number of cloned " . $objectType . "'s children doesn't match the original one (check clone_publication.log file).");
				$response = $this->in("Do you want to continue?", array("y", "n"), "n");
				if ($response == "n") {
					$transaction->rollback();
					$this->out("All data are rollbacked");
					$this->out("Bye");
					return;
				}
			}
		}

		$this->out("Publication cloned");

		$transaction->commit();
	}

	function help() {
		$this->out('Available functions:');
        $this->out('1. rebuildIndex: rebuild search texts index');
		$this->out(' ');
		$this->out('    Usage: rebuildIndex [-engine <search-model>] [-delete] [-id <obj-id>] [-type <object-type>] [-verbose] [-log]');
		$this->out(' ');
        $this->out("    -engine \t search engine to use, e.g. ElasticSearch");
		$this->out("    -delete \t delete index before rebuild");
        $this->out("    -id \t rebuild index for single object only");
        $this->out("    -type \t rebuild index only for a single object type (like 'document', 'image'...)");
        $this->out("    -verbose \t show also successfully results");
		$this->out("    -log \t write errors on rebuildIndex.log file");
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
        $this->out('    Usage: updateStreamFields [-id <object-id>]');
        $this->out(' ');
        $this->out("    -id \t update data only for object with that id");
  		$this->out(' ');
  		$this->out("10. checkConsistency: check objects for consistency on database");
  		$this->out(' ');
		$this->out("11. updateTreeRoot: update area_id field in trees table");
        $this->out(' ');
		$this->out("12. checkDbNames: check database table/column names");
        $this->out(' ');
        $this->out('    Usage: checkDbNames [-schema]');
        $this->out(' ');
        $this->out("    -schema \t use schema file - otherwise read directly from db (default)");
        $this->out(' ');
        $this->out("13. cleanup: remove old items from log/job tables");
        $this->out(' ');
        $this->out('    Usage: cleanup -days <num-of-days>');
        $this->out(' ');
        $this->out("    -days \t number of days to preserve from today in cleanup");
        $this->out(' ');
		$this->out("14. updateCategoryName: update all categories and tags unique name");
        $this->out(' ');
		$this->out("    -objectType \t <object-type-name> update categories for a specific object type (for example: document)");
        $this->out(' ');
		$this->out("15. importXml: import BE objects from XML (in a section)");
        $this->out(' ');
        $this->out('    Usage: importXml -f <xml-file> [-s <section-id>] ');
        $this->out(' ');
        $this->out("    -f \t xml file path");
        $this->out("    -s \t section id to import");
        $this->out(' ');
		$this->out("16. exportXml: export BE objects to XML");
        $this->out(' ');
        $this->out('    Usage: importXml -o <xml-file> -t <obj-type> ');
        $this->out(' ');
        $this->out("    -o \t xml output file path");
        $this->out("    -t \t object type: document, short_news, event,.....");
        $this->out(' ');
		$this->out('17. clearMediaCache: clears media cache files/directories');
		 $this->out(' ');
        $this->out('    Usage: clearMediaCache [-log] ');
        $this->out(' ');
		$this->out("    -log \t write errors on clearMediaCache.log file");
  		$this->out(' ');
        $this->out("18. massRemove: massive removal of object type from system");
        $this->out(' ');
        $this->out('    Usage: massRemove -type <model-type> ');
        $this->out(' ');
		$this->out("19. clonePublication: clone a complete tree structure starting from publication id");
        $this->out(' ');
        $this->out('    Usage: clonePublication -id <publication-id> [-nicknameSuffix <suffix>] [-keepTitle]');
        $this->out(' ');
		$this->out("    -nicknameSuffix <suffix> \t suffix that will be append at original nicknames (nick-<suffix>)");
        $this->out("    -keepTitle \t the cloned objects keep the original titles");
        $this->out(' ');
	}

}

?>
<?php
/**
 * note/domande sul parsing:
 * 
 *  - id del file XML diventano 'aliases' 
 * 
 *  - box dentro sottoparagrafi diventano documenti relazionati con 'seealso'
 *  - anchor con href delle immagini cosa diventano
 *  - immagini solo un href
 * 
 *  - box --> tipo diventa category
 * 
 */  

require_once 'bedita_base.php';
App::import("Core", "Xml");
App::import("File", "BeLib", true, array(BEDITA_LIBS), "be_lib.php");
BeLib::getObject("BeConfigure")->initConfig();


/**
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class XmlImportShell extends BeditaBaseShell {
	
	protected $tagsIgnore = array("<em>", "<i>", "<dfn>", 
		"<p>", "<ol>", "<li>", "<ul>", "<a>", "<h1>", "<h2>");

	protected $tagsMove = array("<immagine>", "<box>");
	
	protected $tagsModified = array();
	
	protected $tagsNotFound = array();
	
	protected $mapTagsObjs = array(
		"sottoparagrafo" => "document",
		"immagine" => "image",
		"paragrafo" => "section",
		"capitolo" => "section",
		"box" => "document",
	);
	
	protected $mapTagsFields = array(
		"titolosottoparagrafo" => "title",
		"capoverso" => "body",
		"value" => "body",
		"titolo" => "title",
		"apertura" => "description",
		"description" => "description",
		"didascalia" => "description",
		"note" => "note",
		"numeroimmagine" => "title",
		"Didascalia" => "",
		"Description" => "",
		"File" => "",
		"id" => "alias",
		"rel" => "relatedAlias",
		"tipo" => "category",
	);
	
	protected $objDefaults = array(
		"status" => "on",
		"user_created" => "1",
		"user_modified" => "1",
		"lang" => "ita",
		"ip_created" => "127.0.0.1",
	);
	
	protected $defaultPublication = 1;
	
	function import() {
		$path = $this->mandatoryArgument('path', "path is mandatory use -path");
		$folder = new Folder($path);
		$logFile = new File($path . DS. "xml-import.log");
		$ls = $folder->ls();
		$this->initTagsModified();
		$beObjArrays = array();
		$logFile->append("\n++++++++++++++++++++++++++++++++++++++++++++++++\n");
		$logFile->append("\n" . date("r"). " - Importing from $path\n");
		foreach ($ls[1] as $f) {
		
			if(substr($f, strlen($f) - 4) != ".xml") {
		    	$this->out("$f ignored");
			} else {

				$this->out("reading $f ...");
				$strXml = file_get_contents($path.DS.$f);
				$xmlData = array();
		    	$this->cleanupTags($strXml, $xmlData);

		    	$logFile->append("xml modified - main:\n");
		    	$logFile->append($xmlData[0] . "\n");
		    	$logFile->append("xml modified - media:\n");
		    	$logFile->append($xmlData[1] . "\n");
		    	
		    	$xml = new XML($xmlData[0]);
				$parsed = set::reverse($xml);
				$logFile->append("$f parsed to array:\n");
		    	$logFile->append(print_r($parsed, true) . "\n");
				// map to BEdita objects arrays
				$this->mapObjArrays($beObjArrays, $parsed);
				
		    	$xml = new XML($xmlData[1]);
				$parsed = set::reverse($xml);
		    	
				$logFile->append("media xml to array:\n");
		    	$logFile->append(print_r($parsed, true) . "\n");
		    	
				$this->mapObjArrays($beObjArrays, $parsed["Media"]);
				
				$logFile->append("\nBE objects created... \n");
				$logFile->append(print_r($beObjArrays, true) . "\n");
		    	$logFile->append("\nErrors, tags not found... \n");
		    	$logFile->append(print_r($this->tagsNotFound, true) . "\n");

		    	$this->saveObjArray($beObjArrays);
		    	$this->out("...done");
			}
		}		
		
		// save arrays
		$this->out("import finished");
		$logFile->append("\n" . date("r"). " - end import from $path\n");
		$logFile->append("\n++++++++++++++++++++++++++++++++++++++++++++++++\n");
	}
	
	function main() {
		$this->import();
	}
	
	private function mapObjArrays(array &$beArr, array &$xmlArr, $parent=null) {
		foreach ($xmlArr as $k=>&$v) {
			
			$k1 = strtolower($k);
			$model = array_key_exists($k1, $this->mapTagsObjs) ? $this->mapTagsObjs[$k1] : null;
			
			if($model === null) {
				$k2 = Inflector::underscore($k);
				$model = array_key_exists($k2, $this->mapTagsObjs) ? $this->mapTagsObjs[$k2] : null;
			}
			
			if($model != null) {
				
				if(!empty($v[0])) {
					foreach ($v as $vi) {
						$tmpArr = array($k => $vi);
						$this->mapObjArrays($beArr, $tmpArr, $parent);
					}					
				} else {
					// create array from obj, and unset 
					$be = $this->objDefaults;
					foreach ($v as $kk=>$vv) {
						$kk2 = Inflector::underscore($kk);
						if(array_key_exists($kk, $this->mapTagsFields)) {
							
							if(!is_array($vv)) {
								$beField = $this->mapTagsFields[$kk];
								$tagText = $this->checkTagText($vv);
								if(!empty($be[$beField])) {
									$be[$beField] .= $tagText;
								} else {
									$be[$beField] =  $tagText;								
								}
								
							} else {
								// there has to be a method to handle field
								$method = "handle".Inflector::camelize($kk2)."Tag";
								$this->{$method}($be, $vv);	
							}
						
						} else if(array_key_exists($kk2, $this->mapTagsObjs)) {
						
							$tmpArr = array($kk => $vv);
							$this->mapObjArrays($beArr, $tmpArr, @$v["id"]);
					
						} else {

							if(!array_key_exists($kk, $this->tagsNotFound)) {
								$this->tagsNotFound[$kk] = 1;
							} else {
								$this->tagsNotFound[$kk] = $this->tagsNotFound[$kk] + 1;
							}
						}
					}
					$be["object_type_id"] = Configure::read("objectTypes." . $model . ".id");				
					if($parent == null) {
						$be["parent_id"] = $this->defaultPublication;
					} else {
						$be["parentAlias"] = $parent;
					}
					$beArr[$model][] = $be;
				}
				
				unset($xmlArr[$k]);			
			}
		}	
		
	}

	
	private function saveObjArray(array &$beArr) {
		$aliases = array();
		$aliasModel = ClassRegistry::init("Alias");
		$sections = array_reverse($beArr["section"]);
		$sectionModel = ClassRegistry::init("Section");
		foreach ($sections as $data) {
			$sectionModel->create();
			if(empty($data["parent_id"])) {
				$data["parent_id"] = $aliases[$data["parentAlias"]]; 
			}
			if(!$sectionModel->save($data)) {
				throw new BeditaException("Error saving section - " . print_r($data, true));
			}
			$aliases[$data["alias"]] = $sectionModel->id;
			$aliasModel->save(array("object_id" => $sectionModel->id, "nickname_alias" => $data["alias"]));
		}
		
		$beObjModel = ClassRegistry::init("BEObject");
		$treeModel = ClassRegistry::init("Tree");
		$mainDocs = array();
		$relatedDocs = array();
		foreach ($beArr["document"] as $d) {
			if(!empty($d["relatedAlias"])) {
				$relatedDocs[] = $d;
			} else {
				$mainDocs[] = $d;
			}
		}
		
		$documents = array_merge($mainDocs, $relatedDocs);
		$documentModel = ClassRegistry::init("Document");
		foreach ($documents as $data) {
			$documentModel->create();
			if(empty($data["parent_id"]) && !empty($data["parentAlias"])) {
				$data["parent_id"] = $aliases[$data["parentAlias"]]; 
			}
			// category and relations
			if(!empty($data["relatedAlias"])) {
				$relObjId = $aliases[$data["relatedAlias"]];
				$type = $beObjModel->getType($relObjId);
				if($type == "Section") {
					$data["parent_id"] = $relObjId;
				} else {
					$data["RelatedObject"]["seealso"][0]["switch"] = "seealso";
					$data["RelatedObject"]["seealso"][$relObjId]["id"] = $relObjId;
					unset($data["parent_id"]);
				}
				unset($data["relatedAlias"]);
			}
			if(!empty($data["category"])) {
				$data["Category"] = array($this->getCategoryId($data["category"], "document"));
				unset($data["category"]);
			}
			
			if(!$documentModel->save($data)) {
				throw new BeditaException("Error saving document - " . print_r($data, true));
			}
			if(!empty($data["parent_id"])) {
				$treeModel->appendChild($documentModel->id, $data["parent_id"]);
			}
			if(!empty($data["alias"])) {
				$aliases[$data["alias"]] = $documentModel->id;
				$aliasModel->save(array("object_id" => $documentModel->id, "nickname_alias" => $data["alias"]));
			}
		}
		
		$images = $beArr["image"];
		$imageModel = ClassRegistry::init("Image");
		$streamModel = ClassRegistry::init("Stream");
		foreach ($images as $data) {
			$imageModel->create();
			$relObjId = $aliases[$data["relatedAlias"]];
			$data["RelatedObject"]["attach"][0]["switch"] = "attach";
			$data["RelatedObject"]["attach"][$relObjId]["id"] = $relObjId;
			$data["Category"] = array($this->getCategoryId("image", "image"));
			if(!$imageModel->save($data)) {
				throw new BeditaException("Error saving image - " . print_r($data, true));
			}
			if(!empty($data["alias"])) {
				$aliases[$data["alias"]] = $imageModel->id;
				$aliasModel->save(array("object_id" => $imageModel->id, "nickname_alias" => $data["alias"]));
			}
			$streamModel->updateStreamFields($imageModel->id);
		}
		
	}
	
	private function getCategoryId($catName, $modelType) {
		$categoryModel = ClassRegistry::init("Category");
		$objTypeId = Configure::read("objectTypes.$modelType.id");
		// if not exists create
		$categoryModel->create();
		$catName = trim($catName);
		$categoryModel->bviorCompactResults = false;
		$idCat = $categoryModel->field('id', array('label'=>$catName, 'object_type_id' => $objTypeId));
		$categoryModel->bviorCompactResults = true;
		if(empty($idCat)) {
			$dataCat = array('name'=>$catName,'label'=>$catName,
				'object_type_id' => $objTypeId, 'status'=>'on');
			if(!$categoryModel->save($dataCat)) {
				throw new BeditaException("Error saving category: " . print_r($dataCat, true));
			}
			$idCat = $categoryModel->id;
		}
		return $idCat;
	}
	
	private function checkTagText($text) {
		$res = trim($text);
		foreach ($this->tagsModified as $tag => $tagMod) {
			$res = str_replace($tagMod, $tag, $res);
		}
		return $res;
	}
	
	private function initTagsModified() {
		foreach ($this->tagsIgnore as $tag) {
			$closeTag = str_replace("<", "</", $tag);
			$modTagOpen = str_replace(">", "]", $tag); 
			$modTagOpen = str_replace("<", "[", $modTagOpen); 
			$modTagClosed = str_replace(">", "]", $closeTag); 
			$modTagClosed = str_replace("<", "[", $modTagClosed); 
			$this->tagsModified[$tag] = $modTagOpen;
			$this->tagsModified[$closeTag] = $modTagClosed;
		}
	}
	
	private function cleanupTags($str, array& $data) {
		$modStr=$str;
		foreach ($this->tagsIgnore as $tag) {
			$closeTag = str_replace("<", "</", $tag);
			$modStr = str_replace($tag, $this->tagsModified[$tag], $modStr);
			$modStr = str_replace($closeTag, $this->tagsModified[$closeTag], $modStr);
			$attribTag = str_replace(">", " ", $tag);
			$pos = strpos($modStr, $attribTag);
			// tags with attributes
			while($pos) {
				$endpos = strpos($modStr, ">", $pos);
				$modStr{$pos} = "[";
				$modStr{$endpos} = "]";
				$pos = strpos($modStr, $attribTag, $endpos);
			}
		}
		
		$strMoved = "\n";
		foreach ($this->tagsMove as $tag) {
			$mvTag = str_replace(">", "", $tag);
			$closeTag = str_replace("<", "</", $tag);
			$pos = strpos($modStr, $mvTag);
			while($pos) {
				$endpos = strpos($modStr, $closeTag, $pos) + strlen($closeTag);
				$strMoved .= substr($modStr, $pos, $endpos-$pos)."\n";
				$modStr = substr($modStr, 0, $pos) . substr($modStr, $endpos);
				$pos = strpos($modStr, $mvTag, $pos);
			}
		}
//		if(!empty($strMoved)) {
//			$pos = strrpos($modStr, "</");
//			$modStr = substr($modStr, 0, $pos-1) . $strMoved . substr($modStr, $pos);
//		}
		$data[0] = $modStr;
		$data[1] = '<?xml version="1.0" encoding="utf-8"?><media>'. $strMoved . '</media>';
//		return $modStr;
	}
	
// BEGIN TAGS HANDLER

	private function handleDidascaliaTag(array &$be, array &$data) {
		if(isset($data["value"])) {
			$be["abstract"] = $this->checkTagText($data["value"]);
		}
		if(isset($data["titolodidascalia"])) {
			$be["description"] = $this->checkTagText($data["titolodidascalia"]);
		}
	}
	
	private function handleTitoloTag(array &$be, array &$data) {
		if(isset($data["value"])) {
			$be["title"] = $this->checkTagText($data["value"]);
		}
		// number??
	}

	private function handleDescriptionTag(array &$be, array &$data) {
		if(!isset($be["description"])) {
			$be["description"] = "";
		}
		foreach ($data as $d) {
			$be["description"] .= $d;
		}
	}
	
	private function handleFileTag(array &$be, array &$data) {
        // [note] => vedi anche 01.01A.hi, 01.01B.hi.treatment3, 01.01C, 01.01D.CC.hi, 01.01E.hi.CC, 01.01F.hi.CC, 01.01G.CC.hi
        // ??[url] => LIFE8E01.01 
        // ??[href_opt] => immagini/01_opt.jpeg
        // ??[href_fmt] => immagini/01_fmt.jpeg
        // [href] => file://immagini/Purves%2001/01.eps
		if(isset($data["note"])) {
			$be["note"] = $this->checkTagText($data["note"]);
		}
		if(isset($data["href_fmt"])) {
//			$be["path"] = $this->checkTagText(str_replace("file:/", "" , $data["href"]));
			$be["path"] = $this->checkTagText("/" . urldecode($data["href_fmt"]));
		}
	}

// END Tags handler
	
	function testOut() {
		
		$id = 13;
		$beObj = ClassRegistry::init("BEObject");
		$modName = $beObj->getType($id);
		$beModel = ClassRegistry::init($modName);
		$beModel->containLevel("detailed");
		$obj = $beModel->findById($id);
		
		unset($obj["UserCreated"]);
		unset($obj["UserModified"]);
		unset($obj["ObjectType"]);
		
		$this->cleanup($obj);
		$this->out("object array clean: ");
		$objArr = array("Object" => $obj);
		pr($objArr);
		$this->hr();
		$xml = new XML($objArr, array('format' => 'tags'));
//		$parsed = set::reverse($xml);
		
		$strXml = $xml->toString();
//    	$this->out("$fileName parsed to array:");
		$this->out("xml string: ");
		$this->out($strXml);
		$this->hr();
		
		// read XML and create array
		$xml = new XML($strXml, array('format' => 'tags'));
		$parsed = set::reverse($xml);
		$this->out("array parsed from xml string: ");
		pr($parsed);
		$this->hr();
		
	}

	private function cleanup(array &$ar) {
		foreach ($ar as $k=>&$v) {
			if(empty($v)) {
				unset($ar[$k]);
			} else if(is_array($v)) {
				$this->cleanup($v);
			}
		}	
	}
	
/*

nomi recuperati:

initTagsModified
checkTagText
createBeObjects
saveModelData
handleTags
#objSaved;XmlImportShell;
saveObjects
objDefaults
createBeObjects
populateObjects
mapObjArrays
parsed;XmlImportShell;testOut
tagsSubstitute
$attrTagMod;XmlImportShell;handleTags
closeNewTag;XmlImportShell;handleTags
$testDirName;XmlImportShell;testFiles
_be;XmlImportShell;handleFileTag
XmlImportShell;handleTags
relations

 * 
 */	
	
// elenco di elementi scalari (campi db) e array (relazioni, varie robe)

// array da non salvare contestualmente: RelatedObject, Annotation

// array da salvare prima: Tag, Category, CustomProperty

// array da salvare contestualmente: DateItem, GeoTag

// array da salvare dopo: LangText
	
// array da salvare alla fine: RelatedObject, position on tree
	
/**
 * Notes:
 * 
 * data types (generic format)
 * 
 * Independent:
 *  - groups
 *  - users
 *  - modules
 *  - object_types
 *  - properties
 *  - banned_ips include????
 *  - event_log
 *  - mail_log
 *  
 * Dependent (not from objects)
 *  - property_options (property)
 *  - users groups (groups, users)
 *  - permission_modules (groups, permissions)
 *  - hash_jobs (users) -- include????
 *  - object
 *
 * Dependent from objects
 *  - categories (areas)
 *  - mail_groups (areas)
 *  - mail_group_cards (mailgroup, card)
 *  - mail_jobs (mailmessage, card)
 */	
	
	function help() {
		$this->out("Shell script to import generic XML in BEdita");
	}
	
}
?>
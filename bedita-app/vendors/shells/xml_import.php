<?php
/**
 * note/domande sul parsing:
 * 
 *  - tag inclusi in un nodo di testo sono da consiederarsi come tag HTML, da conservare
 *  o tag <ref> da gestire? quali di questi tag hanno degli attributi? solo ref?
 *  
 *  - gli id id0e21579, sono univoci all'interno del capitolo? del libro? 
 *  sono cross-referenziati in capitoli diversi?
 * 
 *  - per gli id del file XML possiamo usare 'aliases' in modo 
 * 
 */  

/**
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class XmlImportShell extends BeditaBaseShell {
	
	protected $tagsIgnore = array("<em>", "<i>", "<dfn>");
	protected $tagsModified = array();
	
	protected $mapTagsObjs = array(
		"sottoparagrafo" => "document",
		"immagine" => "image",
	);
	
	protected $mapTagsFields = array(
		"titolosottoparagrafo" => "title",
		"capoverso" => "body",
		"numeroimmagine" => "title",
		"Didascalia" => "",
	
	);
	
	protected $objDefaults = array(
		"status" => "on",
		"user_created" => "1",
	);
	
	
	function zanichelli() {
		$fileName = "";
		if (isset($this->params['f'])) {
            $fileName = $this->params['f'];
    	} else {
    		$this->out("filename is mandatory use -f");
    		$this->help();
    		return;
    	}

    	if(!file_exists($fileName)) {
    		$this->out("$fileName not found, bye");
    		return;
    	}
    	
		$xml = new XML($fileName);
		$parsed = set::reverse($xml);
		
    	$this->out("$fileName parsed to array:");
		pr($parsed);
	}
	
	function testFiles() {
		$testDirName = "/home/ste/tmp/sadava";
		$folder = new Folder($testDirName);
		$ls = $folder->ls();
		$this->initTagsModified();
		$beObjArrays = array();
		foreach ($ls[1] as $f) {
		
	    	$strXml = file_get_contents($testDirName.DS.$f);
	    	$strXml = $this->hideIgnoredTags($strXml);
			$xml = new XML($strXml);
			$parsed = set::reverse($xml);
	    	$this->out("$f parsed to array:");
			pr($parsed);
			// map to BEdita objects arrays
			$this->mapObjArrays($beObjArrays, $parsed);
			pr($beObjArrays);
		}		
		
		// save arrays
		
	}
	
	private function mapObjArrays(array &$beArr, array &$xmlArr) {
		foreach ($xmlArr as $k=>&$v) {
			
			$k1 = strtolower($k);
			$model = array_key_exists($k1, $this->mapTagsObjs) ? $this->mapTagsObjs[$k1] : null;
			
			if($model === null) {
				$k2 = Inflector::underscore($k);
				$model = array_key_exists($k2, $this->mapTagsObjs) ? $this->mapTagsObjs[$k2] : null;
			}
			
			if($model != null) {
				// create array from obj, and unset 
				$be = $this->objDefaults;
				foreach ($v as $kk=>$vv) {
					if(array_key_exists($kk, $this->mapTagsFields)) {
						
						if(!is_array($vv)) {
							$be[$this->mapTagsFields[$kk]] = $this->checkTagText($vv);
						} else {
							// there has to be a method to handle thi field
							$method = "handle".$kk."Tag";
							$this->{$method}($be, $vv);	
						}
					}
				}
				$be["object_type_id"] = Configure::read("objectTypes." . $model . ".id");				
				
				$beArr[] = $be;
				unset($xmlArr[$k]);
			} else if(is_array($v)) {
				$this->mapObjArrays($beArr, $v);
			}
		}	
		
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
	
	private function hideIgnoredTags($str) {
		$modStr=$str;
		foreach ($this->tagsIgnore as $tag) {
			$closeTag = str_replace("<", "</", $tag);
			$modStr = str_replace($tag, $this->tagsModified[$tag], $modStr);
			$modStr = str_replace($closeTag, $this->tagsModified[$closeTag], $modStr);
		}
		return $modStr;
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
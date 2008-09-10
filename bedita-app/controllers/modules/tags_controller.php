<?php
/**
 *
 * @filesource
 * @copyright		
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 * @author 			d.didomenico@channelweb.it
 */

/**
 * Tags handling
 */
class TagsController extends ModulesController {

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission');
	var $uses = array('Category') ;
	
	protected $moduleName = 'tags';

	public function index($order = "label", $dir = 1) {
		$data = $this->Category->getTags(true, null, true, 12, $order, $dir);
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
		
			$referenced = $this->Category->getContentsByTag($tag["label"]);
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
			$this->Category->del($id); 
		}
		$this->Transaction->commit();
		
		$tagsListDeleted = implode(",", $this->params["form"]["tags_selected"]);
		
		$this->userInfoMessage(__("Tag deleted", true) . " -  " . $tagsListDeleted);
		$this->eventInfo("Tag $tagsListDeleted deleted");
	}

	public function listAllTags($href=false) {
		$this->layout = "empty";
		$this->set("listTags",$this->Category->getTags(true, null, true));
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
	
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"save"	=> 	array(
								"OK"	=> "/tags/view/{$this->Category->id}",
								"ERROR"	=> "/tags/view/{$this->Category->id}" 
						), 
			"delete" =>	array(
								"OK"	=> "/tags",
								"ERROR"	=> "/tags/view/{@$this->params['pass'][0]}" 
						),
			"addMultipleTags" => array(
								"OK"	=> "/tags",
								"ERROR"	=> "/tags" 
						),
			"changeStatus" => array(
								"OK"	=> "/tags",
								"ERROR"	=> "/tags" 
						)
		) ;
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}

?>
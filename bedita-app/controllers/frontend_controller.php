<?php
/**
 * Base frontend controller
 * @author ste@channelweb.it
 * @author dante@channelweb.it
 */
abstract class FrontendController extends AppController {

	private $status = array('on');
	protected $checkPubDate = true;
	
	/**
	 * $uses & $components array don't work... (abstract class ??)
	 */
	private function initAttributes() {
		if(!isset($this->Section)) {
			$this->Section = $this->loadModelByType('Section');
		}
		if(!isset($this->Stream)) {
			$this->Stream = $this->loadModelByType('Stream');
		}
		if(!isset($this->BeLangText)) {
			App::import('Component', 'BeLangText');
			$this->BeLangText = new BeLangTextComponent();
		}
		$conf = Configure::getInstance() ;
		if (!empty($conf->draft))
			$this->status[] = "draft";
	}

	/**
	* Get area's section
	* 
	* @param integer $area_id			area parent
	* @param  string $var_name			name result in to template_ vars
	* @params array $exclude_nicknames	list exclude sections 
	* */
	protected function loadSections($area_id, $var_name = null, $exclude_nicknames = null) {
		$this->initAttributes();
		$conf = Configure::getInstance() ;
		$draft = ($conf->draft != null) ? $conf->draft : false;
		$areas_sections = array();
		$areas_sections_source = $this->BeTree->getSectionsTree() ;

		foreach($areas_sections_source as $index => $area) {
			if(empty($area_id) || ($area_id == $area['id'])) {
				
				if( ( ($area['status'] == 'on') || ($draft && ($area['status'] == 'draft'))) && !empty($area['children'])) {
					$children_arr = array();
					foreach($area['children'] as $section_index => $s) {
						if(is_array($exclude_nicknames) && in_array($s['nickname'], $exclude_nicknames)) continue ;
						
						$this->modelBindings($this->Section);
						$section = $this->Section->findById($s['id']);
						if( ($section['status'] == 'on') || ($draft && ($section['status'] == 'draft'))) {
							if(isset($section["LangText"])) {
								$this->BeLangText->setupForView($section["LangText"]) ;
							}
							if(!empty($s['children'])) {
								$c_arr = array();
								foreach($s['children'] as $ss_index => $ss) {
									$this->modelBindings($this->Section);
									$s_section = $this->Section->findById($ss['id']);
									if( ($s_section['status'] == 'on') || ($draft && ($s_section['status'] == 'draft'))) {
										if(isset($s_section["LangText"])) {
											$this->BeLangText->setupForView($s_section["LangText"]) ;
										}
										$c_arr[] = $s_section;
									}
								}
								$section['children'] = $c_arr;
							}
							$children_arr[] = $section;
						}
					}
					$a['id'] = $area['id'];
					$a['title'] = $area['title'];
					$a['children'] = $children_arr;
					$areas_sections[$index]=$a;
				}
			}
		}
		$this->set((isset($var_name)?$var_name:'sections'), $areas_sections);
	}

	/**
	 * check if current date is compatible with required pubblication dates (start/end date)
	 *
	 * @param array $obj
	 * @return true if content may be published, false otherwise
	 */
	protected function checkPubblicationDate(array $obj) {
		$currDate = strftime("%Y-%m-%d");
		if(isset($obj["start"])) {
			if(strncmp($currDate, $obj["start"], 10) < 0)
				return false;
		}
		if(isset($obj["end"])) {
			if(strncmp($currDate, $obj["end"], 10) > 0)
				return false;
		}
		return true;
	}
	
	protected function loadObj($obj_id,$ot, $var_name = null) {
		$this->initAttributes();
		$conf = Configure::getInstance() ;
		$lang = $this->Session->read('Config.language');
		if($lang==null) 
			$lang = $conf->frontendLang;
		if($obj_id == null) {
			$objects = $this->BeTree->getDiscendents(null, $this->status, $conf->objectTypes[$ot], null, true, 1, 1);
			if(!empty($objects['items'])) {
				$obj_id = $objects['items'][0]['id'];
			}
		}
		if($obj_id==null)
			return null;
		$model = $this->loadModelByObjectTypeId($conf->objectTypes[$ot]);
		$this->modelBindings($model);
		$obj = $model->find("first", array(
								"conditions" => array(
									"BEObject.id" => $obj_id,
									"status" => $this->status
									)
								)
							);

		if($this->checkPubDate && !$this->checkPubblicationDate($obj)) {
			return null;
		}
		if(!empty($obj) && !empty($obj["LangText"])) {
			$this->BeLangText->setupForView($obj["LangText"]) ;
		}
		if(!empty($obj) && !empty($obj["ObjectRelation"])) {
			$relations = $this->objectRelationArray($obj['ObjectRelation']);
			$obj['relations'] = $relations;
		}
		$this->BeLangText->objectForLang($obj_id,$lang,$obj);
		if(!empty($obj['gallery_id'])) {
			$gid = $obj['gallery_id'];
			$types = array($conf->objectTypes['image'], $conf->objectTypes['audio'], $conf->objectTypes['video']) ;
			$children = $this->BeTree->getChildren($gid, $this->status, $types, "priority") ;
			$objForGallery = &$children['items'] ;
			$multimedia=array();
			foreach($objForGallery as $index => $object) {
				$model = $this->loadModelByObjectTypeId($object['object_type_id']);
				$this->modelBindings($model);
				$Details = $model->find("first", array(
									"conditions" => array(
										"BEObject.id" => $object['id'],
										"status" => $this->status
										)
									)
								);
				if (!$Details) 
					continue ;
				if(!empty($Details["LangText"])) {
					$this->BeLangText->setupForView($Details["LangText"]) ;
				}
				$Details['priority'] = $object['priority'];
				$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
				$multimedia[$index]=$Details;
			}
			$obj['gallery_items'] = $multimedia;
		}
		
		$this->set((isset($var_name)?$var_name:$ot),$obj);
		return $obj;
	}

	protected function loadObjects($parent_id,$obj_to_view, $conditions=array(),$children=false) {
		$this->initAttributes();
		$result = array();
		$conf = Configure::getInstance();
		$lang = $this->Session->read('Config.language');
		if($lang==null) 
			$lang = $conf->frontendLang;
		foreach($obj_to_view as $tplvar => $ot) {
			$fullitems = array();
			$items = ($children) ? $this->BeTree->getChildren($parent_id, $this->status, $conf->objectTypes[$ot]) : $this->BeTree->getDiscendents($parent_id, $this->status, $conf->objectTypes[$ot])  ;
			if(!empty($items) && !empty($items['items'])) {
				foreach($items['items'] as $index => $item) {
					$model = $this->loadModelByObjectTypeId($item['object_type_id']);
					$this->modelBindings($model);
					$cond = array_merge(array(
										"BEObject.id" => $item['id'],
										"status" => $this->status
										), $conditions
									);
					$Details = $model->find("first", array(
									"conditions" => $cond
									)
								);
					if(!$Details) 
						continue;
					if($this->checkPubDate && !$this->checkPubblicationDate($Details)) {
						continue;
					}
								
					if(!empty($Details) && !empty($Details["LangText"])) {
						$this->BeLangText->setupForView($Details["LangText"]) ;
					}
					$this->BeLangText->objectForLang($Details['id'],$lang,$Details);
					if(!empty($Details) && !empty($Details["ObjectRelation"])) {
						$relations = $this->objectRelationArray($Details['ObjectRelation']);
						$Details['relations'] = $relations;
					}
					
					if(!empty($Details['gallery_id'])) {
						$gid = $Details['gallery_id'];
						$types = array($conf->objectTypes['image'], $conf->objectTypes['audio'], $conf->objectTypes['video']) ;
						$children = $this->BeTree->getChildren($gid, $this->status, $types, "priority") ;
						$objForGallery = &$children['items'] ;
						$multimedia=array();
						foreach($objForGallery as $index => $object) {
							$model = $this->loadModelByObjectTypeId($object['object_type_id']);
							$this->modelBindings($model);
							$obj_data = $model->find("first", array(
												"conditions" => array(
													"BEObject.id" => $object['id'],
													"status" => $this->status
													)
												)
											);
							if (!$obj_data) 
								continue ;
							$obj_data['priority'] = $object['priority'];
							$obj_data['filename'] = substr($obj_data['path'],strripos($obj_data['path'],"/")+1);
							$multimedia[$index]=$obj_data;
						}
						$Details['gallery_items'] = $multimedia;
					}
					$fullitems[]=$Details;
				}
				$this->set($tplvar,$fullitems);
				$result[$tplvar] = $fullitems;
			}
		}
		return $result;
	}
	
	protected function loadGalleries() {
		$this->initAttributes();
		$conf = Configure::getInstance();
		$types = array($conf->objectTypes['gallery']);
		$result = array();
		$objects = $this->BeTree->getDiscendents(null, $this->status, $types) ;
		if(!empty($objects) && !empty($objects['items'])) {
			$galleries = $objects['items'];
			$ot  = array($conf->objectTypes['image'],$conf->objectTypes['audio'],$conf->objectTypes['video']);
			foreach($galleries as $key => $gallery) {
				$result[$key] = $gallery;
				// get language version
				$model = $this->loadModelByObjectTypeId($gallery['object_type_id']);
				$this->modelBindings($model);
				$Details = $model->findById($gallery["id"]);
				if(!empty($Details) && !empty($Details["LangText"])) {
					$this->BeLangText->setupForView($Details["LangText"]) ;
				}
				$result[$key]["LangText"] = $Details["LangText"];
				// get gallery items
				$multimedia_items = $this->BeTree->getChildren($gallery['id'], $this->status, $ot, "priority") ;
				if(!empty($multimedia_items) && !empty($multimedia_items['items'])) {
					$items = array();
					foreach($multimedia_items['items'] as $i) {
						$model = $this->loadModelByObjectTypeId($i['object_type_id']);
						$this->modelBindings($model);
						$obj = $model->findById($i['id']);
						if(!empty($obj) && !empty($obj["LangText"])) {
							$this->BeLangText->setupForView($obj["LangText"]) ;
						}
						$items = $i;
						$items['Stream'] =$obj;
						$result[$key]['items'][] = $items;
			
					}
				}				
			}
			$this->set('galleries',$result);
		}
	}
	
	protected function showDraft() {
		$this->status[] = "draft";
	}
	
	public function getStatus() {
		return $this->status;
	}
}
?>
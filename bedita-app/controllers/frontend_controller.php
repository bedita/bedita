<?php
/**
 * Base frontend controller
 * @author ste@channelweb.it
 * @author dante@channelweb.it
 */
abstract class FrontendController extends AppController {

	private $status = array('on');
	
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

	protected function loadSections($area_id) {
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
		$this->set('sections',$areas_sections);
	}

	protected function loadObj($obj_id,$ot) {
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
				$Details['priority'] = $object['priority'];
				$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
				$multimedia[$index]=$Details;
			}
			$obj['gallery_items'] = $multimedia;
		}
		
		$this->set($ot,$obj);
		return $obj;
	}

	protected function loadObjects($parent_id,$obj_to_view) {
		$this->initAttributes();
		$result = array();
		$conf = Configure::getInstance();
		$lang = $this->Session->read('Config.language');
		if($lang==null) 
			$lang = $conf->frontendLang;
		foreach($obj_to_view as $tplvar => $ot) {
			$fullitems = array();
			$items = $this->BeTree->getDiscendents($parent_id, $this->status, $conf->objectTypes[$ot])  ;
			if(!empty($items) && !empty($items['items'])) {
				foreach($items['items'] as $index => $item) {
					$model = $this->loadModelByObjectTypeId($item['object_type_id']);
					$this->modelBindings($model);
					$Details = $model->find("first", array(
									"conditions" => array(
										"BEObject.id" => $item['id'],
										"status" => $this->status
										)
									)
								);
					if(!$Details) 
						continue ;
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
		$draft = ($conf->draft != null) ? $conf->draft : false;
		$types = array($conf->objectTypes['gallery']);
		$result = array();
		$objects = $this->BeTree->getDiscendents(null, null, $types, "", true, 1, 10) ;
		if(!empty($objects) && !empty($objects['items'])) {
			$galleries = $objects['items'];
			$ot  = array($conf->objectTypes['image'],$conf->objectTypes['audio'],$conf->objectTypes['video']);
			foreach($galleries as $key => $gallery) {
				if( ($gallery['status'] == 'on') || ($draft && ($gallery['status'] == 'draft'))) {
					$result[$key] = $gallery;
					$multimedia_items = $this->BeTree->getChildren($gallery['id'], null, $ot, "priority") ;
					if(!empty($multimedia_items) && !empty($multimedia_items['items'])) {
						$items = array();
						foreach($multimedia_items['items'] as $i) {
							$this->modelBindings($this->Stream);
							$obj = $this->Stream->findById($i['id']);
							if( ($i['status'] == 'on') || ($draft && ($i['status'] == 'draft'))) {
								$items = $i;
								$items['Stream'] =$obj['Stream'];
								$result[$key]['items'][] = $items;
							}
						}
					}
				}
			}
			$this->set('galleries',$result);
		}
	}
	
	protected function showDraft() {
		$this->status[] = "draft";
	}
}
?>
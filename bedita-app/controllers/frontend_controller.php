<?php
/**
 * Base frontend controller
 * @author ste@channelweb.it
 * @author dante@channelweb.it
 */
abstract class FrontendController extends AppController {

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
		
	}
	
	protected function loadSections($area_id) {
		$this->initAttributes();
		$areas_sections = array();
		$areas_sections_source = $this->BeTree->getSectionsTree() ;
		
		foreach($areas_sections_source as $index => $area) {
			if(empty($area_id) || ($area_id == $area['id'])) {
				if(!empty($area['children'])) {
					$children_arr = array();
					foreach($area['children'] as $section_index => $s) {
							$this->modelBindings($this->Section);
							$section = $this->Section->findById($s['id']);
							if(isset($section["LangText"])) {
								$this->BeLangText->setupForView($section["LangText"]) ;
							}
						if(!empty($s['children'])) {
							$c_arr = array();
							foreach($s['children'] as $ss_index => $ss) {
								$this->modelBindings($this->Section);
								$s_section = $this->Section->findById($ss['id']);
								if(isset($s_section["LangText"])) {
									$this->BeLangText->setupForView($s_section["LangText"]) ;
								}
								$c_arr[] = $s_section;
							}
							$section['children'] = $c_arr;
						}
						$children_arr[] = $section;
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
			$objects = $this->BeTree->getDiscendents(null, null, $conf->objectTypes[$ot]);
			if(!empty($objects) && !empty($objects['items'])) {
				$obj_id = $objects['items'][0]['id'];
			}
		}
		$model = $this->loadModelByObjectTypeId($conf->objectTypes[$ot]);
		$this->modelBindings($model);
		$obj = $model->findById($obj_id);
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
			$children = $this->BeTree->getChildren($gid, null, $types, "priority") ;
			$objForGallery = &$children['items'] ;
			$multimedia=array();
			foreach($objForGallery as $index => $object) {
				$model = $this->loadModelByObjectTypeId($object['object_type_id']);
				$this->modelBindings($model);
				if(!($Details = $model->findById($object['id']))) 
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
			$items = $this->BeTree->getDiscendents($parent_id, null, $conf->objectTypes[$ot])  ;
			if(!empty($items) && !empty($items['items'])) {
				foreach($items['items'] as $index => $item) {
					$model = $this->loadModelByObjectTypeId($item['object_type_id']);
					$this->modelBindings($model);
					if(!($Details = $model->findById($item['id']))) 
						continue ;
					if(!empty($Details) && !empty($Details["LangText"])) {
						$this->BeLangText->setupForView($Details["LangText"]) ;
					}
					$this->BeLangText->objectForLang($Details['id'],$lang,$Details);
					if(!empty($Details) && !empty($Details['EventDateItem'])) {
						$Details['time'] = substr($Details['EventDateItem'][0]['start'],11,5);
						$Details['date'] = substr($Details['EventDateItem'][0]['start'],0,10);
					}
					if(!empty($Details) && !empty($Details['ObjectCategory'])) {
						$Details['category'] = $Details['ObjectCategory'][0]['label'];
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
		$objects = $this->BeTree->getDiscendents(null, null, $types, "", true, 1, 10) ;
		if(!empty($objects) && !empty($objects['items'])) {
			$galleries = $objects['items'];
			$ot  = array($conf->objectTypes['image'],$conf->objectTypes['audio'],$conf->objectTypes['video']);
			foreach($galleries as $key => $gallery) {
				$multimedia_items = $this->BeTree->getChildren($gallery['id'], null, $ot, "priority") ;
				if(!empty($multimedia_items) && !empty($multimedia_items['items'])) {
					$items = array();
					foreach($multimedia_items['items'] as $i) {
						$this->modelBindings($this->Stream);
						$obj = $this->Stream->findById($i['id']);
						$items = $i;
						$items['Stream'] =$obj['Stream'];
						$galleries[$key]['items'][] = $items;
					}
				}
			}
			$this->set('galleries',$galleries);
		}
	}
}
?>
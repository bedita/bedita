<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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

/**
 * Controller module Publications: managing of publications, sections and sessions
 *
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class AreasController extends ModulesController {
	var $name = 'Areas';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'BeCustomProperty', 'BeLangText', 'BeUploadToObj', 'BeFileHandler', 'BeSecurity');

	var $uses = array('BEObject', 'Area', 'Section', 'Tree', 'User', 'Group', 'ObjectType','Category') ;
	protected $moduleName = 'areas';
	protected $categorizableModels = array('Section');

	function index($id = null, $order = '', $dir = true, $page = 1, $dim = 20) {
        if ($id == null && !empty($this->params["named"]["id"])) {
            $id = $this->params["named"]["id"];
        }
        // if $id redirect to detail view
        if (!empty($id)) {
            $this->redirect('/areas/view/' . $id);
        }

        $conf = Configure::getInstance();
        $filter['object_type_id'] = array(
            $conf->objectTypes['area']['id'],
            $conf->objectTypes['section']['id'],
        );
        $filter['count_annotation'] = array('Comment', 'EditorNote');
        $this->paginatedList($id, $filter, $order, $dir, $page, $dim);
        $this->loadCategories($filter['object_type_id']);

        $this->set('objectTypeIds', $filter['object_type_id']);
	}

	public function view($id = null) {
		$objectTypeId = $this->BEObject->field("object_type_id", array("BEObject.id" => $id));
		$modelName = Configure::read("objectTypes.".$objectTypeId.".model");
		if(empty($modelName)) {
			throw new BeditaException(sprintf(__("Object id not found: %d", true), $id));
		}
		$this->viewObject($this->{$modelName}, $id);
		$dir = ($this->viewVars["object"]["priority_order"] == "asc")? true : false;
		$this->loadChildren($id, "priority", $dir);
		$this->set("objectType", Configure::read("objectTypes.".$objectTypeId.".name"));
		$parentId = null;
		if (empty($id) && !empty($this->params['named']['id'])) {
			$id = $this->params['named']['id'];
		}
		if (!empty($id)) {
			$parentId = $this->Tree->getParent($id);
		} else if (!empty($this->params['named']['branch'])) {
			$parentId = $this->params['named']['branch'];
		}
		$this->set('parent_id', $parentId);
		$filters = $this->loadFilters('export');
		$this->set('export_filters',$filters);
	}
	 
	/**
	 * load paginated contents and no paginated sections of $id publication/section
	 *
	 * @param int $id
	 * @param string $order
	 * @param bool $dir
	 * @param int $page
	 * @param int $dim
	 */
	protected function loadChildren($id, $order = "priority", $dir = true, $page = 1, $dim = 20) {
		// get paginated children content (leaf objectTypes) if no other is passed
		if (!empty($this->params["named"]["object_type_id"])
				&& $this->params["named"]["object_type_id"] != Configure::read("objectTypes.area.id")
				&& $this->params["named"]["object_type_id"] != Configure::read("objectTypes.section.id")) {
			$filter["object_type_id"] = $this->params["named"]["object_type_id"];
		} else {
			$filter["object_type_id"] = Configure::read("objectTypes.leafs.id");
		}
		$filter["count_annotation"] = array("EditorNote");
		$dir = ($this->viewVars["object"]["priority_order"] == "asc")? true : false;
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);

		// get no paginated children sections
		$filter["object_type_id"] = Configure::read("objectTypes.section.id");
		$filter["count_permission"] = true;
		$sections = $this->BeTree->getChildren($id, null, $filter, "priority", $dir);
		$this->set("sections", $sections["items"]);
	}

	function viewArea($id = null) {
		// Get selected area
		$area = null ;
		if($id) {
			$this->Area->containLevel("detailed");
			if(!($area = $this->Area->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading area: %d", true), $id));
			}
		}

		$property = $this->BeCustomProperty->setupForView($area, Configure::read("objectTypes.area.id"));

		// Data for template
		$this->set('area',$area);
		$this->set('objectProperty', $property);
		// get users and groups list
		$this->User->displayField = 'userid';
		$this->set("usersList", $this->User->find('list', array("order" => "userid")));
		$this->set("groupsList", $this->Group->find('list', array("order" => "name")));
		$this->set('object', null);
	}

	function viewSection($id = null) {
		$sec = null;
		$this->set('objectProperty', $this->BeCustomProperty->setupForView($sec, Configure::read("objectTypes.section.id"))) ;

        // #578 - Memory exhausted when attempting to create new Section.
        $user = $this->BeAuth->getUserSession();
        $expanded = (!empty($this->params['named']['branch'])) ? array($this->params['named']['branch']) : array();  // #632 - Create new section here issue.
        $this->set('tree', $this->Tree->getAllRoots($user['userid'], null, array('count_permission' => true), $expanded));

		$parentId = null;
		if (empty($id) && !empty($this->params['named']['id'])) {
			$id = $this->params['named']['id'];
		}
		if (!empty($id)) {
			$parentId = $this->Tree->getParent($id);
		} else if (!empty($this->params['named']['branch'])) {
			$parentId = $this->params['named']['branch'];
		}
		$this->set('parent_id', $parentId);
		$this->set('object', null);
	}


	 /**
	  * Add or modify area
	  */
	function saveArea() {
		$this->checkWriteModulePermission();
		$new = (empty($this->data['id'])) ? true : false;
		$this->Transaction->begin();
		if (empty($this->data["syndicate"])) {
			$this->data["syndicate"] = 'off';
		}
		$this->saveObject($this->Area);

		$id = $this->Area->id;
		if(!$new) {

			// remove children
			if (!empty($this->params["form"]["contentsToRemove"])) {
				$childrenToRemove = explode(",", trim($this->params["form"]["contentsToRemove"],","));
				foreach ($childrenToRemove as $idToRemove) {
					$this->Tree->removeChild($idToRemove, $id);
				}
			}

			$reorder = (!empty($this->params["form"]['reorder'])) ? $this->params["form"]['reorder'] : array();

			// add new children and reorder priority
			foreach ($reorder as $r) {
			 	if (!$this->Tree->find("first", array("conditions" => "id=".$r["id"]." AND parent_id=".$id))) {
					$this->Tree->appendChild($r["id"], $id);
				}
				if (!$this->Tree->setPriority($r['id'], $r['priority'], $id)) {
					throw new BeditaException( __("Error during reorder children priority", true), $r["id"]);
				}
			}
		}

	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Area saved", true)." - ".$this->data["title"]);
		$this->eventInfo("area ". $this->data["title"]."saved");
	}

	/**
	 * Save/modify section.
	 */
	function saveSection() {

		$this->checkWriteModulePermission();
		$new = (empty($this->data['id'])) ? true : false;
		$this->Transaction->begin();
		if (empty($this->data["syndicate"])) {
			$this->data["syndicate"] = 'off';
		}
		if(empty($this->data["parent_id"])) {
			throw new BeditaException( __("Missing parent", true));
		}

		$this->saveObject($this->Section);
		$id = $this->Section->id;

		// Move section in the right tree position, if necessary
		if(!$new) {

			if (!$this->BEObject->isFixed($id)) {
				$oldParent = $this->Tree->getParent($id);
				if($oldParent != $this->data["parent_id"]) {
					if(!$this->Tree->move($this->data["parent_id"], $oldParent, $id)) {
						throw new BeditaException( __("Error moving section in the tree", true));
					}
				}
			}

			// save Tree.menu
			$menu = (!empty($this->data['menu']))? 1 : 0;
			$this->Tree->saveMenuVisibility($id, $this->data["parent_id"], $menu);

			// remove children
			if (!empty($this->params["form"]["contentsToRemove"])) {
				$childrenToRemove = explode(",", trim($this->params["form"]["contentsToRemove"],","));
				foreach ($childrenToRemove as $idToRemove) {
					$this->Tree->removeChild($idToRemove, $id);
				}
			}

			$reorder = (!empty($this->params["form"]['reorder'])) ? $this->params["form"]['reorder'] : array();

			// add new children and reorder priority
			foreach ($reorder as $r) {
			 	if (!$this->Tree->find("first", array("conditions" => "id=".$r["id"]." AND parent_id=".$id))) {
					$this->Tree->appendChild($r["id"], $id);
				}
				if (!$this->Tree->setPriority($r['id'], $r['priority'], $id)) {
					throw new BeditaException( __("Error during reorder children priority", true), $r["id"]);
				}
			}
		}

	 	$this->Transaction->commit() ;
		$this->userInfoMessage(__("Section saved", true)." - ".$this->data["title"]);
		$this->eventInfo("section [". $this->data["title"]."] saved");
	}

	function delete() {
		if(empty($this->data['id'])) {
			throw new BeditaException(__("No data", true));
		}
		$ot_id = $this->BEObject->field("object_type_id", array("BEObject.id" => $this->data['id']));
		switch ($ot_id) {
			case Configure::read("objectTypes.area.id"):
				$this->deleteArea();
				break;

			case Configure::read("objectTypes.section.id"):
				$this->deleteSection();
				break;
		}
	}

    /**
     * Export section objects to a specific file format
     */
    public function export() {
        $this->autoRender = false;
        $formData = $this->data;
        // $this->params['form'];
        if (empty($formData['type'])) {
            throw new BeditaException(__('No valid export filter has been selected', true));
        }
        if(empty($formData['filename'])) {
            throw new BeditaException(__('No valid export filename has been selected', true));
        }
        $filterClass = $formData['type'];
        $filterModel = ClassRegistry::init($filterClass);
        $objects = array($formData['id']);
        $options = array(
            'filename' => $formData['filename']
        );
        if (!empty($formData['options'])) {
            $options = array_merge($options,$formData['options']);
        }
        ini_set('max_execution_time', 600); // 10 minutes
        $result = @$filterModel->export($objects, $options);

        Configure::write('debug', 0);
        // TODO: optimizations!!! use cake tools
        header('Content-Description: File Transfer');
        header("Content-Type: {$result['contentType']}");
        header("Content-Disposition: attachment; filename={$formData['filename']}");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header("Content-Length: {$result['size']}");
        ob_clean();
        flush();
        echo $result['content'];
        exit;
    }

    /**
     * Import objects from file in current section
     */
    public function import() {
        $this->checkWriteModulePermission();

        $this->Transaction->begin();
        if (!empty($this->params['form']['Filedata']['name'])) {
            unset($this->data['url']);
            $this->params['form']['forceupload'] = true;
            $streamId = $this->BeUploadToObj->upload($this->data);
        } elseif (!empty($this->data['url'])) {
            $streamId = $this->BeUploadToObj->uploadFromURL($this->data);
        }
        $stream = ClassRegistry::init('Stream');
        $path = $stream->field('uri', array('id' => $streamId));

        if ($this->data['type'] !== 'auto') {
            $filterClass = Configure::read("filters.import.{$this->data['type']}");
        } else {
            // search matching mime types
            $mimeType = $stream->field('mime_type', array('id' => $streamId));
            $filterClass = Configure::read("filters.mime.{$mimeType}.import");
        }

        $this->Section->id = $this->data['sectionId'];
        if (!empty($filterClass)) {
            $filterModel = ClassRegistry::init($filterClass);
            $options = array('sectionId' => $this->data['sectionId']);
            $result = $filterModel->import(Configure::read('mediaRoot') . $path, $options);
            $this->userInfoMessage(__('Objects imported', true) . ': ' . $result['objects']);
            $this->eventInfo("{$result['objects']} objects imported in section {$this->Section->id} from {$path}");
        } else {
            $this->userErrorMessage(__('No import filter found for file type', true) . ' : ' . $mimeType);
            $this->eventError('Import filter not found for type ' . $mimeType);
        }
        if (!$this->BeFileHandler->del($streamId)) {
            throw new BeditaException(__('Error deleting object: ', true) . $streamId);
        }
        $this->Transaction->commit();
    }

	private function deleteArea() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Area");
		$this->userInfoMessage(__("Area deleted", true)." - ".$objectsListDeleted);
		$this->eventInfo("area [". $objectsListDeleted."] deleted");
	}

	private function deleteSection() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Section");
		$this->userInfoMessage(__("Section deleted", true)." - ".$objectsListDeleted);
		$this->eventInfo("section [". $objectsListDeleted."] deleted");
	}

	 /**
	  * Return associative array representing publications/sections tree
	  *
	  * @param unknown_type $data
	  * @param unknown_type $tree
	  */
	private function _getTreeFromPOST(&$data, &$tree) {
		$tree = array() ;
		$IDs  = array() ;
		// Creating subtrees
		$arr = preg_split("/;/", $data) ;
		for($i = 0 ; $i < count($arr) ; $i++) {
			$item = array() ;
			$tmp = split(" ", $arr[$i] ) ;
			foreach($tmp as $val) {
				$t  = split("=", $val) ;
				$item[$t[0]] = ($t[1] == "null") ? null : ((integer)$t[1]) ;
			}
			$IDs[$item["id"]] 				= $item ;
			$IDs[$item["id"]]["children"] 	= array() ;
		}
		// Creating the tree
		foreach ($IDs as $id => $item) {
			if(!isset($item["parent"])) {
				$tree[] = $item ;
				$IDs[$id] = &$tree[count($tree)-1] ;
			}
			if(isset($IDs[$item["parent"]])) {
				$IDs[$item["parent"]]["children"][] = $item ;
				$IDs[$id] = &$IDs[$item["parent"]]["children"][count($IDs[$item["parent"]]["children"])-1] ;
			}
		}
		unset($IDs) ;
	}

	public function categories() {
		$this->showCategories($this->Section);
	}
	
    protected function forward($action, $result) {
        $moduleRedirect = array(
            'saveArea' => array(
                'OK'	=> "/areas/view/{$this->Area->id}",
                'ERROR'	=> $this->referer()
            ),
            'saveSection'	=> 	array(
                'OK'	=> "/areas/view/{$this->Section->id}",
                'ERROR'	=> $this->referer()
            ),
            'delete'	=> 	array(
                'OK'	=> '/areas',
                'ERROR'	=> $this->referer()
            ),
            'deleteSection'	=> 	array(
                'OK'	=> '/areas',
                'ERROR'	=> $this->referer()
            ),
            'deleteArea'	=> 	array(
                'OK'	=> '/areas',
                'ERROR'	=> $this->referer()
            ),
            'import'	=> 	array(
                'OK'	=> "/areas/view/{$this->Section->id}",
                'ERROR'	=> $this->referer()
            ),
            'export'	=> 	array(
                'OK'	=> $this->referer(),
                'ERROR'	=> $this->referer()
            ),
        );
        return $this->moduleForward($action, $result, $moduleRedirect);
    }
}

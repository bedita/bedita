<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008-2015 ChannelWeb Srl, Chialab Srl
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
 * AddressBook Module Controller
 */
class AddressbookController extends ModulesController {
	
	var $name = 'Addressbook';
	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'BeCustomProperty', 'BeLangText', 'BeFileHandler', 'BeSecurity');

	var $uses = array('BEObject','Tree', 'Category', 'Card', 'MailGroup') ;
	protected $moduleName = 'addressbook';
	protected $categorizableModels = array('Card');
	
    public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$filter["object_type_id"] = $conf->objectTypes['card']["id"];
		$filter["Card.country"] = "";
		$filter["Card.email"] = "";
		$filter["Card.company_name"] = "";
		$filter["object_user"] = "card";
		$filter["count_annotation"] = "EditorNote";
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim); 
		$this->loadCategories($filter["object_type_id"]);
		$this->loadMailgroups();
	 }

	function view($id = null) {
		if($id == null) {
			Configure::write("defaultStatus", "on"); // set default ON for new objects
		}
		$this->viewObject($this->Card, $id);
		$this->set("groupsByArea", $this->MailGroup->getGroupsByArea(null, $id));

		include CONFIGS . 'countries.iso.php';
		$this->set('country_list_iso', $config['countryList']);
	}

	function save() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin();
        $conf  = Configure::getInstance() ;

        $kind = ($this->data['company']==0) ? 'person' : 'cmp';
		if($kind == 'person') {
			if(!empty($this->data['name']) || !empty($this->data['surname'])) {
				$this->data['title'] = $this->data['name']." ".$this->data['surname'];
			}
        } else {
			if(!empty($this->data['company_name'])) {
				$this->data['title'] = $this->data['company_name'];
			}
		}

		if(empty($this->data['User'][0])) {
			$this->data['User'] = array();
		}


		$this->saveObject($this->Card);
	 	$this->Transaction->commit();
	 	if(empty($this->data["title"])) {
	 		$this->data["title"] = "";
	 	}
		$this->userInfoMessage(__("Card saved", true)." - ".$this->data["title"]);
		$this->eventInfo("card [". $this->data["title"]."] saved");
	}

	function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Card");
		$this->userInfoMessage(__("Card deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("card $objectsListDeleted deleted");
	}

	function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Card");
		$this->userInfoMessage(__("Cards deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("cards $objectsListDeleted deleted");
	}
	
	public function categories() {
		$this->showCategories($this->Card);
	}

	public function cloneObject() {
		unset($this->data['ObjectUser']);
		parent::cloneObject();
	}

	private function loadMailgroups() {
		$result = ClassRegistry::init("MailGroup")->find("all",
			array(
				"fields" => array("id","group_name"),
				"contain" => array()
			)
		);
		$mailgroups = array();
		foreach($result as $k => $v) {
			$mailgroups[$k] = $v['MailGroup'];
		}
		$this->set("mailgroups",$mailgroups);
	}

	public function addToMailgroup() {
		$this->checkWriteModulePermission();
		$counter = 0;
		$missingEmail = 0;
		if(!empty($this->params['form']['objects_selected'])) {
			$objects_to_assoc = $this->params['form']['objects_selected'];
			$mailgroup = $this->data['mailgroup'];
			$MailGroupObj = ClassRegistry::init("MailGroupCard");
			$this->Transaction->begin() ;
			for($i = 0; $i < count($objects_to_assoc); $i++) {
				// get email from  card
				$email = $this->Card->field("newsletter_email", array("id" => $objects_to_assoc[$i]));
				if(!empty($email)) { // if 'newsletter_email' skip saving
					$data = array(
						"card_id"=>$objects_to_assoc[$i],
						"mail_group_id" => $mailgroup
					);
					$mg = $MailGroupObj->find("first",array('conditions' => $data));
					if(empty($mg)) { // if relation already exists, skip saving
						$data["status"] = "confirmed";
						$MailGroupObj->create();
						$MailGroupObj->save($data);
						$counter++;
					}
				} else {
					$missingEmail++;
				}
			}
			$this->Transaction->commit() ;

			$userMsg = '';
			if ($missingEmail > 0) {
				$userMsg = $missingEmail . ' ' . __('cards without email not added to mailgroup', true) . ', ';
			}
			$userMsg .= $counter . ' ' . __("card(s) associated to mailgroup", true);
			$this->userInfoMessage($userMsg);
			$this->eventInfo("$counter card(s) associated to mailgroup id: " . $mailgroup);
		}
	}

	
    /**
     * Export objects to CSV, using current filter
     */
    public function exportCsv() {
        $this->autoRender = false;
        $data = $this->loadDataFromSessionFilter();
        $result = array();
        $result['contentType'] = 'text/csv';
        $result['filename'] = $this->name . '_n-' . count($data) . '_' . date('Y.m.d') . '.csv';
        $options = array();
        if (Configure::read('csvFields.card')) {
            $options['delimiter'] = ';';
            $options['custom'] = true;
        }
        $result['content'] = $this->Card->createCsvAsString($data, $options);
        $result['size'] = strlen($result['content']);

        Configure::write('debug', 0);
        header('Content-Description: File Transfer');
        header('Content-type: ' . $result['contentType']);
        header('Content-Disposition: attachment; filename='. $result['filename']);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $result['size']);
        ob_clean();
        flush();
        echo $result['content'];
        exit();
    }

    private function loadDataFromSessionFilter() {
        $filter = array();
        $filter['object_type_id'] = Configure::read('objectTypes.card.id');
        $filter['Card.*'] = '';
        $filterKey = $this->name . '.index';
        $this->SessionFilter->setup($filterKey);
        $sessionFilter = $this->SessionFilter->setFromUrl();
        $filter = array_merge($filter, $sessionFilter);
        $objects = $this->BeTree->getChildren(null, null, $filter);
        return $objects['items'];
    }

    protected function forward($action, $result) {
        $moduleRedirect = array(
            'addToMailgroup'	=> 	array(
                'OK'	=> $this->referer(),
                'ERROR'	=> $this->referer()
            )
        );
        return $this->moduleForward($action, $result, $moduleRedirect);
    }

}

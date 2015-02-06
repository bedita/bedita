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
 * AddressBook Module Controller
 */
class AddressbookController extends ModulesController {
	
	var $name = 'Addressbook';
	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

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
				}
			}
			$this->Transaction->commit() ;
			$this->userInfoMessage("$counter" . __("card(s) associated to mailgroup", true) . " - " . $mailgroup);
			$this->eventInfo("$counter card(s) associated to mailgroup " . $mailgroup);
		}
	}

    // #573 - Automatic Card creation.
    /**
     * Returns a JSON object with an array of "similar" Cards to the given User data, excluding Cards already related to another User.
     * 
     * A Card is considered "similar" to a User if any of the following conditions are `true`:
     *  1. `Card.email = User.email`
     *  2. `Card.email2 = User.email`
     *  3. `Card.name` is a substring of `User.realname` *AND* `Card.surname` is a substring of `User.realname`
     */
    public function similarCards() {
        // Prepare data.
        $userId = (isset($this->params['form']['id']) && is_numeric($this->params['form']['id'])) ? $this->params['form']['id'] : 0;
        $name = Sanitize::escape($this->params['form']['name'], 'default');  // Needs manual escape!! See query conditions few lines below for potential threat.
        $email = $this->params['form']['email'];

        // Search for similar Cards.
        $cards = $this->Card->find('all', array(
            'fields' => array('Card.id', 'Card.name', 'Card.surname', 'Card.email', 'Card.email2'),
            'contain' => array(),
            'joins' => array(
                array(
                    'table' => 'object_users',
                    'alias' => 'ObjectUser',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'ObjectUser.object_id = Card.id',
                        'ObjectUser.switch' => 'card',
                        'ObjectUser.user_id <>' => $userId,
                    ),
                ),
            ),
            'conditions' => array(
                'ObjectUser.user_id' => null,
                'OR' => array(
                    // See if email address matches.
                    'Card.email' => $email,
                    'Card.email2' => $email,
                    // See if full name matches somehow.
                    'AND' => array(
                        "'{$name}' LIKE CONCAT('%', Card.name, '%')",  // `$name` MUST be properly escaped!
                        "'{$name}' LIKE CONCAT('%', Card.surname, '%')",  // (same here)
                    ),
                ),
            ),
            'limit' => 25,  // Keeping our feet on the ground.
        ));

        // Output as JSON.
        $this->layout = 'ajax';
        $this->set('cards', $cards);
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

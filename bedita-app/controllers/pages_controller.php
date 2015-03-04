<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008, 2009, 2010 ChannelWeb Srl, Chialab Srl
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
 * 
 *
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $LastChangedDate$
 * 
 * $Id$
 */
class PagesController extends AppController {
    
    public $uses = array();
    public $helpers = array('BeTree');
    public $components = array(
        'BeUploadToObj',
        'BeSecurity' => array(
            'disableActions' => array('loadNote', 'loadObjectToAssoc', 'loadUsersGroupsAjax')
        )
    );

    protected function beforeCheckLogin() {
        if($this->action === 'changeLang') { // skip auth check, on lang change
            $this->skipCheck = true;
        }
    }
        
    function changeLang($lang = null) {
        if (!empty($lang)) {
            $this->Session->write('Config.language', $lang);
            $this->Cookie->write('bedita.lang', $lang, null, '+350 day'); 
        }
        $this->redirect($this->referer());
    }
    
    /**
     * Print an object 
     */
    public function printme() {
        $id = $this->params["form"]["id"];
        $printLayout = $this->params["form"]["printLayout"];
        if (!empty($this->params["form"]["printcontext"])) {
            $publication_url = ClassRegistry::init("Area")->field("public_url", array("id" => $this->params["form"]["printcontext"]));
            if (!empty($publication_url)) {
                $this->redirect($publication_url . "/printme/" . $id . "/" . $printLayout);
            }
        }
        $object_type_id = ClassRegistry::init("BEObject")->findObjectTypeId($id);
        $objectModel = $this->loadModelByObjectTypeId($object_type_id);
        $objectModel->containLevel("detailed");
        if (!$objectData = $objectModel->findById($id)) {
            throw new BeditaException(__("Error finding object", true));
        }
        if (!empty($objectData['RelatedObject'])) {
            $objectData['relations'] = $this->objectRelationArray($objectData['RelatedObject']);
        }
        if (!empty($objectData['Annotation'])) {
            $this->setupAnnotations($objectData);
        }
        $this->layout = "print";
        $this->set("printLayout", $printLayout);
        $this->set("object", $objectData);
        if (file_exists(APP."views".DS."pages".DS.$printLayout.".tpl"))
            $this->render($printLayout);
        else
            $this->render("print");
        
    }   
    

    /* AJAX CALLS */

    /**
     * called via ajax
     * Show list of objects for relation, append to section,...
     * 
     * @param int $main_object_id, object id of main object used to exclude association with itself 
     * @param string $relation, relation type
     * @param int $main_object_type_id, object_type_id of main object. Used if $main_object_id is not defined or empty
     * @param string $objectType name of objectType to filter. It has to be a string that defined a group of type
     *                            defined in bedita.ini.php (i.e. 'related' 'leafs',...)
     *                            Used if $this->parmas["form"]["objectType"] and $relation are empty   
     * 
     **/
    public function showObjects($main_object_id = null, $relation = null, $main_object_type_id = null, $objectType = "related") {
        $this->ajaxCheck();
        // clean session filter
        if (empty($this->params['form']['filter'])) {
            $this->SessionFilter->clean();
        }

        $filter = array();
        $excludeIds = array();
        $conf = Configure::getInstance();
        
        if (!empty($relation)) {
            
            $relTypes = BeLib::getObject("BeConfigure")->mergeAllRelations();
            $usedRelation = $relation;
            if (empty($relTypes[$relation])) {
                foreach ($relTypes as $n => $r) {
                    if (!empty($r["inverse"]) && $r["inverse"] == $relation) {
                        $usedRelation = $n;
                    }
                }
            }
                        
            if (!empty($relTypes[$usedRelation])) {
                
                if (!empty($main_object_id)) {
                    $main_object_type_id = ClassRegistry::init("BEObject")->field("object_type_id", array("id" => $main_object_id));
                }
                
                $objectTypeName = Configure::read("objectTypes." . $main_object_type_id . ".name");

                if (!empty($relTypes[$usedRelation][$objectTypeName])) {
                    $ot = $relTypes[$usedRelation][$objectTypeName];
                } else {
                    $addRight = array();
                    if (key_exists("left", $relTypes[$usedRelation])) {
                        // if 'left' is empty means that in the 'left' you have all objects in 'related' group => get right relations
                        // or if $objectTypeName is in the 'left' => get right relations
                        if (empty($relTypes[$usedRelation]["left"])
                                || (is_array($relTypes[$usedRelation]["left"]) && in_array($objectTypeName, $relTypes[$usedRelation]["left"]))
                                || $relTypes[$usedRelation]["left"] === $objectTypeName) {
                            if (!empty($relTypes[$usedRelation]["right"])) {
                                $addRight = $relTypes[$usedRelation]["right"];
                            } else {
                                $addRight = $conf->objectTypes["related"]["id"];
                            }
                        }
                    }

                    $addLeft = array();
                    if (key_exists("right", $relTypes[$usedRelation])) {
                        // if 'right' is empty means that in the 'right' you have all objects in 'related' group => get left relations
                        // or if $objectTypeName is in the 'right' => get left relations
                        if (empty($relTypes[$usedRelation]["right"])
                                || (is_array($relTypes[$usedRelation]["right"]) && in_array($objectTypeName, $relTypes[$usedRelation]["right"]))
                                || $relTypes[$usedRelation]["right"] === $objectTypeName) {
                            if (!empty($relTypes[$usedRelation]["left"])) {
                                $addLeft = $relTypes[$usedRelation]["left"];
                            } else {
                                $addLeft = $conf->objectTypes["related"]["id"];
                            }
                        }
                    }
                    
                    if (!is_array($addRight)) {
                        $addRight = array($addRight);
                    }
                    if (!is_array($addLeft)) {
                        $addLeft = array($addLeft);
                    }
                    
                    // if relation has not "inverse" use left and right types
                    if (empty($relTypes[$usedRelation]["inverse"])) {
                        $ot = array_unique(array_merge($addRight, $addLeft));
                    } else {
                        // otherwise use "right" types on "direct" relations, "left" types on "inverse" relations
                        if($usedRelation === $relation) {
                            $ot = $addRight;
                        } else {
                            $ot = $addLeft;
                        }
                    }
                }

                $objectTypeIds = array();
                foreach ($ot as $val) {
                    $objectTypeIds[] = $conf->objectTypes[$val]["id"];
                }
            }

        } else {
            // if set param named group get leafs + section + area and only objects without permission on that group
            if (!empty($this->params['named']['group'])) {
                $objectType = 'all';
                $permission = ClassRegistry::init('Permission');
                $objIdsWithPerms = $permission->find('list', array(
                    'fields' => array('object_id'),
                    'conditions' => array('switch' => 'group', 'ugid' => $this->params['named']['group'])
                ));
                $excludeIds = array_merge($excludeIds, $objIdsWithPerms);
            }

            if ($objectType == 'all') {
                $leafsIds = Configure::read("objectTypes.leafs.id");
                $collectionIds = array(Configure::read('objectTypes.area.id'), Configure::read('objectTypes.section.id'));
                $objectTypeIds = array_merge($leafsIds, $collectionIds);
            } else {
                $objectTypeIds = Configure::read("objectTypes." . $objectType . ".id");
            }
        }

        $objectTypeIds = (is_array($objectTypeIds))? $objectTypeIds : array($objectTypeIds);
        $filter["object_type_id"] = $objectTypeIds;

        $page = (!empty($this->params["form"]["page"]))? $this->params["form"]["page"] : 1;

        // set id to exclude: $main_object_id and already related objects
        if (!empty($main_object_id)) {
            $res = ClassRegistry::init("ObjectRelation")->find("all", array(
                "conditions" => array(
                    "id" => $main_object_id,
                    "switch" => @$usedRelation
                )
            ));
            $excludeIds = array_merge($excludeIds, Set::extract("/ObjectRelation/object_id", $res));
            $excludeIds[] = $main_object_id;
        }

        if (!empty($excludeIds)) {
            $filter["BEObject.id"] = array("NOT" => $excludeIds);
        }
        
        $filter = array_merge($filter, $this->SessionFilter->read());

        $relationRulesClass = Inflector::camelize($relation)."RelationRules";
        if (App::import("model", $relationRulesClass) ) {
            $model = ClassRegistry::init($relationRulesClass);  
            $params = array("object_type_id" => $main_object_type_id , "object_id" => $main_object_id );
            $model->connectFilter($params, $filter);
        }
        
        if ($filter !== null) {
            $objects = $this->BeTree->getChildren(null, null, $filter, "modified", false, $page, $dim=20) ;
        } else  {
            $objects["items"] = array();
        }

        foreach ($objects["items"] as $key => $obj) {
            $objects["items"][$key]["moduleName"] = ClassRegistry::init("ObjectType")->field("module_name", array("id" => $obj["object_type_id"]));

            // get image and video details
            if ($obj['object_type_id'] == Configure::read("objectTypes.image.id") || $obj['object_type_id'] == Configure::read("objectTypes.video.id")) {
                $mediaModelName = Configure::read("objectTypes." . $obj['object_type_id'] . ".model");
                $mediaData = ClassRegistry::init($mediaModelName)->find('first', array(
                    'conditions' => array('Stream.id' => $obj['id']),
                    'contain' => array('Stream')
                ));
                $objects["items"][$key] = array_merge($objects["items"][$key], $mediaData);
            }
        }

        // get publications
        $treeModel = ClassRegistry::init("Tree");
        $user = $this->BeAuth->getUserSession();
        $expandBranch = array();
        if (!empty($filter['parent_id'])) {
            $expandBranch[] = $filter['parent_id'];
        } elseif (!empty($id)) {
            $expandBranch[] = $id;
        }
        $tree = $treeModel->getAllRoots($user['userid'], null, array('count_permission' => true), $expandBranch);

        // get available relations
        $availableRelations = array();
        if (!empty($objectTypeIds)) {
            foreach ($objectTypeIds as $objectTypeId) {
                $r = ClassRegistry::init('ObjectRelation')->availableRelations($objectTypeId);
                $availableRelations = array_merge($availableRelations, $r);
            }
        }

        $this->set("objectsToAssoc", $objects);
        $this->set('tree', $tree);
        $this->set('availableRelations', $availableRelations);
        $this->set("relation", $relation);
        $this->set("main_object_id", $main_object_id);
        $this->set("object_type_id", $main_object_type_id);
        $this->set("objectType", $objectType);
        $this->set("objectTypeIds", $objectTypeIds);
                
        if (!empty($this->params["form"])) {
            $this->render("list_contents_to_assoc");
        }
    }
    
    /**
     * called via ajax
     * load objects selected to main view to prepare association form
     *
     * @param int $main_object_id, object id of main object used to exclude association with itself 
     * @param string $objectType, object type used to filter
     * @param string $tplname, template name without '.tpl' 
     *               if it contains dots replace it with /
     *               i.e. areas.inc.list_object become areas/inc/list_object.tpl
     *                
     */
    public function loadObjectToAssoc($main_object_id = null, $objectType = null, $tplname = null) {
        $this->ajaxCheck();
        $tplname = (!empty($this->params["form"]["tplname"]))? $this->params["form"]["tplname"] : $tplname;
        $relation = (!empty($this->params["form"]["relation"]))? $this->params["form"]["relation"] : null;

        $conditions = array("BEObject.id" => explode( ",", trim($this->params["form"]["object_selected"],",") ));
        
        if (!empty($objectType)) {
            $conditions["BEObject.object_type_id"] = Configure::read("objectTypes." . $objectType . ".id");
        }
        
        $objects = ClassRegistry::init("BEObject")->find("all", array(
                                                    "contain" => array("ObjectType"),
                                                    "conditions" => $conditions
                                                )
                                        ) ;
        $objRelated = array();

        // build permissions array to add to every object
        if (!empty($this->params['form']['permission'])) {
            $permissions = array();
            foreach ($this->params['form']['permission'] as $flag) {
                $permissions[] = array('flag' => $flag);
            }
        }

        foreach ($objects as $key => $obj) {
            if (empty($main_object_id) || $objects[$key]["BEObject"]["id"] != $main_object_id) {
                $obj["BEObject"]["module_name"] = $obj["ObjectType"]["module_name"];

                // get image and video details
                if ($obj["BEObject"]['object_type_id'] == Configure::read("objectTypes.image.id") || $obj["BEObject"]['object_type_id'] == Configure::read("objectTypes.video.id")) {
                    $mediaModelName = Configure::read("objectTypes." . $obj["BEObject"]['object_type_id'] . ".model");
                    $mediaData = ClassRegistry::init($mediaModelName)->find('first', array(
                        'conditions' => array('Stream.id' => $obj["BEObject"]['id']),
                        'contain' => array('Stream')
                    ));
                    $obj["BEObject"] = array_merge($obj["BEObject"], $mediaData);
                // for other media file get streams.*
                } elseif (in_array($obj["BEObject"]['object_type_id'], Configure::read("objectTypes.multimedia.id"))) {
                    $streamFields = ClassRegistry::init("Stream")->find("first", array(
                            "conditions" => array(
                                "id" => $obj["BEObject"]["id"]
                            )
                        )
                    );
                    $obj["BEObject"] = array_merge($obj["BEObject"], $streamFields["Stream"]);
                }

                $obj = array_merge($obj["BEObject"], array("ObjectType" => $obj["ObjectType"]));

                if (isset($permissions)) {
                    $obj['Permission'] = $permissions;
                }

                $objRelated[] = $obj;
            }
        }

        $this->set("objsRelated", $objRelated);
        $this->set("rel", $relation);
        $tplname = (empty($tplname))? "elements/form_assoc_object.tpl" : str_replace(".", "/", $tplname) . ".tpl";
        $this->render(null, null, VIEWS . $tplname);
    }
    
    /**
     * load user or group list
     */
    public function loadUsersGroupsAjax() {
        $this->ajaxCheck();
        if($this->params['form']['itype'] == 'user') {
            $userModel = ClassRegistry::init("User");
            $userModel->displayField = 'userid';
            $this->set("itemsList", $userModel->find('list', array("order" => "userid")));
        } else if($this->params['form']['itype'] == 'group') {
            $this->set("itemsList", ClassRegistry::init("Group")->find('list', array("order" => "name")));
        }
    }
    
    /**
     * save editor note
     * if it fails throw BeditaAjaxException managed like json object
     */
    public function saveNote() {
        $this->ajaxCheck();
        if (empty($this->data["object_id"]))
            throw new BeditaAjaxException(__("Missing referenced object. Save new item before adding a note", true), array("output" => "json"));
        
        $this->Transaction->begin();
        try {
            $editorNoteModel = ClassRegistry::init("EditorNote");
            $this->saveObject($editorNoteModel);
            $this->Transaction->commit();
            $this->set("data", array("id" => $editorNoteModel->id));
            $this->view = "View";
            header("Content-Type: application/json");
            $this->render("json");
        } catch (BeditaException $ex) {
            $errorMsg = "Error saving note";
            throw new BeditaAjaxException(__("Error saving note", true), array_merge($editorNoteModel->validationErrors, array("output" => "json")));
        }
    }
    
    /**
     * load an editor note
     */
    public function loadNote() {
        $this->ajaxCheck();
        $editorNoteModel = ClassRegistry::init("EditorNote");
        $this->set("note", $editorNoteModel->find("first", array(
                                    "conditions" => array("EditorNote.id" => $this->params["form"]["id"]))
                                )
                    );
    }
    
    public function deleteNote() {
        $this->ajaxCheck();
        if (empty($this->params["form"]["id"]))
            throw new BeditaAjaxException(__("Error deleting note, missing id", true), array("output" => "json"));
        
        $this->data["id"] = $this->params["form"]["id"];
        try {
            $objectsListDeleted = $this->deleteObjects("EditorNote");
            $this->eventInfo("editor note $objectsListDeleted deleted");
            $this->set("data", array("id" => $objectsListDeleted));
            $this->view = "View";
            $this->render("json");
        } catch (BeditaException $ex) {
            throw new BeditaAjaxException(__("Error deleting note", true), array("output" => "json"));
        }
    }
    
    /**
      * Add Link with Ajax...
      */
    public function addLink() {
        $this->ajaxCheck();
        $this->layout = "ajax";
        $this->data = $this->params['form'];
        $this->data["status"] = "on";
        $this->Transaction->begin() ;
        $linkModel = $this->loadModelByType("Link");
        $this->data['url'] = $linkModel->checkUrl($this->data['url']);
        
        $link = $linkModel->find('all',array('conditions' =>array('url' => $this->data['url'])));
        if(!empty($link)) {
            $linkModel->id = $link[0]['id'];
            if(empty($this->data['title'])) {
                $this->data['title'] = $link[0]['title'];
            }
        } else {
            if(empty($this->data['title'])) { // try to read title from URL directly
                $this->data['title'] = $linkModel->readHtmlTitle($this->data['url']);
            }
            if(!$linkModel->save($this->data)) {
                throw new BeditaAjaxException(__("Error saving link", true), $linkModel->validationErrors);
            }
        }
        $this->Transaction->commit() ;
        if(empty($link)) {
            $this->eventInfo("link [". $this->data["title"]."] saved");
        }
        $this->data["id"] = $linkModel->id;
        $this->set("objRelated", $this->data);
     }

    private function ajaxCheck() {
        if (!$this->RequestHandler->isAjax()) {
            exit;
        }
        $this->layout="ajax";
    }

    /**
     * Ajax update of current object editors/viewers
     *
     * @param int $objectId - object id
     */
    public function updateEditor($objectId) {
        // TODO: check perms on object/module
        $this->ajaxCheck();
        $objectEditor = ClassRegistry::init("ObjectEditor"); 
        $user = $this->Session->read("BEAuthUser");
        $objectEditor->cleanup($objectId);
        $objectEditor->updateAccess($objectId, $user["id"]);
        $res = $objectEditor->loadEditors($objectId);
        $this->set("editors", $res);
    }
    
    public function showAjaxMessage() {
        $this->ajaxCheck();
        $methodName = 'user'.ucfirst($this->params['form']['type']).'Message';
        $this->{$methodName}($this->params['form']['msg']);
        $this->render(null, null, "/elements/flash_messages");
    }
    
    /**
     * Show object revision information (specific revision)
     *
     * @param int $id, object id
     * @param int $rev, revision number
     */
    public function revision($id, $rev) {
        $beObject = ClassRegistry::init("BEObject"); 
        $modelName = $beObject->getType($id);
        $model = $this->loadModelByType($modelName);
        $this->viewRevision($model, $id, $rev);
    }

    public function tree($parentid) {
        $this->layout = 'ajax';
        if (empty($parentid)) {
            $this->set('tree', $this->BeTree->getSectionsTree());
        } else {
            $this->set('tree', $this->BeTree->getPublicationTree($parentid));
        }
    }

    /**
     * Ajax modal for export 
     *
     * @param int $objectId - object id / all TODO
     */
    public function export($objectId) { 
        $this->set("objectId", $objectId);
        $this->render(null, null, "form_export");
    }
    
    public function import($objectId) { 
        $this->set("objectId", $objectId);
        $this->render(null, null, "form_import");
    }
    

    /**
     * save quick item
     * used in modal window to save quickly objects to associate
     * to main object
     *
     * @return void
     */
    public function saveQuickItem() {
        if ($this->RequestHandler->isAjax()) {
            if (empty($this->data['object_type_id'])) {
                throw new BeditaAjaxException(__('Missing object type', true), array('output' => 'json'));
            }
        } else {
            if (empty($this->data['object_type_id'])) {
                throw new BeditaAjaxException(__('Missing object type', true));
            }
        }
        
        $id = null;

        try {
            $this->Transaction->begin();
            // if it's multimedia object and a file was loaded
            $multimediaIds = Configure::read('objectTypes.multimedia.id');
            if (in_array($this->data['object_type_id'], $multimediaIds) && (!empty($this->params['form']['Filedata']) || !empty($this->data['url']))) {
                try {
                    if (!empty($this->params['form']['Filedata'])) {
                        $this->data['id'] = $this->BeUploadToObj->upload();
                    } else {
                        $this->data['id'] = $this->BeUploadToObj->uploadFromURL($this->data);
                    }
                } catch (BEditaFileExistException $ex) {
                    // prepare data to touch multimedia object (to show it on top of object list)
                    // or to create new one if title and description are different
                    $mediaId = $ex->getObjectId();
                    $newMedia = false;
                    $regExpPattern = '/\s|\n|\r/';
                    $title = preg_replace($regExpPattern, '', $this->data['title']);
                    $desc = preg_replace($regExpPattern, '', $this->data['description']);
                    if (!empty($title) || !empty($desc)) {
                        $beObject = ClassRegistry::init('BEObject');
                        $mediaData = $beObject->find('first', array(
                            'fields' => array('title', 'description'),
                            'conditions' => array('id' => $mediaId),
                            'contain' => array()
                        ));

                        $objTitle = $mediaData['BEObject']['title'];
                        $objTitle = preg_replace($regExpPattern, '', $objTitle);
                        $objDesc = strip_tags($mediaData['BEObject']['description']);
                        $objDesc = preg_replace($regExpPattern, '', $objDesc);

                        // if title or description are different to form data prepare to create new media object
                        if ( (!empty($title) && $objTitle != $title) || (!empty($desc) && $objDesc != $desc) ) {
                            $newMedia = true;
                        }
                    }

                    // if media is not new then touch the object
                    if (!$newMedia) {
                        unset($this->data['title']);
                        unset($this->data['description']);
                        unset($this->data['destination']);
                        $this->data['id'] = $mediaId;
                    } else {
                        $this->params['form']['forceupload'] = true;
                        $this->data['id'] = $this->BeUploadToObj->upload();
                    }

                } catch (BEditaException $ex) {
                    throw new BeditaException($ex->getMessage(), $ex->getDetails());
                }
            }
            $modelName = Configure::read('objectTypes.' . $this->data['object_type_id'] . '.model');
            $model = ClassRegistry::init($modelName);
            $this->saveObject($model);
            $this->Transaction->commit();
            $id = $model->id;
        } catch (BeditaException $ex) {
            if ($this->RequestHandler->isAjax()) {
                throw new BeditaAjaxException($ex->getMessage(), array('output' => 'json', 'headers' => 'HTTP/1.1 500 Internal Server Error'));
            } else {
                throw new BeditaAjaxException($ex->getMessage(), array('headers' => 'HTTP/1.1 500 Internal Server Error'));
            }
        }

        if ($this->RequestHandler->isAjax()) {
            $this->RequestHandler->respondAs('json');
            // jsonize object saved
            $model->containLevel('detailed');
            $object = $model->findById($model->id);
            $this->set('data', $object);
            $this->view = 'View';
            $this->action = 'json';
        } else {
            if (!empty($id)) {
                $this->redirect('/view/'. $id);
            }
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
        $cards = ClassRegistry::init('Card')->find('all', array(
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

        $this->layout = 'ajax';
        $this->set('cards', $cards);
        $this->render('/addressbook/similar_cards');
    }
}

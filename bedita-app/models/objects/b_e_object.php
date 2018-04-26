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
 * BEObject class
 *
 */
class BEObject extends BEAppModel {

    public $actsAs = array('Cacheable');

    var $name = 'BEObject';
    var $useTable	= "objects" ;

    private $defaultIp = "::1"; // default IP addr for saved objects

    var $validate = array(
//		'title' => array(
//			'rule' => 'notEmpty'
//		),
        'object_type_id' => array(
            'rule' => 'notEmpty'
        ),
        'nickname' => array(
            'rule' => 'notEmpty'
        ),
        'lang' => array(
            'rule' => 'notEmpty'
        ),
        'ip_created' => array(
            'rule' => 'ip'
        ),
        'status' => array(
            'rule' => array('inList', array('on', 'off', 'draft'))
        ),
    ) ;

    var $belongsTo = array(
        'ObjectType' =>
            array(
                'className'		=> 'ObjectType',
                'foreignKey'	=> 'object_type_id',
                'conditions'	=> ''
            ),
        'UserCreated' =>
            array(
                'className'		=> 'User',
                'fields'		=> 'id, userid, realname',
                'foreignKey'	=> 'user_created',
            ),
        'UserModified' =>
            array(
                'className'		=> 'User',
                'fields'		=> 'id, userid, realname',
                'foreignKey'	=> 'user_modified',
            ),
    ) ;

    var $hasMany = array(
        'Permission',

        'Version' =>
            array(
                'className'		=> 'Version',
                'foreignKey'	=> 'object_id',
                'dependent'		=> true
            ),

        'ObjectProperty' =>
            array(
                'className'		=> 'ObjectProperty',
                'foreignKey'	=> 'object_id',
                'dependent'		=> true
            ),
        'SearchText' =>
            array(
                'foreignKey'	=> 'object_id',
                'dependent'		=> true
            ),
        'LangText' =>
            array(
                'className'		=> 'LangText',
                'foreignKey'	=> 'object_id',
                'dependent'		=> true
            ),
        'Annotation' =>
            array(
                'foreignKey'	=> 'object_id',
                'dependent'		=> true
            ),
        'RelatedObject' =>
            array(
                'className'				=> 'ObjectRelation',
                'joinTable'    			=> 'object_relations',
                'foreignKey'   			=> 'id',
                'associationForeignKey'	=> 'object_id',
                'order'					=> 'priority'
            ),
        'Alias',
        'GeoTag' =>
            array(
                'foreignKey'	=> 'object_id',
                'dependent'		=> true
            )

        );

    var $hasAndBelongsToMany = array(
        'Category' =>
            array(
                'className'				=> 'Category',
                'joinTable'    			=> 'object_categories',
                'foreignKey'   			=> 'object_id',
                'associationForeignKey'	=> 'category_id',
                'unique'				=> true
            ),
        'User' =>
               array(
                'className'    			=> 'User',
                'joinTable'       		=> 'object_users',
                'foreignKey'      		=> 'object_id',
                'associationForeignKey' => 'user_id',
                'unique'    			=> true,
                   'with' 					=> 'ObjectUser'
               )
    );

    /**
     * Format object data (ObjectProperty, Tag, Category, LangText, Permission)
     *
     * If ObjectProperty is populated a simplified customProperties array (useful in frontend apps) is built as
     *
     * 	"customProperties" => array(
     *  	"prop_name" => "prop_val",
     *   	"prop_name_multiple_choice" => array("prop_val_1", "prop_val_2")
     *  )
     */
    function afterFind($result) {

        // format object properties
        if(!empty($result['ObjectProperty'])) {
            $result["customProperties"] = array();
            $propertyModel = ClassRegistry::init("Property");
            $property = $propertyModel->find("all", array(
                                "conditions" => array("object_type_id" => $result["object_type_id"]),
                                "contain" => array("PropertyOption")
                            )
                        );

            foreach ($property as $keyProp => $prop) {

                foreach ($result["ObjectProperty"] as $k => $value) {
                    if ($value["property_id"] == $prop["id"]) {
                        if ($prop["multiple_choice"] != 0) {
                            $property[$keyProp]["value"][] = $value;
                            $result["customProperties"][$prop["name"]][] = $value["property_value"];
                        } else {
                            $property[$keyProp]["value"] = $value;
                            $result["customProperties"][$prop["name"]] = $value["property_value"];
                        }

                        // set selected to true in PropertyOption array
                        if (!empty($prop["PropertyOption"])) {
                            foreach ($prop["PropertyOption"] as $n => $option) {
                                if ($option["property_option"] == $value["property_value"]) {
                                    $property[$keyProp]["PropertyOption"][$n]["selected"] = true;
                                }
                            }
                        }

                        unset($result["ObjectProperty"][$k]);
                    }
                }

            }
            $result["ObjectProperty"] = $property;
            unset($property);
        }

        // set up LangText for view
        if (!empty($result['LangText'])) {
            $langText = array();
            foreach ($result['LangText'] as $lang) {
                if (!empty($lang["name"]) && !empty($lang["lang"])) {
                    $langText[$lang["name"]][$lang["lang"]] = $lang["text"];
                    $langText[$lang["object_id"]][$lang["lang"]][$lang["name"]] = $lang["id"];
                }
            }
            $result['LangText'] = $langText;
        }

        // divide tags from categories
        if (!empty($result["Category"])) {

            $tag = array();
            $category = array();

            foreach ($result["Category"] as $ot) {
                if (!empty($ot["object_type_id"])) {
                    $category[] = $ot;
                } else {
                    $tag[] = $ot;
                }
            }

            $result["Category"] = $category;
            $result["Tag"] = $tag;
        }

        if (!empty($result["Permission"])) {
            foreach ($result["Permission"] as &$perm) {
                if ($perm["switch"] == "group") {
                    $perm["name"] = $this->Permission->Group->field("name", array("id" => $perm["ugid"]));
                } elseif ($perm["switch"] == "user") {
                    $perm["name"] = $this->Permission->User->field("name", array("id" => $perm["ugid"]));
                }
            }
        }

        return $result ;
    }

    function beforeSave() {
        $data;
        if(isset($this->data[$this->name]))
            $data = &$this->data[$this->name] ;
        else
            $data = &$this->data ;

        // format custom properties and searchable text fields
        $labels = array('SearchText');
        foreach ($labels as $label) {
            if(!isset($data[$label]))
                continue;

            if(is_array($data[$label]) && count($data[$label])) {
                $tmps = array();
                foreach($data[$label]  as $k => $v) {
                    $this->_value2array($k, $v, $arr);
                    array_push($tmps, $arr);
                }
                $data[$label] = $tmps;
            }
        }

        // empty GeoTag array if no value is in
        if (!empty($data['GeoTag'])) {
            foreach ($data['GeoTag'] as $key => $geotag) {
                $concat = '';
                $geoTagFields = array('title', 'address', 'latitude', 'longitude');
                foreach ($geoTagFields as $field) {
                    if (isset($geotag[$field])) {
                        $concat .= trim($geotag[$field]);
                    }
                }
                if (strlen($concat) == 0) {
                    unset($data['GeoTag'][$key]);
                }
            }
        }

        $this->unbindModel(array("hasMany"=>array("LangText","Version")));
        $this->unbindModel(array("hasAndBelongsToMany"=>array("User")));

        return true;
    }

    /**
     * Save hasMany relations data
     */
    function afterSave() {

        // hasMany relations
        foreach ($this->hasMany as $name => $assoc) {
            // skip specific manage
            if ($name == 'Permission' || $name == 'RelatedObject' || $name == 'Annotation') {
                continue;
            }

            // if not set data array do nothing
            if (!isset($this->data[$this->name][$name])) {
                continue;
            }

            $db =& ConnectionManager::getDataSource($this->useDbConfig);
            $model = new $assoc['className']();

            // delete previous associations
            $id = (isset($this->data[$this->name]['id']))? $this->data[$this->name]['id'] : $this->getInsertID();
            $foreignK = $assoc['foreignKey'];
            $model->deleteAll(array($foreignK => $id));

            if (!(is_array($this->data[$this->name][$name]) && count($this->data[$this->name][$name]))) {
                continue;
            }

            // save associations
            $size = count($this->data[$this->name][$name]);
            for ($i = 0; $i < $size; $i++) {
                $modelTmp = new $assoc['className']();
                $data = &$this->data[$this->name][$name][$i];
                $data[$foreignK] = $id;
                if (!$modelTmp->save($data)) {
                    throw new BeditaException(__("Error saving object", true), "Error saving hasMany relation in BEObject for model " . $assoc['className']);
                }

                unset($modelTmp);
            }

        }

        // build ObjectUser Relation
        if (isset($this->data['BEObject']["ObjectUser"])) {
            $objectUserModel = ClassRegistry::init("ObjectUser");
            if (empty($this->data['BEObject']["ObjectUser"])) {
                $objectUserModel->deleteAll(array(
                        "object_id" => $this->id
                    )
                );
            } else {
                foreach ($this->data['BEObject']["ObjectUser"] as $switch => $objUserArr) {
                    $objectUserModel->deleteAll(array(
                            "object_id" => $this->id,
                            "switch" => $switch
                        )
                    );
                    if (is_array($objUserArr)) {
                        foreach ($objUserArr as $objUserData) {
                            if (!empty($objUserData["user_id"])) {
                                if (empty($objUserData["object_id"])) {
                                    $objUserData["object_id"] = $this->id;
                                }
                                $objectUserModel->create();
                                if (!$objectUserModel->save($objUserData)) {
                                    throw new BeditaException(__("error saving object_users relations",true));
                                }
                            }
                        }
                    }
                }
            }
        }

        $permissions = false;
        if (isset($this->data["Permission"])) {
            $permissions = $this->data["Permission"] ;
        } elseif (isset($this->data[$this->name]["Permission"])) {
            $permissions = $this->data[$this->name]["Permission"];
        }

        if (is_array($permissions)) {
            $this->Permission->replace($this->{$this->primaryKey}, $permissions);
        }
        // save relations between objects
        if (!empty($this->data['BEObject']['RelatedObject'])) {
            $queriesInsert = array();
            $switches = array();
            $inverseSwitches = array();
            $lang = (isset($this->data['BEObject']['lang']))? $this->data['BEObject']['lang'] : null;

            $allRelations = BeLib::getObject("BeConfigure")->mergeAllRelations();
            $inverseRelations = array();
            foreach ($allRelations as $n => $r) {
                if (!empty($r["inverse"])) {
                    $inverseRelations[$r["inverse"]] = $n;
                }
            }

            $assoc = $this->hasMany['RelatedObject'] ;
            $ObjectRelation = ClassRegistry::init('ObjectRelation');

            foreach ($this->data['BEObject']['RelatedObject'] as $switch => $values) {

                foreach ($values as $key => $val) {
                    $obj_id	= isset($val['id'])? $val['id'] : false;
                    $priority = isset($val['priority'])? $val['priority'] : null;
                    $params = isset($val['params'])? json_encode($val['params']) : null;

                    $inverseSwitch = $switch;
                    if (!empty($allRelations[$switch]) && !empty($allRelations[$switch]["inverse"])) {
                        $inverseSwitch = $allRelations[$switch]["inverse"];
                    } elseif (!empty($inverseRelations[$switch])) {
                        $inverseSwitch = $inverseRelations[$switch];
                    }

                    // Add switches to list of switches to be cleaned up for current object.
                    $switches[] = $switch;
                    $inverseSwitches[] = $inverseSwitch;

                    if (!empty($obj_id)) {
                        $queriesInsert[] = array(
                            'id' => $this->id,
                            'object_id' => $obj_id,
                            'switch' => $switch,
                            'priority' => $priority,
                            'params' => $params,
                        );

                        // find priority of inverse relation
                        $inverseRel = $ObjectRelation->find('first', array(
                            'fields' => array('priority'),
                            'conditions' => array(
                                'object_id' => $this->id,
                                'switch' => $inverseSwitch,
                            ),
                        ));

                        if (empty($inverseRel["ObjectRelation"]["priority"])) {
                            $inverseRel = $ObjectRelation->find('first', array(
                                'fields' => array('MAX(priority) + 1 AS priority'),
                                'conditions' => array(
                                    'id' => $obj_id,
                                    'switch' => $inverseSwitch,
                                ),
                            ));
                            $inversePriority = (empty($inverseRel[0]["priority"]))? 1 : $inverseRel[0]["priority"];
                        } else {
                            $inversePriority = $inverseRel["ObjectRelation"]["priority"];
                        }
                        $queriesInsert[] = array(
                            'id' => $obj_id,
                            'object_id' => $this->id,
                            'switch' => $inverseSwitch,
                            'priority' => $inversePriority,
                            'params' => $params,
                        );
                    }

                    $modified = (isset($val['modified']))? ((boolean)$val['modified']) : false;
                    if ($modified && $obj_id) {
                        // Save tmp data.
                        $tmp_id = $this->id;
                        $tmp_data = $this->data;

                        // Update related models.
                        $this->data = array();
                        $title = isset($val['title']) ? $val['title'] : null;
                        $description = isset($val['description']) ? $val['description'] : null;
                        if ($switch == 'link') {
                            ClassRegistry::init('Link')->save(array(
                                'id' => $obj_id,
                                'title' => $title,
                                'url' => $val['url'],
                            ));
                        } else {
                            $this->updateTitleDescription($obj_id, $title, $description);
                        }

                        // Restore tmp data.
                        $this->id = $tmp_id;
                        $this->data = $tmp_data;
                    }
                }
            }

            // Save tmp data.
            $tmp_id = $this->id;
            $tmp_data = $this->data;

            // Delete old relations.
            $ObjectRelation->deleteAll(array(
                $assoc['foreignKey'] => $this->id,
                'switch' => $switches,
            ));
            $ObjectRelation->deleteAll(array(
                $assoc['associationForeignKey'] => $this->id,
                'switch' => $inverseSwitches,
            ));

            // Insert updated relations data.
            if ($ObjectRelation->saveAll($queriesInsert) === false) {
                throw new BeditaException(__("Error inserting associations", true), $queriesInsert);
            }

            // Restore tmp data.
            $this->id = $tmp_id;
            $this->data = $tmp_data;
        }

        return true;
    }

    /**
     * Define default values.
     */
    function beforeValidate() {
        if(isset($this->data[$this->name]))
            $data = &$this->data[$this->name] ;
        else
            $data = &$this->data ;

        if(isset($data['title'])) {
            $data['title'] = trim($data['title']);
        }

        if (isset($data['fixed']) && !$this->_isCurrentUserAdmin()) {
            // #590 - Prevent non-admin Users to be able to change fixed property.
            unset($data['fixed']);
        }

        // set language -- disable for comments?
        if(!isset($data['lang'])) {
            $data['lang'] = $this->_getDefaultLang();
        }
        // check/set IP
        if(!isset($data['ip_created']) && !isset($data['id'])) {
            $data['ip_created'] = $this->_getDefaultIP();
        }

        // #650 set always user_modified = current user, set user_created only on new objects.
        // Because of cakephp populate fields with default value on db
        // user_created and user_modified are always set to 1 (systemUserId) if no value was set in $this->data array so
        // 1. user_created will be populated if no 'id' is set and is empty $data['user_created'] or it's equal to $systemUserId.
        //    In that case it will try to use session user
        //    If 'id' isset then it unset $data['user_created'] if exists.
        // 2. user_modified will be populated if is empty $data['user_modified'] or it's equal to $systemUserId.
        //    In that case it will try to use session user
        $systemUserId = BeLib::getObject('BeSystem')->systemUserId();
        if (empty($data['user_modified']) || $data['user_modified'] == $systemUserId) {
            $data['user_modified'] = $this->_getIDCurrentUser();
        }
        if (!isset($data['id'])) {
            if (empty($data['user_created']) || $data['user_created'] == $systemUserId) {
                $data['user_created'] = $this->_getIDCurrentUser();
            }
        } elseif (isset($data['user_created'])) {
            unset($data['user_created']);
        }

        $this->setPublisher();

        // nickname: verify nick and status change, object not fixed
        if(isset($data['id'])) {
            $currObj = $this->find("first", array(
                                            "conditions"=>array("BEObject.id" => $data['id']),
                                            "fields" =>array("status", "nickname", "fixed"),
                                            "contain" => array()
                                            ));
            if($currObj['BEObject']['fixed'] == 1) {  // don't change nickname & status
                // throws exceptions if status/nicknames are changed
                if((!empty($data['status']) && $data['status'] != $currObj['BEObject']['status']) ||
                    (!empty($data['nickname']) && $data['nickname'] != $currObj['BEObject']['nickname'])) {
                    throw new BeditaException(__("Error: modifying fixed object!", true));
                }
                $data['nickname'] = $currObj['BEObject']['nickname'];
                $data['status'] = $currObj['BEObject']['status'];
            } else {
                // Check if nickname has changed.
                if (empty($data['nickname']) && !empty($currObj['BEObject']['nickname'])) {
                    $data['nickname'] = $currObj['BEObject']['nickname'];
                } else {
                    $data['nickname'] = $this->_getDefaultNickname($data['nickname']);
                }

                // Check if status has changed.
                if (empty($data['status']) && !empty($currObj['BEObject']['status'])) {
                    $data['status'] = $currObj['BEObject']['status'];
                }
            }
        } else {
            $title = isset($data['title']) ? $data['title'] : null;
            $tmpName = !empty($data['nickname']) ? $data['nickname'] : $title;
            $data['nickname'] = $this->_getDefaultNickname($tmpName);
        }

        if(empty($data["user_created"])) unset($data["user_created"]) ;

        // format custom properties data type
        if (!empty($data["ObjectProperty"])) {
            foreach ($data["ObjectProperty"] as $key => $val) {
                if (!empty($val["property_type"]) && $val["property_type"] == "date")
                    $data["ObjectProperty"][$key]["property_value"] = $this->getDefaultDateFormat($val["property_value"]);
            }
        }

        // Se c'e' la chiave primaria vuota la toglie
        if(isset($data[$this->primaryKey]) && empty($data[$this->primaryKey]))
            unset($data[$this->primaryKey]) ;

        return true ;
    }

    public function findObjectTypeId($id) {
        $object_type_id = $this->field("object_type_id", array("BEObject.id" => $id));
        return $object_type_id;
    }

    /**
     * Is object fixed??
     *
     * @param int $id
     * @return boolean
     */
    public function isFixed($id) {
        $fixed = $this->field("fixed", array("BEObject.id" => $id));
        return ($fixed == 1);
    }

    /**
     * Model name/type from id
     *
     * @param unknown_type $id
     */
    public function getType($id) {
        $type_id = $this->findObjectTypeId($id);
        if($type_id === false) {
            throw new BeditaException(__("Error: object type not found", true));
        }
        return Configure::getInstance()->objectTypes[$type_id]["model"] ;
    }

    /**
     * Update title and description only.
     *
     * @param int $id
     * @param string|null $title
     * @param string|null $description
     * @return bool
     **/
    public function updateTitleDescription($id, $title, $description) {
        if (empty($id)) {
            return false;
        }

        $model = Configure::read('objectTypes.' . $this->findObjectTypeId($id) . '.model');
        $reg = ClassRegistry::getInstance();
        $reg->removeObject($model);  // #292 - Avoid loop in some cases, if related object is of the same model as the parent.

        // #722 - Avoid emptying title or description if field not submitted.
        $data = array(
            'id' => $id,
            'title' => $title,
            'description' => $description,
        );
        return $reg->init($model)->save(array_filter($data, function ($val) {
            return !is_null($val);
        }));
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * build object unique name
     *
     * @param string $value
     * @return string
     */
    private function _getDefaultNickname($value) {
        $nickname = $nickname_base = BeLib::getInstance()->friendlyUrlString($value);
        $conf = Configure::getInstance() ;
        $nickOk = false;
        $countNick = 1;
        $reservedWords = array_merge ( $conf->defaultReservedWords, $conf->cfgReservedWords );
        if(empty($nickname)) {
            $objTypeId = $this->data['BEObject']['object_type_id'];
            $nickname_base = $conf->objectTypes[$objTypeId]["name"] . "-" . time(); // default name - model type name - timestamp
            $nickname = $nickname_base ;
        };


        $aliasModel = ClassRegistry::init("Alias");
        while (!$nickOk) {

            $cond = "WHERE BEObject.nickname = '{$nickname}'";
            if ($this->id) {
                $cond .= " AND BEObject.id<>".$this->id;
            }
            $numNickDb = $this->find("count", array("conditions" => $cond, "contain" => array()));

            // check nickname in db and in reservedWords
            if ($numNickDb == 0 && !in_array($nickname, $reservedWords)) {
                // check aliases
                $object_id = $aliasModel->field("object_id", array("nickname_alias" => $nickname));
                if(empty($object_id)) {
                    $nickOk = true;
                }
            }
            if(!$nickOk) {
                $nickname = $nickname_base . "-" . $countNick++;
            }
        }

        return $nickname ;
    }

    private function _getDefaultLang() {
        $conf = Configure::getInstance() ;
        return ((isset($conf->defaultLang))?$conf->defaultLang:'') ;
    }

//	private function _getDefaultPermission($value, $object_type_id) {
//		if(isset($value) && is_array($value)) return $value ;
//
//		$conf = Configure::getInstance() ;
//		$permissions = &$conf->permissions ;
//
//		// Aggiunge i permessi di default solo se sta creando un nuovo oggetto
//		if(isset($this->data[$this->name][$this->primaryKey])) return null ;
//
//		// Seleziona i permessi in base al tipo di oggetti
//		if(isset($permissions[$object_type_id])) 	return $permissions[$object_type_id] ;
//		else if (isset($permissions['all']))		return $permissions['all'] ;
//
//		return null ;
//	}

    private function _getDefaultIP() {
        if(!empty($_SERVER['REMOTE_ADDR'])) {
            $IP = $_SERVER['REMOTE_ADDR'];
        } else {
            $IP = $this->defaultIp;
        }
        return $IP ;
    }

    /**
     * Returns the current user ID. If a unit test is running, the test user ID is returned instead.
     *
     * @return int Current User's ID, or test User's ID. Defaults to system User's ID.
     * @see BeSystem::systemUserId()
     */
    private function _getIDCurrentUser() {
        $conf = Configure::getInstance();
        $systemUserId = BeLib::getObject('BeSystem')->systemUserId();

        if (isset($conf->beditaTestUserId)) {
            // Unit tests.
            return $conf->beditaTestUserId;
        } elseif (class_exists('CakeSession')) {
            $session = new CakeSession();
            if (!$session->started() || $session->valid() === false) {
                return $systemUserId;
            }

            $user = $session->read($conf->session['sessionUserKey']);
            if (!isset($user['id'])) {
                return $systemUserId;
            }

            return $user['id'];
        }

        return $systemUserId;
    }

    /**
     * Checks whether current User is in Group `administrator` or not.
     *
     * @return bool Current User's administrator permissions.
     */
    private function _isCurrentUserAdmin() {
        return !is_null(Configure::read('beditaTestUserId')) || in_array('administrator', ClassRegistry::init('User')->getGroups($this->_getIDCurrentUser()));
    }

    /**
     * Set the publisher to use in object creation.
     *
     * In editing mode (id not empty) no takes action.
     * In frontend no takes action.
     * In backend set `editorialContents.defaultPublisher` if configured and publisher in data is empty
     *
     * @return void
     */
    private function setPublisher() {
        if (isset($this->data[$this->name])) {
            $data = &$this->data[$this->name];
        } else {
            $data = &$this->data;
        }

        if (!empty($data['id']) || !BACKEND_APP) {
            return;
        }

        $defaultPublisher = Configure::read('editorialContents.defaultPublisher');

        if (!empty($defaultPublisher) && empty($data['publisher'])) {
            $data['publisher'] = $defaultPublisher;
        }
    }

    /**
     * torna un array con la variabile archiviata in un array
     */
    private function _value2array($name, &$val, &$arr) {
        $type = null ;
        switch(gettype($val)) {
            case "integer" : 	{ $type = "integer" ; } break ;
            case "boolean" : 	{ $type = "bool" ; } break ;
            case "double" : 	{ $type = "float" ; } break ;
            case "string" :		{ $type = "string" ; } break ;

            default: {
                $type = "stream" ;
                $val = serialize($val) ;
             }
        }
        $arr = array(
            'name'		=> $name,
            'type'		=> $type,
            $type		=> $val
        ) ;
    }

    /**
     * Get object id from an identifier that could be an id or nickname
     * @param mixed $val
     */
    public function objectId($val) {
        $res = 0;
        if(is_numeric($val)) {
            $res = $val;
        } else {
            $res = $this->getIdFromNickname(strtolower($val));
        }
        return $res;
    }

    /**
     * Get object id from unique name
     * @param string $nickname
     */
    function getIdFromNickname($nickname, $status = null) {
        $id = null;
        if($status != null) {
            $id = $this->field("id", array("nickname" => $nickname, "status" => $status));
        } else {
            $id = $this->field("id", array("nickname" => $nickname));
        }
        if(empty($id)) { // if nickname not found lookup aliases
            $aliasModel = ClassRegistry::init("Alias");
            $id = $aliasModel->field("object_id", array("nickname_alias" => $nickname));
        }
        return $id;
    }

    /**
     * Get object nickname from id
     * @param integer $id
     */
    function getNicknameFromId($id) {
        return $this->field("nickname", array("id" => $id));
    }

    /**
     * Get an image id and uri that can be used as a poster of the one represented by the $id
     * Search in the relations expressed by $relations
     * @param int|string $id
     * @param array
     * @return array
     */
    public function getPoster($id = null, $relations = array('attach')) {
        if (empty($id) && !empty($this->id)) {
            $id = $this->id;
        }
        if (empty($id)) {
            return false;
        }
        if (!is_numeric($id)) {
            $id = $this->getIdFromNickname($id);
        }

        $Stream = ClassRegistry::init('Stream');

        /** Use `poster` relation, if present. */
        $poster = $Stream->find('first', array(
            'contain' => array(),
            'fields' => array(
                'RelatedObject.object_id',
                'Stream.uri',
            ),
            'joins' => array(
                array(
                    'table' => 'object_relations',
                    'alias' => 'RelatedObject',
                    'type' => 'INNER',
                    'conditions' => array(
                        'RelatedObject.object_id = Stream.id',
                        'RelatedObject.switch' => 'poster',
                        'RelatedObject.id' => $id,
                    ),
                ),
            ),
            'conditions' => array(
                'Stream.mime_type LIKE' => 'image%',
            ),
            'order' => array('RelatedObject.priority ASC'),
        ));
        if (!empty($poster)) {
            return array(
                'id' => $poster['RelatedObject']['object_id'],
                'uri' => $poster['Stream']['uri'],
            );
        }

        /** Use current object, if it is an image. */
        if ($this->findObjectTypeId($id) == Configure::read('objectTypes.image.id')) {
            $uri = $Stream->field('uri', array('Stream.id' => $id));
            return compact('id', 'uri');
        }

        /** Use attachments and other configured relations, as a last resort. */
        $related = $Stream->find('first', array(
            'contain' => array(),
            'fields' => array(
                'RelatedObject.object_id',
                'Stream.uri',
            ),
            'joins' => array(
                array(
                    'table' => 'object_relations',
                    'alias' => 'RelatedObject',
                    'type' => 'INNER',
                    'conditions' => array(
                        'RelatedObject.object_id = Stream.id',
                        'RelatedObject.switch' => $relations,
                        'RelatedObject.id' => $id,
                    ),
                ),
            ),
            'conditions' => array(
                'Stream.mime_type LIKE' => 'image%',
            ),
            'order' => array('RelatedObject.priority ASC'),
        ));
        if (!empty($related)) {
            return array(
                'id' => $related['RelatedObject']['object_id'],
                'uri' => $related['Stream']['uri'],
            );
        }

        return false;
    }
}

<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2014 ChannelWeb Srl, Chialab Srl
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

class DataTransfer extends BEAppModel
{
    public $useTable = false;

    protected $objDefaults = array(
        'status' => 'on',
        'user_created' => '1',
        'user_modified' => '1',
        'lang' => 'ita',
        'ip_created' => '127.0.0.1',
        'syndicate' => 'off',
    );

    protected $objMinimalSet = array(
        'id',
        'objectType'
    );

    protected $relMinimalSet = array(
        'idLeft',
        'idRight',
        'switch'
    );

    // utility array
    protected $import = array(
        'source' => array(
            'string' => null, // (is_string(data)) ? data : null
            'data' => array() // json_decode of data
        ),
        'objects' => array(
            'ids' => array(),
            'types' => array()
        ),
        'tree' => array(
            'ids' => array(),
            'parents' => array()
        ),
        'relations' => array(
            'ids' => array(),
            'switches' => array()
        ),
        'saveMap' => array(
            // oldId => newId
        ),
        'saveMode' => 1, // NEW
        'logLevel' => 2 // INFO
    );

    protected $export = array(
        'destination' => array(
            'byType' => array(
                'ARRAY' => array(
                    'config' => array(),
                    'tree' => array(),
                    'objects' => array(),
                    'relations' => array()
                ),
                'JSON' => '' // string
            )
        ),
        'returnType' => 0, // JSON
        'logLevel' => 2, // INFO
        'objectUnsetFields' => array(
            'user_created',
            'user_modified',
            'valid',
            'ip_created',
            'UserCreated',
            'UserModified',
            'User'
        ),
        'contain' => array(
            'BEObject' => array(
                'RelatedObject',
                'ObjectProperty',
                'LangText',
                'Annotation',
                'Category',
                'GeoTag'
            )
        ),
        'media' => array(),
        'customProperties' => array()
    );

    protected $result = array(
    );

    private $logFile;
    private $logLevel;

    protected $logLevels = array(
        'ERROR' => 0,
        'WARN' => 1,
        'INFO' => 2,
        'DEBUG' => 3
    );

    protected $saveModes = array(
        'MERGE' => 0, // merge relations (always for imported objects)
        'NEW' => 1, // create new object with new nickname
        'OVERRIDE' => 2, // remove object with same nickname
        'IGNORE' => 3, // ignore object
        'UPDATE' => 4 // merge relations and update data
    );

    protected $customPropertyDataTypes = array(
        'number',
        'date',
        'text',
        'options'
    );

    /**
     * Import data (json string or array) to BEdita
     *
     * @param $data string|array
     * @param $options array
     * @return $result array
     *
     * @link https://github.com/bedita/bedita/wiki/Default-serialized-format-for-BEdita-objects
     *
     * $options = array(
     *    'logDebug' => true, // can be true|false
     *    'saveMode' => 0, // can be 0 (MERGE), 1 (NEW), 2 (OVERRIDE), 3 (IGNORE), 4 (UPDATE)
     *    'sourceMediaRoot' => '/media/source/path', // default /TMP/media-import
     * )
     * 
     * 1. Validating input data
     * 2. Importing data to BEdita
     * 3. Return result object
     */
    
    public function import(&$data, $options = array()) {
        $this->logFile = 'import';
        // setting logLevel - default INFO
        $this->logLevel = (!empty($options['logLevel'])) ? $options['logLevel'] : $this->logLevels['INFO'];
        // setting save mode - default NEW
        $this->import['saveMode'] = (!empty($options['saveMode'])) ? $options['saveMode'] : $this->saveModes['NEW'];
        // setting sourceMediaRoot - default TMP/media-import
        $this->import['sourceMediaRoot'] = (!empty($options['sourceMediaRoot'])) ? $options['sourceMediaRoot'] : 'TMP' . DS . 'media-import';
        $this->trackInfo('START');
        try {
            // 1. Validate
            $this->trackInfo('1 validate start');
            $this->validate($data, $options);
            $this->trackInfo('1 validate OK');
            // 2. Importing
            $this->trackInfo('2 import start');
            // 2.1 import config
            $this->trackInfo('2.1 import config');
            if (!empty($this->import['source']['data']['config'])) {
                // 2.1.1 import custom properties
                $this->trackInfo('2.1.1 import custom properties');
                if (!empty($this->import['source']['data']['config']['customProperties'])) {
                    foreach ($this->import['source']['data']['config']['customProperties'] as $customProperty) {
                        $this->trackDebug('2.1.1 import custom properties - save property with policy \'NEW\'');
                        $this->saveProperty($customProperty);
                    }
                }
            }
            // 2.1.? [...] [TODO]
            //$this->trackInfo('2.1.? [...] [TODO]');
            // 2.2 save areas/sections
            $this->trackInfo('2.2 save areas/sections');
            // 2.2.1 save roots (areas/sections)
            $this->trackInfo('2.2.1 save roots (areas/sections)');
            $rootIds = $this->import['tree']['roots'];
            foreach ($rootIds as $rootId) {
                $rootData = $this->import['source']['data']['objects'][$rootId];
                $rootObjType = $this->import['source']['data']['objects'][$rootId]['objectType'];
                // 2.2.1.1 save area(s) with policy 'NEW'
                $this->trackDebug('2.2.1.1 save area/section(s) with policy (old id ' . $rootId . ') \'NEW\'');
                // 2.2.1.2 save area(s) with other policies [TODO]
                $this->trackDebug('2.2.1.2 save area/section(s) with other policies (old id ' . $rootId . ') [TODO]');
                if ($rootObjType == 'area') {
                    $this->saveArea($rootData);
                } else if ($rootObjType == 'section') {
                    $parentId = $options['section_root_id'];
                    $this->saveSection($rootData, $parentId);
                }
            }
            // 2.2.2 save other section(s)
            $this->trackDebug('2.2.2 save other section(s)');
            foreach ($this->import['source']['data']['tree']['sections'] as $section) {
                $newParentId = $this->import['saveMap'][$section['parent']];
                // 2.2.2.1 save section(s) with policy 'NEW'
                $this->trackDebug('2.2.2.1 save section(s) (old section id ' . $section['id'] . ' | old parent_id ' . $section['parent'] . ' | new parent id ' . $newParentId . ') with policy \'NEW\'');
                // 2.2.2.2 save section(s) with other policies [TODO]
                $this->trackDebug('2.2.2.2 save section(s) (old section id ' . $section['id'] . ' | old parent_id ' . $section['parent'] . ' | new parent id ' . $newParentId . ') with other policies [TODO]');
                $this->saveSection($section, $newParentId);
            }
            $this->trackInfo('2.3 save objects');
            if (!empty($this->import['media'])) {
                $this->trackInfo('2.3.1 copy media');
                $streamModel = ClassRegistry::init('Stream');
                foreach ($this->import['media'] as $id => &$media) {
                    try {
                        $beUri = $streamModel->copyFileToMediaFolder($media['full'], $this->import['destination']['media']['root']);
                        $beFull = $this->import['destination']['media']['root'] . $beUri;
                        $this->import['source']['data']['objects'][$id]['uri'] = $beUri;
                    } catch(Exception $e) {
                        $this->trackError($e->getMessage());
                        //$this->trackWarn($e->getMessage());
                    }
                }
            }
            foreach ($this->import['source']['data']['objects'] as &$object) {
                $this->saveObject($object);
            }
            // 2.4 save relations
            if (!empty($this->import['source']['data']['relations'])) {
                $this->trackInfo('2.4 save relations');
                // cycle over json["relations"] items: creating bedita relations
                $relationKeys = array_keys($this->import['source']['data']['relations']);
                $relationStructureOne = is_int($relationKeys[0]);
                $counter = 1;
                if ($relationStructureOne) {
                    foreach ($this->import['source']['data']['relations'] as &$relation) {
                        $this->saveRelation($relation, $counter++);
                    }
                } else {
                    foreach ($this->import['source']['data']['relations'] as $switch => &$relations) {
                        foreach ($relations as &$relation) {
                            $this->saveRelation($relation, $counter++);
                        }
                    }
                }
            }
            // 2.? [...] [TODO]
            //$this->trackInfo('2.? [...] [TODO]');
            $this->trackInfo('2 import OK');
        } catch(Exception $e) {
            $this->trackError('ERROR: ' . $e->getMessage());
        }
        // 3. result
        $this->trackInfo('3 result');
        // 3.1 format / process result (?) [TODO]
        //$this->trackDebug('3.1 format / process result (?) [TODO]');
        // 3.2 return result
        $this->trackDebug('3.2 return result');
        $this->trackInfo('END');
        return $this->result;
    }

    /**
     * Export BEdita objects data to JSON or other format
     * 
     * @param  array &$objects  ids of root elements (publication|section) or ids of objects (document|event|...)
     * @param  array $options   export parameters
     * @return mixed object     json|array|other (file?)
     * 
     * $options = array(
     *    'logDebug' => true, // can be true|false
     *    'destMediaRoot' => '/media/dest/path', // default /TMP/media-export
     *    'returnType' => 'JSON' // default 'JSON' - can be 'ARRAY'
     * )
     */
    public function export(array &$objects, $options = array()) {
        $this->logFile = 'export';
        // setting logLevel - default INFO
        $this->logLevel = (!empty($options['logLevel'])) ? $options['logLevel'] : $this->logLevels['INFO'];
        // return type - default JSON
        $this->export['returnType'] = (!empty($options['returnType'])) ? $options['returnType'] : 'JSON';
        $this->export['filename'] = (!empty($options['filename'])) ? $options['filename'] : NULL;
        $this->export['destMediaRoot'] = (!empty($options['destMediaRoot'])) ? $options['destMediaRoot'] : "";
        $this->trackInfo('START');
        try {
            $this->trackDebug('1 area/section/other objects data');
            // $objects contain ids. they can be areas/sections or objects (document, etc.)
            // if objects are areas/sections => roots, otherwise roots is empty
            $treeModels = array('Area', 'Section');
            $extractTreeData = true;
            $conf = Configure::getInstance();
            foreach ($objects as $objectId) {
                $objectTypeId = ClassRegistry::init('BEObject')->findObjectTypeId($objectId);
                if (isset($conf->objectTypes[$objectTypeId])) {
                    $model = $conf->objectTypes[$objectTypeId]['model'];
                } else if (isset($conf->objectTypesExt[$objectTypeId])) {
                    $model = $conf->objectTypesExt[$objectTypeId]['model'];
                } else {
                    throw new BeditaException('Model not found per objecttypeId "' . $objectTypeId . '"');
                }

                $objModel = ClassRegistry::init($model);
                $objModel->contain(
                    $this->export['contain']
                );
                $obj = $objModel->findById($objectId);
                $this->prepareObjectForExport($obj);
                if (!in_array($model, $treeModels)) {
                    $extractTreeData = false;
                }
            }
            $this->trackDebug('2 tree:');
            if ($extractTreeData) {
                $this->trackDebug('2.1 roots:');
                $this->export['destination']['byType']['ARRAY']['tree']['roots'] = $objects;

                $this->trackDebug('2.2 sections:');
                foreach ($objects as $parent) {
                    $filter = array(
                        'object_type_id' => $conf->objectTypes['section']['id']
                    );
                    $sections = $this->findObjects($parent, null, 'on', $filter, null, true, 1, null, true, array());
                    if (!empty($sections['items'])) {
                        foreach ($sections['items'] as $section) {
                            $this->export['destination']['byType']['ARRAY']['tree']['sections'][] = array(
                                'id' => $section['id'],
                                'parent' => $section['parent_id']
                            );
                            $objModel = ClassRegistry::init('Section');
                            $objModel->contain(
                                $this->export['contain']
                            );
                            $obj = $objModel->findById($section['id']);
                            $this->prepareObjectForExport($obj);
                       }
                    }
                }
            } else {
                $this->trackDebug('... skip roots and sections');
            }
            $this->trackDebug('3 objects + 4 relations');
            if (!empty($this->export['destination']['byType']['ARRAY']['tree']['roots'])) {
                $this->prepareObjectsForExportByParents($this->export['destination']['byType']['ARRAY']['tree']['roots']);
            }
            if (!empty($this->export['destination']['byType']['ARRAY']['tree']['sections'])) {
                $parents = Set::extract('/id',$this->export['destination']['byType']['ARRAY']['tree']['sections']);
                $this->prepareObjectsForExportByParents($parents);
            }
            // set position for objects
            $treeTypes = array('area', 'section');
            foreach ($this->export['destination']['byType']['ARRAY']['objects'] as &$object) {
                if (!in_array($object['objectType'], $treeTypes) && !empty($this->export['destination']['byType']['ARRAY']['tree']['roots'])) {
                    $object['parents'] = $this->parentsForObjId($object['id'], $this->export['destination']['byType']['ARRAY']['tree']['roots']);
                }
            }
            $this->trackDebug('4 config');
            $this->trackDebug('4.1 config.customProperties:');
            if (!empty($this->export['customProperties'])) {
                foreach ($this->export['customProperties'] as $property) {
                    $propertyNew = array();
                    $propertyNew['id'] = $property['id'];
                    $propertyNew['name'] = $property['name'];
                    $propertyNew['objectType'] = Configure::read('objectTypes.' . $property['object_type_id'] . '.name');
                    $propertyNew['dataType'] = $property['property_type'];
                    if (!empty($property['multiple_choice'])) {
                        $propertyNew['multipleChoice'] = $property['multiple_choice'];
                    }
                    if (!empty($property['PropertyOption'])) {
                        $propertyNew['options'] = array();
                        foreach ($property['PropertyOption'] as $propertyOption) {
                            $propertyNew['options'][] = $propertyOption['property_option'];
                        }
                    }
                    $propertiesNew[] = $propertyNew;
                }
                $this->export['destination']['byType']['ARRAY']['config']['customProperties'] = $propertiesNew;
            }
            $this->trackDebug('5. media');
            if (!empty($this->export['media'])) {
                $this->export['srcMediaRoot'] = Configure::read('mediaRoot');
                if (!file_exists($this->export['srcMediaRoot'])) {
                    throw new BeditaException('srcMediaRoot folder "' . $this->export['srcMediaRoot'] . '" not found');
                }
                if (!file_exists($this->export['destMediaRoot'])) {
                    throw new BeditaException('destMediaRoot folder "' . $this->export['destMediaRoot'] . '" not found');
                }
                foreach ($this->export['media'] as $objectId => $uri) {
                    $this->copyFileToFolder($this->export['srcMediaRoot'], $this->export['destMediaRoot'], $uri);                    
                    $this->trackDebug('... saving ' . $this->export['destMediaRoot'] . $uri);
                }
            }
            if ($this->export['returnType'] === 'JSON') {
                if (phpversion() >= '5.4') {
                    $this->export['destination']['byType']['JSON'] = json_encode($this->export['destination']['byType']['ARRAY'], JSON_PRETTY_PRINT);
                } else {
                    $this->export['destination']['byType']['JSON'] = json_encode($this->export['destination']['byType']['ARRAY']);
                }
                if (!empty($this->export['filename'])) {
                    if (!file_put_contents($this->export['filename'], $this->export['destination']['byType']['JSON'])) {
                        throw new BeditaException('error saving data to file "' . $this->export['filename'] . '"');
                    }
                }
            }
            $this->trackInfo('export OK');
        } catch(Exception $e) {
            $this->trackError('ERROR: ' . $e->getMessage());
        }
        $this->trackInfo('END');
        return $this->export['destination']['byType'][$this->export['returnType']];
    }

    /**
     * Validation of data and related objects and semantics
     * 
     * 1 if data is a string: check json
     * 1.1 if data is a string: not empty
     * 1.2 if data is a string: valid (json_decode / json_last_error)
     *
     * 2 config
     * 2.1 custom properties
     * 2.1.1 fields not empty: name, objectType, dataType
     * 2.1.2 valid objectType
     * 2.1.3 dataType can be 'number', 'date', 'text', 'options'
     * 2.1.4 existence
     * 2.1.5 conflict
     * 2.1.6 objects.customProperty consistence (objects.customProperty.name and objects.customProperty.value not empty / objects.customProperty.name must be declared in config.customProperties)
     * [...]
     *
     * 3 objects
     * 3.1 not empty
     * 3.2 necessary fields (defined in $this->objMinimalSet)
     * 3.3 objectType existence
     * 3.4 specific validation by objectType [TODO]
     * 3.5 categories
     * 3.6 tags [TODO]
     *
     * 4 trees
     * 4.1 not empty
     * 4.2 roots not empty
     * 4.3 valid root ids => if more than one, all must be all of the same type (area or section) / if type is section => options[root_section_id]
     * 4.4 valid parent ids => parents elements must be a subcollection of tree elements
     * 4.5 id referenced in tree must be referenced in objects too
     *
     * 5 relations
     * 5.1 necessary fields (defined in $this->relMinimalSet)
     *       relation structure 1: flat array / i.e. array(<relation item>, <relation item>, ...)
     *       relation structure 2: group by switch / i.e. array(<relation switch> => array(<relation item>, <relation item>, ...), <relation switch> => array(<relation item>, <relation item>, ...), ...)
     * 5.2 id referenced in relations must be referenced in objects too
     * 5.3 switch must be a valid relation name + valid relation objects (existence of objects and right connection)
     * 5.4 objectType(s) must be valid for specified relation switch
     *
     * 6 media
     * 6.1 source folder (sourceMediaRoot)
     * 6.1.1 existence
     * 6.1.2 permits [TODO]
     * 6.2 destination folder
     * 6.2.1 existence
     * 6.2.2 space available
     * 6.3 files
     * 6.3.1 existence (base folder + objects[i].uri) [TODO]
     * 6.3.2 extension allowed [TODO]
     * 6.3.3 dimension allowed [TODO]
     * 6.3.4 all files dimension < space available
     * ...
     * 7 [...] [TODO]
     *
     * @param $data string|array
     * @param $options array
     */
    public function validate(&$data, $options = array()) {
        if (!is_array($data)) {
            // 1 json
            $this->import['source']['string'] = $data;
            // 1.1 not empty
            if (empty($this->import['source']['string'])) {
                throw new BeditaException('empty json string');
            }
            $this->import['source']['string'] = trim($this->import['source']['string']);
            $this->import['source']['data'] = json_decode($this->import['source']['string'], true);
            // 1.2 valid (json_decode / json_last_error)
            if (empty($this->import['source']['data'])) {
                throw new BeditaException('json string not valid: json_last_error error code ' . $this->jsonLastErrorMsg());
            }
        } else {
            $this->import['source']['data'] = $data;
            if (empty($this->import['source']['data'])) {
                throw new BeditaException('empty input data');
            }
        }
        // convert source objects to alternative structure
        $this->import['source']['data']['objects'] = Set::combine($this->import['source']['data'], 'objects.{n}.id', 'objects.{n}');
        $this->import['objects']['ids'] = array_keys($this->import['source']['data']['objects']);
        $this->import['media'] = array();
        $this->import['config'] = array();
        // 2 config
        if (!empty($this->import['source']['data']['config'])) {
            // 2.1 custom properties
            if (!empty($this->import['source']['data']['config']['customProperties'])) {
                $this->import['config']['properties'] = array();
                foreach ($this->import['source']['data']['config']['customProperties'] as $customProperty) {
                    // 2.1.1 fields not empty: name, objectType, dataType
                    if (empty($customProperty['name'])) {
                        throw new BeditaException('config.customProperties: missing name field');
                    }
                    if (empty($customProperty['objectType'])) {
                        throw new BeditaException('config.customProperties: missing objectType field');
                    }
                    if (empty($customProperty['dataType'])) {
                        throw new BeditaException('config.customProperties: missing dataType field');
                    }
                    // 2.1.2 valid objectType
                    $objTypeId = Configure::read('objectTypes.' . $customProperty['objectType'] . '.id');
                    if (empty($objTypeId)) {
                        throw new BeditaException('config.customProperties: objectType ' . $customProperty['objectType'] . ' not found');
                    }
                    // 2.1.3 dataType can be 'number', 'date', 'text', 'options'
                    if (!in_array($customProperty['dataType'],$this->customPropertyDataTypes)) {
                        throw new BeditaException('config.customProperties: dataType ' . $customProperty['dataType'] . ' not allowed | dataType should be number, date, text or options');
                    }
                    // 2.1.4 existence
                    $propertyModel = ClassRegistry::init('Property');
                    $propertyModel->create();
                    $property = $propertyModel->find('first',
                        array(
                            'conditions' => array(
                                'name' => $customProperty['name'],
                                'object_type_id' => $objTypeId
                            )
                        )
                    );
                    if (!empty($property)) {
                        $this->import['config']['properties'][$customProperty['name']] = $property;
                        // 2.1.5 conflict
                        // object_type_id
                        if ($property['object_type_id'] != $objTypeId) {
                            throw new BeditaException('config.customProperties.object_type_id "' . $objTypeId . '"" different from system custom property object_type_id "' . $property['object_type_id'] . '" for property ' . $customProperty['name']);
                        }
                        // property_type
                        if ($property['property_type'] != $customProperty['dataType']) {
                            throw new BeditaException('config.customProperties.dataType "' . $customProperty['dataType'] . '" different from system custom property property_type "' . $property['property_type'] . '" for property ' . $customProperty['name']);
                        }
                        // multiple_choice
                        $multipleChoice = (!empty($property['multiple_choice']) && ($property['multiple_choice'] == true));
                        $multipleChoiceImport = (!empty($customProperty['multipleChoice']) && ($customProperty['multipleChoice'] == true));
                        if ($multipleChoice != $multipleChoiceImport) {
                            throw new BeditaException('config.customProperties.multipleChoice "' . $customProperty['multipleChoice'] . '"" different from system custom property multiple_choice "' . $property['multiple_choice'] . '" for property ' . $customProperty['name']);
                        } else if ($multipleChoice) {
                            // property_options
                            if (empty($customProperty['options'])) {
                                throw new BeditaException('config.customProperties.options: empty options for multiple_choice property for property ' . $customProperty['name']);
                            }
                            $options = $customProperty['options'];
                            if (sizeof($options) != sizeof($property['PropertyOption'])) {
                                throw new BeditaException('config.customProperties.options: options differ from set of PropertyOption in the system property for property ' . $customProperty['name']);
                            }
                            $propertyOptions = array();
                            foreach ($property['PropertyOption'] as $propertyOption) {
                                if (!in_array($propertyOption['property_option'], $options)) {
                                    throw new BeditaException('config.customProperties.options: options does not contain the system property PropertyOption "' . $propertyOption['property_option'] . '"" for property ' . $customProperty['name']);
                                }
                                $propertyOptions[] = $propertyOption['property_option'];
                            }
                            foreach ($options as $option) {
                                if (!in_array($option,$propertyOptions)) {
                                    throw new BeditaException('config.customProperties.options: option "' . $option . '" not found in the system property set of PropertyOption for property ' . $customProperty['name']);
                                }
                            }
                        }
                    }
                    if (!in_array($customProperty['name'], array_keys($this->import['config']['properties']))) {
                        $this->import['config']['properties'][$customProperty['name']] = array();
                    }
                }
            }
        }
        // 2.1.6 objects.customProperty consistence (objects.customProperty must be declared in config.customProperties)
        if ( !empty($this->import['source']['data']) && !empty($this->import['source']['data']['objects'])) {
            foreach ($this->import['source']['data']['objects'] as $object) {
                if (!empty($object['customProperties'])) {
                    if (empty($this->import['config']['properties'])) {
                        throw new BeditaException('object.customProperties defined, but config.customProperties not defined');
                    }
                    foreach ($object['customProperties'] as $customProperty) {
                        // name
                        if (empty($customProperty['name'])) {
                            throw new BeditaException('object.customProperties.name not found for object ' . $object['id']);
                        }
                        if (!in_array($customProperty['name'], array_keys($this->import['config']['properties']))) {
                            throw new BeditaException('object.customProperties.name "' . $customProperty['name'] . '" not defined in config.customProperties');
                        }
                        // value
                        if (!isset($customProperty['value'])) {
                            throw new BeditaException('object.customProperties.value not found for object ' . $object['id']);
                        }
                    }
                }
            }
        }
        // 3 objects consistence validation
        // 3.1 non empty
        if (empty($this->import['source']['data']) || empty($this->import['source']['data']['objects'])) {
            throw new BeditaException('empty objects set');
        }
        // 3.2 necessary fields (defined in $this->objMinimalSet)
        foreach ($this->objMinimalSet as $field) {
            foreach ($this->import['source']['data']['objects'] as $object) {
                if (empty($object[$field])) {
                    $objDesc = (!empty($object['id'])) ? $object['id'] : '';
                    throw new BeditaException('missing field ' . $field . ' for object ' . $objDesc);
                }
            }
        }
        $this->import['expectedParentIds'] = array();
        $treeObjectTypes = array('area', 'section');
        foreach ($this->import['source']['data']['objects'] as $object) {
            if (!empty($object['id']) && !empty($object['objectType']) && in_array($object['objectType'], $treeObjectTypes)) {
                $this->import['expectedParentIds'][] = $object['id'];
            }
            // 3.3 objectType existence
            if (!in_array($object['objectType'], $this->import['objects']['types'])) {
                $ot = Configure::read('objectTypes.' . $object['objectType'] . '.id');
                if (empty($ot)) {
                    throw new BeditaException('missing objectType ' . $object['objectType']);
                }
                $this->import['objects']['types'][] = $object['objectType'];
            }
            $this->import['objects']['typeById'][$object['id']] = $object['objectType'];
            // populate media uri array
            if (!empty($object['id']) && !empty($object['uri'])) {
                $this->import['media'][$object['id']]['uri'] = $object['uri'];
            }
            // 3.4 specific validation by objectType
            // TODO: implement it / idea: use model reflection (i.e. if <modelClass> has method 'validateBeforeImport' then invoke it)
            // 3.5 categories
            if (!empty($object['categories'])) {
                foreach ($object['categories'] as $category) {
                    if (empty($category['name'])) {
                        throw new BeditaException('missing category name for object id "' . $object['id'] . '"');
                    }
                }
            }
        }
        // 4 tree consistency
        // 4.1 tree not empty
        if (empty($this->import['source']['data']['tree'])) {
            throw new BeditaException('missing tree in source json data');
        }
        // 4.2 tree roots not empty
        if (empty($this->import['source']['data']['tree']['roots'])) {
            throw new BeditaException('missing tree roots in source json data');
        }
        $rootIds = $this->import['source']['data']['tree']['roots'];
        $this->import['tree']['roots'] = $rootIds;
        $this->import['tree']['ids'] = $rootIds;
        $this->import['tree']['parents'] = $rootIds;
        // 4.3 valid root ids => if more than one, all must be all of the same type (area or section) / if type is section => options[root_section_id]
        $rootObjTypes = array();
        foreach ($rootIds as $rootId) {
            if (empty($this->import['source']['data']['objects'][$rootId])) {
                throw new BeditaException('root id ' . $rootId . ' not referenced in objects');
            }
            $rootObjType = $this->import['source']['data']['objects'][$rootId]['objectType'];
            if (empty($rootObjType)) {
                throw new BeditaException('missing root object type for root id ' . $rootId);
            }
            if (empty($rootObjTypes)) {
                $rootObjTypes[] = $rootObjType;
            } else if(!in_array($rootObjType, $rootObjTypes)) {
                $rootObjTypes[] = $rootObjType;
            }
            if (sizeof($rootObjTypes) > 1) {
                throw new BeditaException('all tree roots elements must be of the same type (area|section)');
            }
            if ($rootObjType != 'area' && $rootObjType != 'section') {
                throw new BeditaException('root object type [' . $rootObjType . '] not valid: must be area or section');
            } else if ($rootObjType == 'section') {
                if (empty($options['section_root_id'])) {
                    throw new BeditaException('missing $options[section_root_id] for root section');
                }
            }
        }
        // order sections
        $sectionsByParent = array();
        foreach ($this->import['source']['data']['tree']['sections'] as $section) {
            if (empty($sectionsByParent[$section['parent']])) {
                $sectionsByParent[$section['parent']] = array();
            }
            $sectionsByParent[$section['parent']][] = $section;
        }
        $orderedSections = array();
        foreach ($this->import['source']['data']['tree']['roots'] as $rootId) {
            if (!empty($sectionsByParent[$rootId])) {
                $sections = $sectionsByParent[$rootId];
                $orderedSections = $this->orderSections($orderedSections, $sectionsByParent, $sections);
            }
        }
        $this->import['source']['data']['tree']['sections'] = $orderedSections;
        foreach ($this->import['source']['data']['tree']['sections'] as $section) {
            $this->import['tree']['ids'][] = $section['id'];
            if (!in_array($section['parent'], $this->import['tree']['parents'])) {
                $this->import['tree']['parents'][] = $section['parent'];
            }
        }
        // 4.4 valid parent ids => parents elements must be a subcollection of tree elements
        foreach ($this->import['tree']['parents'] as $parentId) {
            if (!in_array($parentId, $this->import['tree']['ids'])) {
                throw new BeditaException('parent id ' . $parentId . ' not found in tree');
            }
        }
        // expected area/section - elements inside objects... should be in tree too
        foreach ($this->import['expectedParentIds'] as $elemId) {
            if (!in_array($elemId, $this->import['tree']['ids'])) {
                throw new BeditaException('element ' . $elemId . ' not found in specified tree source');
            }
        }
        // 4.5 id referenced in tree must be referenced in objects too
        foreach ($this->import['tree']['ids'] as $treeId) {
            if (!in_array($treeId, $this->import['objects']['ids'])) {
                throw new BeditaException('tree id ' . $treeId . ' not found in objects');
            }
        }
        // 5 relations
        if (!empty($this->import['source']['data']['relations'])) {
            // 5.1 necessary fields (defined in $this->relMinimalSet)
            // relation structure 1: flat array / i.e. array(<relation item>, <relation item>, ...)
            // relation structure 2: group by switch / i.e. array(<relation switch> => array(<relation item>, <relation item>, ...), <relation switch> => array(<relation item>, <relation item>, ...), ...)
            $relationKeys = array_keys($this->import['source']['data']['relations']);
            $relationStructureOne = is_int($relationKeys[0]);
            foreach ($this->relMinimalSet as $field) {
                if ($relationStructureOne) {
                    foreach ($this->import['source']['data']['relations'] as $relation) {
                        if (empty($relation[$field])) {
                            throw new BeditaException('missing field ' . $field . ' for relation');
                        }
                        if ($field == 'idLeft' && !in_array($relation['idLeft'], $this->import['relations']['ids'])) {
                            $this->import['relations']['ids'][] = $relation['idLeft'];
                        }
                        if ($field == 'idRight' && !in_array($relation['idRight'], $this->import['relations']['ids'])) {
                            $this->import['relations']['ids'][] = $relation['idRight'];
                        }
                        if ($field == 'switch' && !in_array($relation['switch'], $this->import['relations']['switches'])) {
                            $this->import['relations']['switches'][] = $relation['switch'];
                        }
                    }
                } else {
                    foreach ($this->import['source']['data']['relations'] as $relationName => &$relations) {
                        foreach ($relations as &$relation) {
                            if (empty($relation[$field])) { // set switch for relation structure two
                                $relation[$field] = $relationName;
                            }
                            if ($field == 'idLeft' && !in_array($relation['idLeft'], $this->import['relations']['ids'])) {
                                $this->import['relations']['ids'][] = $relation['idLeft'];
                            }
                            if ($field == 'idRight' && !in_array($relation['idRight'], $this->import['relations']['ids'])) {
                                $this->import['relations']['ids'][] = $relation['idRight'];
                            }
                            if ($field == 'switch' && !in_array($relation['switch'], $this->import['relations']['switches'])) {
                                $this->import['relations']['switches'][] = $relation['switch'];
                            }
                        }
                    }
                }
            }
            // 5.2 id referenced in relations must be referenced in objects too
            foreach ($this->import['relations']['ids'] as $relId) {
                if (!in_array($relId, $this->import['objects']['ids'])) {
                    throw new BeditaException('relation id (left/right) ' . $relId . ' not found in objects');
                }
            }
            // 5.3 switch must be a valid relation name + valid relation objects (existence of objects and right connection)
            $allRelations = BeLib::getObject('BeConfigure')->mergeAllRelations();
            foreach ($allRelations as $switch => $a) {
                $this->import['allRelations'][$switch] = array(
                    'left' => $a['left'],
                    'right' => $a['right'],
                    'symmetric' => ( !array_key_exists('inverse', $a) || ($a['inverse'] == $switch) ),
                    'inverse' => array_key_exists('inverse', $a) ? $a['inverse'] : $switch,
                );
                if (!$this->import['allRelations'][$switch]['symmetric']) {
                    $this->import['allRelations'][$a['inverse']] = array(
                        'left' => $a['right'],
                        'right' => $a['left'],
                        'symmetric' => false,
                        'inverse' => $switch
                    );
                }
            }
            $this->import['allRelationsKeys'] = array_keys($this->import['allRelations']);
            foreach ($this->import['relations']['switches'] as $switch) {
                if (!in_array($switch, $this->import['allRelationsKeys'])) {
                    throw new BeditaException('relation switch ' . $switch . ' not found in bedita relations');
                }
            }
            // 5.4 objectType(s) must be valid for specified relation switch
            if ($relationStructureOne) {
                foreach ($this->import['source']['data']['relations'] as $relation) {
                    $objTypeLeft = $this->import['objects']['typeById'][$relation['idLeft']];
                    $objTypeRight = $this->import['objects']['typeById'][$relation['idRight']];
                    $switch = $relation['switch'];
                    $symmetric = $this->import['allRelations'][$switch]['symmetric'];
                    $cdl = $this->relationAllowed($objTypeLeft, $this->import['allRelations'][$switch]['left']);
                    $cdr = $this->relationAllowed($objTypeRight, $this->import['allRelations'][$switch]['right']);
                    $cil = $this->relationAllowed($objTypeLeft, $this->import['allRelations'][$switch]['right']);
                    $cir = $this->relationAllowed($objTypeRight, $this->import['allRelations'][$switch]['left']);
                    if ( !($cdl && $cdr) && !($symmetric && ($cil && $cir)) ) {
                        throw new BeditaException('relation switch ' . $switch . ' object not allowed (idLeft: ' . $relation['idLeft'] . ', idRight: ' . $relation['idRight'] . ')');
                    }
                }
            } else {
                foreach ($this->import['source']['data']['relations'] as $relationName => $rr) {
                    foreach ($rr as $r) {
                        $objTypeLeft = $this->import['objects']['typeById'][$r['idLeft']];
                        $objTypeRight = $this->import['objects']['typeById'][$r['idRight']];
                        $symmetric = $this->import['allRelations'][$relationName]['symmetric'];
                        $relationLeftEmpty = empty($this->import['allRelations'][$relationName]['left']);
                        $cdl = $this->relationAllowed($objTypeLeft, $this->import['allRelations'][$relationName]['left']);
                        $cdr = $this->relationAllowed($objTypeRight, $this->import['allRelations'][$relationName]['right']);
                        $cil = $this->relationAllowed($objTypeLeft, $this->import['allRelations'][$relationName]['right']);
                        $cir = $this->relationAllowed($objTypeRight, $this->import['allRelations'][$relationName]['left']);
                        if (!$relationLeftEmpty) { // left empty => relation allowed with every type of objects
                            if ( !($cdl && $cdr) && !($symmetric && ($cil && $cir)) ) {
                                $this->trackWarn('relation switch ' . $relationName . ' object not allowed (idLeft: ' . $r['idLeft'] . ', idRight: ' . $r['idRight'] . ')');
                                // not blocking... to avoid errors for huge database validation
                                // throw new BeditaException('relation switch ' . $relationName . ' object not allowed (idLeft: ' . $r['idLeft'] . ', idRight: ' . $r['idRight'] . ')');
                            }
                        }
                    }
                }
            }
        }
        // 6.media
        if (!empty($this->import['media'])) {
            // 6.1 source folder (sourceMediaRoot)
            // 6.1.1 existence
            if (!file_exists($this->import['sourceMediaRoot'])) {
                throw new BeditaException('sourceMediaRoot folder "' . $this->import['sourceMediaRoot'] . '" not found');
            }
            $this->import['source']['media']['root'] = $this->import['sourceMediaRoot'];
            // ... not for remote folders
            $folder =& new Folder($this->import['source']['media']['root'], true);
            $this->import['source']['media']['size'] = $folder->dirSize();
            // 6.1.2 permits [TODO]
            // ...
            // 6.2 destination folder
            $this->import['destination']['media']['root'] = Configure::read('mediaRoot');
            // 6.2.1 existence
            if (!file_exists($this->import['destination']['media']['root'])) {
                if (!mkdir($this->import['destination']['media']['root'])) {
                    throw new BeditaException('destination folder "' . $this->import['destination']['media']['root'] . '" not found: failure on creating it');
                }
            }
            // 6.2.2 space available
            $this->import['destination']['media']['space'] = disk_free_space($this->import['destination']['media']['root']);
            // 6.3 files
            foreach ($this->import['media'] as $id => &$media) {
                $filePath = $this->import['sourceMediaRoot'] . $media['uri'];
                // 6.3.1 existence (base folder + objects[i].uri) [TODO]
                if (!file_exists($filePath)) {
                    throw new BeditaException('file "' . $filePath . '" not found (object id "' . $id . '")');
                } else {
                    $media['base'] = $this->import['sourceMediaRoot'];
                    $media['full'] = $filePath;
                }
                // 6.3.2 extension allowed [TODO]
                // ...
                // 6.3.3 dimension allowed [TODO]
                // ...
            }
            // 6.3.4 all files dimension < space available
            // space required => $this->import['source']['media']['size']
            // space available => $this->import['destination']['media']['space']
            if ($this->import['source']['media']['size'] >= $this->import['destination']['media']['space']) {
                throw new BeditaException('not enought space on destination folder "' . $this->import['destination']['media']['root'] . '" - space required: ' . $this->import['source']['media']['size'] . ' / space available: ' . $this->import['destination']['media']['space']);
            }
        }        
    }

    /* private methods for relation management */

    private function relationAllowed($objType, array $relType) {
        return empty($relType) || in_array($objType, $relType);
    }

    /* private methods saving objects */

    private function saveProperty($customProperty) {
        // TODO: manage different saving policies | now => direct save of NEW property
        $mode = $this->import['saveMode'];
        $this->trackDebug('- saving property ' . $customProperty['name'] . ' with mode ' . $mode);
        $model = ClassRegistry::init('Property');
        $model->create();
        $objTypeId = Configure::read('objectTypes.' . $customProperty['objectType'] . '.id');
        $property = $model->find('first',
            array(
                'conditions' => array(
                    'name' => $customProperty['name'],
                    'object_type_id' => $objTypeId
                )
            )
        );
        // does custom property exist?
        if (empty($property)) { // no => save it
            $propertyData = array(
                'name' => $customProperty['name'],
                'property_type' => $customProperty['dataType'],
                'object_type_id' => $objTypeId
            );
            if (!empty($customProperty['multipleChoice'])) {
                $propertyData['multiple_choice'] = $customProperty['multipleChoice'];
            }
            if(!$model->save($propertyData)) {
                throw new BeditaException('error saving property (import name ' . $customProperty['name'] . ')');
            }
            $propertyData['id'] = $model->id;
            if (!empty($customProperty['options'])) {
                $propertyOptions = array();
                $this->trackDebug('- saving property options for property "' . $customProperty['name'] . '"');
                $model->PropertyOption->deleteAll("property_id='" . $propertyData['id'] . "'");
                $options = $customProperty['options'];
                foreach ($options as $option) {
                    $propertyOptions[] = array(
                        'property_id' => $propertyData['id'],
                        'property_option' => trim($option)
                    );
                }
                if (!$model->PropertyOption->saveAll($propertyOptions)) {
                    throw new BeditaException('error saving property options (property import name ' . $customProperty['name'] . ')');
                }
            }
            $this->import['config']['properties'][$customProperty['name']] = $propertyData;
            $this->import['saveMap']['property'][$customProperty['name']][] = $propertyData['id'];
            $this->trackDebug('- saving property ' . $customProperty['name'] . ' with BEdita Property id ' . $propertyData['id'] . ' ... END');
        } else { // yes => skip | something else?
            // TODO: update? in case of options 'extra data', update could be useful or necessary
            $this->trackDebug('- property "' . $customProperty['name'] . '" found: skip saving [TODO: update?]');
        }
    }

    private function saveArea($area) {
        // TODO: manage different saving policies | now => direct save of NEW area
        $mode = $this->import['saveMode'];
        $this->trackDebug('- saving area ' . $area['id'] . ' with mode ' . $mode . ' ... START');
        $newArea = array_merge($this->objDefaults, $this->import['source']['data']['objects'][$area['id']]);
        unset($newArea['id']);
        $model = ClassRegistry::init('Area');
        $model->create();
        if (!$model->save($newArea)) {
            throw new BeditaException('error saving area (import id ' . $area['id'] . ')');
        }
        $this->import['saveMap'][$area['id']] = $model->id;
        $this->trackDebug('- saving area ' . $area['id'] . ' with BEdita Area id ' . $model->id . ' ... END');
    }

    private function saveSection($section, $parendId = null) {
        if (!empty($this->import['saveMap'][$section['id']])) {
            $this->trackDebug('-- section ' . $section['id'] . ' already saved with BEdita id ' . $this->import['saveMap'][$section['id']]);
        } else {
            $mode = $this->import['saveMode'];
            $this->trackDebug('-- saving section ' . $section['id'] . ' with mode ' . $mode . ' ... START');
            // TODO: manage different saving policies | now => direct save of NEW section
            $newSection = array_merge($this->objDefaults, $this->import['source']['data']['objects'][$section['id']]);
            unset($newSection['id']);
            $newSection['parent_id'] = ($parendId != null) ? $parendId : $this->import['saveMap'][$section['parent']];
            $model = ClassRegistry::init('Section');
            $model->create();
            if (!$model->save($newSection)) {
                throw new BeditaException('error saving section (import id ' . $section['id'] . ')');
            }
            $this->import['saveMap'][$section['id']] = $model->id;
            $this->trackDebug('-- saving section ' . $section['id'] . ' with BEdita Section id ' . $model->id . ' ... END');
        }
    }

    private function saveObject($object) {
        if (!empty($this->import['saveMap'][$object['id']])) {
            $this->trackDebug($object['objectType'] . ' ' . $object['id'] . ' already saved with BEdita id ' . $this->import['saveMap'][$object['id']]);
        } else {
            $this->trackDebug('2.3.2 save object with policy \'NEW\'');
            $this->trackDebug('2.3.3 save object with other policies [TODO]');

            $mode = $this->import['saveMode'];
            $this->trackDebug('- saving object ' . $object['id'] . ' with mode ' . $mode . ' ... START');

            $object['Category'] = array();
            if (!empty($object['categories'])) {
                $this->trackDebug('2.3.4 save object.categories');
                foreach ($object['categories'] as $category) {
                    $object['Category'] = $this->saveCategory($category['name'], $object['objectType']);
                }
            }
            if (!empty($object['tags'])) {
                $this->trackDebug('2.3.5 save object.tags');
                $tagListString = '';
                foreach ($object['tags'] as $tag) {
                    $tagListString.= $tag . ',';
                }
                if (!empty($tagListString)) {
                    $tagListString = substr($tagListString, 0, strlen($tagListString)-1);
                    $tags = $this->saveTags($tagListString);
                    $object['Category'] = array_merge($object['Category'], $tags);    
                }
            }
            $newObject = array_merge($this->objDefaults, $object);
            unset($newObject['id']);
            $model = ClassRegistry::init(Inflector::camelize($object['objectType']));
            $model->create();
            if (!$model->save($newObject)) {
                throw new BeditaException('error saving ' . $object['objectType'] . ' (import id ' . $object['id'] . ')');
            }
            if (!empty($object['customProperties'])) {
                $this->trackDebug('2.3.6 save object.customProperties');
                $this->trackDebug('- saving custom properties for ' . $object['objectType'] . ' ' . $object['id'] . ' with BEdita id ' . $model->id);
                $object['ObjectProperty'] = array();
                foreach ($object['customProperties'] as $customProperty) {
                    $property = array(
                        'object_id' => $model->id,
                        'property_id' => $this->import['config']['properties'][$customProperty['name']]['id'],
                        'property_value' => $customProperty['value']
                    );
                    $object['ObjectProperty'][] = $property;
                }
                foreach ($object['ObjectProperty'] as $objectProperty) {
                    $objectPropertyModel = ClassRegistry::init('ObjectProperty');
                    $objectPropertyModel->create();
                    if (!$objectPropertyModel->save($objectProperty)) {
                        throw new BeditaException('error saving ObjectProperty for ' . $object['objectType'] . ' (import id ' . $object['id'] . ')');
                    }
                }
            }
            $this->import['saveMap'][$object['id']] = $model->id;
            $this->trackDebug('- saving ' . $object['objectType'] . ' ' . $object['id'] . ' with BEdita id ' . $model->id . ' ... object saved');
            if (!empty($object['parents'])) {
                $tree = ClassRegistry::init('Tree');
                foreach ($object['parents'] as $parentId) {
                    $beParentId = $this->import['saveMap'][$parentId];
                    if (!empty($beParentId)) {
                        $this->trackDebug('-- saving tree record for ' . $object['objectType'] . ' ' . $object['id'] . ' (BEdita id ' . $model->id . ') - (position - import parent id ' . $parentId . ' / BEdita parent id ' . $beParentId . ') ... START');
                        $tree->appendChild($model->id,$beParentId);
                        $this->trackDebug('-- saving tree record for ' . $object['objectType'] . ' ' . $object['id'] . ' (BEdita id ' . $model->id . ') - (position - import parent id ' . $parentId . ' / BEdita parent id ' . $beParentId . ') ... END');
                    } else {
                        $this->trackDebug('-- bedita object not found in import saveMap for id ' . $parentId);
                    }
                }
            } else {
                $this->trackDebug('-- empty tree record for ' . $object['objectType'] . ' ' . $object['id'] . ' with BEdita id ' . $model->id . ' ...');
            }
            $this->trackDebug('- saving object ' . $object['id'] . ' with BEdita id ' . $model->id . ' ... END');
        }
    }

    private function saveCategory($categoryName, $objectType) {
        $categoryModel = ClassRegistry::init('Category');
        $objectTypeId = Configure::read("objectTypes.$objectType.id");
        $categories = array( $categoryName );
        return $categoryModel->findCreateCategories($categories, $objectTypeId);
    }

    private function saveTags($tagListString) {
        $categoryModel = ClassRegistry::init('Category');
        return $categoryModel->saveTagList($tagListString);
    }

    private function saveRelation($relation, $counter) {
        $this->trackDebug('- saving relation ' . $counter . ': ' . $relation['switch'] . ' ... START');
        $relationData = array(
            'id' => $this->import['saveMap'][$relation['idLeft']],
            'objectId' => $this->import['saveMap'][$relation['idRight']],
            'switch' => $relation['switch'],
            'inverse' => $this->import['allRelations'][$relation['switch']]['inverse'],
            'priority' => NULL,
            'params' => array()
        );
        if (!empty($relation['priority'])) {
            $relationData['priority'] = $relation['priority'];
        }
        if (!empty($relation['params'])) {
            $relationData['params'] = $relation['params'];
        }
        $objRelModel = ClassRegistry::init('ObjectRelation');
        if (!@$objRelModel->createRelationAndInverse(
                $relationData['id'],
                $relationData['objectId'],
                $relationData['switch'],
                $relationData['inverse'],
                $relationData['priority'],
                $relationData['params']) ) {
            throw new BeditaException('Error saving relation ' . $relation['switch'] . ' idLeft ' . $relation['idLeft'] . ' idRight ' . $relation['idRight'] );
        }
        $this->import['saveMap']['relations'][$objRelModel->id][] = $relationData;
        $this->trackDebug('- saving relation ' . $counter . ': ' . $relation['switch'] . ' ... DONE');
    }

    /* object utils */

    private function orderSections(array $orderedSections, array $sectionsByParent, array $sections) {
        if (!empty($sections)) {
            foreach ($sections as $section) {
                $orderedSections[$section['id']] = $section;
                if (!empty($sectionsByParent[$section['id']])) {
                    $orderedSections = $this->orderSections($orderedSections, $sectionsByParent, $sectionsByParent[$section['id']]);
                }
            }
        }
        return $orderedSections;
    }

    private function cleanObjectFields(array &$object) {
        foreach ($this->export['objectUnsetFields'] as $unsetKey) {
            if (isset($object[$unsetKey])) {
                unset($object[$unsetKey]);
            }
        }
        foreach ($object as $key => $value) {
            if (empty($value)) {
                unset($object[$key]);
            }
        }
    }

    private function rearrangeObjectFields(array &$object) {
        if (isset($object['RelatedObject'])) {
            foreach ($object['RelatedObject'] as $relation) {
                if (empty($this->export['destination']['byType']['ARRAY']['objects'][$relation['object_id']])) {
                    $object['relatedObjectIds'][] = $relation['object_id'];
                }
                if (!in_array($relation['switch'], $this->export['destination']['byType']['ARRAY']['relations'])) {
                    $this->export['destination']['byType']['ARRAY']['relations'][$relation['switch']] = array();
                }
                $r = array(
                    'idLeft' => $relation['id'],
                    'idRight' => $relation['object_id'],
                    'priority' => $relation['priority']
                );
                if (!empty($relation['params'])) {
                    $r['params'] = $relation['params'];
                }
                $this->export['destination']['byType']['ARRAY']['relations'][$relation['switch']][] = $r;
            }
            unset($object['RelatedObject']);
        }
        if (isset($object['LangText'])) {
            // TODO: arrange lang text data
            unset($object['LangText']);
        }
        if (isset($object['Annotation'])) {
            // TODO: arrange annotation data
            unset($object['Annotation']);
        }
        if (isset($object['GeoTag'])) {
            // TODO: arrange geotag data
            unset($object['GeoTag']);
        }
        if (isset($object['ObjectProperty'])) {
            foreach ($object['ObjectProperty'] as $cproperty) {
                $this->export['customProperties'][$cproperty['id']] = $cproperty;
            }
            unset($object['ObjectProperty']);
        }
        if (isset($object['customProperties'])) {
            $cproperties = array();
            foreach ($object['customProperties'] as $cplabel => $cpvalue) {
                $cproperty = array(
                    'name' => $cplabel
                );
                if (!is_array($cpvalue)) {
                    $cproperty['value'] = $cpvalue;
                } else {
                    if (sizeof($cpvalue) == 1) {
                        $cproperty['value'] = $cpvalue[0];
                    } else {
                        $cproperty['value'] = $cpvalue;
                    }
                }
                $cproperties[] = $cproperty;
            }
            $object['customProperties'] = $cproperties;
        }
        if (isset($object['Category'])) {
            $categories = array();
            foreach ($object['Category'] as $category) {
                $c = array(
                    'name' => $category['name']
                );
                if (!empty($category['label'])) {
                    $c['label'] = $category['label'];
                }
                $categories[] = $c;
            }
            $object['categories'] = $categories;
            unset($object['Category']);
        }
        if (isset($object['Tag'])) {
            $tags = array();
            foreach ($object['Tag'] as $tag) {
                $tags[] = (!empty($tag['label'])) ? $tag['label'] : $tag['name'];
            }
            $object['tags'] = $tags;
            unset($object['Tag']);
        }
    }

    /**
     * clean object and prepare relation data
     * 
     * remove empty data (or null)
     * remove meaningless data for export (i.e. user, stats, etc. @see $this->export['objectUnsetFields'])
     * 
     * @param  array $object data
     * @return array $object data
     */
    private function prepareObjectForExport(array &$object) {
        $this->trackDebug('... prepareObjectForExport for object id ' . $object['id']);
        if (!empty($object['object_type_id'])) {
            $object['objectType'] = Configure::read('objectTypes.' . $object['object_type_id'] . '.name');
        }
        // 1 parse data, unset unused fields and remove entries for empty values, recursively
        $this->trackDebug('... cleanObjectFields for object id ' . $object['id']);
        $this->cleanObjectFields($object);
        // 2 parse and rearrange object data
        $this->trackDebug('... rearrangeObjectFields for object id ' . $object['id']);
        $this->rearrangeObjectFields($object);
        // 3 set object for result
        $relatedObjectIds = array();
        if (!empty($object['relatedObjectIds'])) {
            $relatedObjectIds = $object['relatedObjectIds'];
            unset($object['relatedObjectIds']);
        }
        $this->export['destination']['byType']['ARRAY']['objects'][$object['id']] = $object;
        // 4 set related objects
        $this->trackDebug('... load related objects');
        if (!empty($relatedObjectIds)) {
            $conf = Configure::getInstance();
            foreach ($relatedObjectIds as $relatedObjectId) {
                $objModel = ClassRegistry::init('BEObject');
                $objectTypeId = $objModel->findObjectTypeId($relatedObjectId);
                if (isset($conf->objectTypes[$objectTypeId])) {
                    $model = $conf->objectTypes[$objectTypeId]['model'];
                } else if (isset($conf->objectTypesExt[$objectTypeId])) {
                    $model = $conf->objectTypesExt[$objectTypeId]['model'];
                } else {
                    throw new BeditaException('Model not found per objecttypeId "' . $objectTypeId . '"');
                }
                $relatedObjModel = ClassRegistry::init($model);
                $relatedObjModel->contain(
                    $this->export['contain']
                );
                $relatedObj = $relatedObjModel->findById($relatedObjectId);
                $this->prepareObjectForExport($relatedObj);
            }
        }
        // 5 set media uris        
        if (!empty($object['uri'])) { // map object id with media uri
            $this->export['media'][$object['id']] = $object['uri'];
        }
    }

    private function parentsForObjId($objId, $rootIds) {
        $tree = ClassRegistry::init('Tree');
        $parents = $tree->find('list',
            array(
                'fields' => array('parent_id'),
                'conditions' => array(
                    'id' => $objId,
                    'area_id' => $rootIds
                )
            )
        );
        if (empty($parents)) {
            return array();
        }
        return array_values($parents);
    }

    private function prepareObjectsForExportByParents($parents) {
        $tree = ClassRegistry::init('Tree');
        $tree->bindModel(
            array('belongsTo' => array('BEObject' => array('foreignKey' => 'id'))),
            false
        );
        $conf = Configure::getInstance();
        foreach ($parents as $parentId) {
            $this->trackDebug('... extracting objects inside rootId ' . $parentId);
            $children = $tree->find('all',
                array(
                    'fields' => array('Tree.id', 'BEObject.object_type_id'),
                    'conditions' => array(
                        'parent_id' => $parentId,
                        'NOT' => array(
                            'object_type_id' => array(
                                Configure::read('objectTypes.area.id'),
                                Configure::read('objectTypes.section.id')
                            )
                        )
                    )
                )
            );
            if (!empty($children)) {
                foreach ($children as $child) {
                    $objectId = $child['Tree']['id'];
                    $objectTypeId = $child['BEObject']['object_type_id'];
                    if (isset($conf->objectTypes[$objectTypeId])) {
                        $model = $conf->objectTypes[$objectTypeId]['model'];
                    } else {
                        $model = $conf->objectTypesExt[$objectTypeId]['model'];
                    }
                    $objModel = ClassRegistry::init($model);
                    $objModel->contain(
                        $this->export['contain']
                    );
                    $obj = $objModel->findById($objectId);
                    $this->prepareObjectForExport($obj);
                }
            }
        }
        $tree->unbindModel(array('belongsTo' => array('BEObject')));
    }

    /* file utils */

    /**
     * Copy $source (from $sourceBasePath) to $destBasePath, creating subfolders if necessary
     * 
     * @param  string $sourceBasePath folder
     * @param  string $destBasePath folder
     * @param  string $source path to file (file name included)
     */
    private function copyFileToFolder($sourceBasePath, $destBasePath, $source) {
        $tmp = explode(DS, $source);
        $dirs = array();
        $dirsString = "";
        foreach($tmp as $dir) {
            if (!empty($dir)) {
                $dirsString.= DS . $dir;
                $dirs[] = $dir;
            }
        }
        $name = array_pop($tmp);
        $pointPosition = strrpos($name,".");
        $filename = $tmpname = substr($name, 0, $pointPosition);
        $ext = substr($name, $pointPosition);
        $counter = 1;
        // creating directories
        $d = $destBasePath;
        $dirs = array_reverse($dirs);
        while (($current = array_pop($dirs))) {
            $d.= DS . $current;
            if (!file_exists($d) && !is_dir($d)) {
                if (!mkdir($d)) {
                    throw new BeditaException('Error creating dir "' . $current . '"');
                }
            }
        }
        // save new name (passed by reference)
        $name = $filename . $ext;
        $destination = $destBasePath . DS . $dirsString . DS . $name;
        if (!copy($sourceBasePath . DS . $source, $destination)) {
            $this->trackError('Error copying file "' . $sourceBasePath . DS . $source . '" to "' . $destination);
            //$this->trackWarn('Error copying file "' . $sourceBasePath . DS . $source . '" to "' . $destination);
            //throw new BeditaException('Error copying file "' . $sourceBasePath . DS . $source . '" to "' . $destination);
        }
    }

    /* private logging functions */

    private function trackError($message) {
        $this->trackResult('ERROR', $message);
    }

    private function trackWarn($message) {
        $this->trackResult('WARN', $message);
    }

    private function trackInfo($message) {
        $this->trackResult('INFO', $message);
    }

    private function trackDebug($message) {
        $this->trackResult('DEBUG', $message);
    }

    private function trackResult($level = 'INFO', $message) {
        $this->result['log'][$level][] = $message;
        $this->result['log']['ALL'][] = $level . ': ' . $message;
        if ($this->logLevels[$level] <= $this->logLevel) {
            $this->result['log']['filtered'][] = $message;
            $this->log($message, strtolower($level));
            if (!empty($this->logFile)) {
                $this->log($message, $this->logFile);
            }
        }
    }

    private function jsonLastErrorMsg() {
        $msg = '';
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $msg = ' - No errors';
                break;
            case JSON_ERROR_DEPTH:
                $msg = ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $msg = ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $msg = ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $msg = ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $msg = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $msg = ' - Unknown error';
                 break;
        }
        return $msg;
    }
}
?>
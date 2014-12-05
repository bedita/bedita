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

class BEFormat extends BEAppModel
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
            'string' => '', // jsonString
            'data' => array() // json_decode of jsonString
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

    protected $result = array(
    );

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
     * Import jsonString to BEdita
     *
     * @param $jsonString string
     * @param $options array
     * @return $result array
     *
     * $jsonString = '{
     *    'tree': {
     *       'roots': ['<id>'],
     *       'sections': [
     *        {
     *           'id': '<id>',
     *           'parent': '<id>'
     *        }
     *       ]
     *     },
     *    'objects': [
     *      {
     *         'id': '<id>',
     *         'objectType': '<objectType>'
     *      },
     *      {
     *         'id': '<id>',
     *         'objectType': '<objectType>'
     *      }
     *     ],
     *    'relations': [
     *      {
     *         'idLeft': '<id>',
     *         'idRight': '<id>',
     *         'switch': '<relationName>',     
     *         'params': []
     *      },
     *      {
     *         'idLeft': '<id>',
     *         'idRight': '<id>',
     *         'switch': '<relationName>',     
     *         'params': []
     *      }
     *    ]
     * }'
     *
     * $options = array(
     *    'logDebug' => true, // can be true|false
     *    'saveMode' => 0, // can be 0 (MERGE), 1 (NEW), 2 (OVERRIDE), 3 (IGNORE), 4 (UPDATE)
     *)
     * 
     * 1. Validating input data
     * 2. Importing data to BEdita
     * 3. Return result object
    */
    
    public function import($jsonString, $options = array()) {

        // setting log level - default INFO
        if (!empty($options['logDebug'])) {
            if ($options['logDebug'] == true) {
                $this->import['logLevel'] = $this->logLevels['DEBUG']; // DEBUG
            } else {
                $this->import['logLevel'] = $this->logLevels['INFO'];; // INFO
            }
        }
        $this->logLevel = $this->import['logLevel'];
        echo "\n" . 'Import options - logLevel: ' . $this->logLevel . ' (' . array_search($this->logLevel, $this->logLevels) . ')';

        // setting save mode - default NEW
        if (!empty($options['saveMode'])) {
            $this->import['saveMode'] = $options['saveMode'];
        }
        echo "\n" . 'Import options - saveMode: ' . $this->import['saveMode'] . ' (' . array_search($this->import['saveMode'], $this->saveModes, true) . ')' . "\n\n";

        try {

            // 1. Validate
            $this->trackInfo('1. validate start');
            $this->validate($jsonString, $options);
            $this->trackInfo('1. validate OK');

            // 2. Importing
            $this->trackInfo('2. import start');

            // TODO: import config
            // 2.1 import config [TODO]
            $this->trackInfo('2.1 import config [TODO]');
            // 2.1.1 import custom properties [TODO]
            $this->trackInfo('2.1.1 import custom properties [TODO]');
            // 2.1.? [...] [TODO]
            $this->trackInfo('2.1.? [...] [TODO]');

            // 2.2 save areas/sections
            $this->trackInfo('2.2 save areas/sections');
            // 2.2.1 save roots (areas/sections)
            $this->trackInfo('2.2.1 save roots (areas/sections)');

            $rootIds = $this->import['tree']['roots'];
            foreach ($rootIds as $rootId) {
                $rootData = $this->import['source']['data']['objects'][$rootId];
                $rootObjType = $this->import['source']['data']['objects'][$rootId]['objectType'];
                // 2.2.1.1 save area(s) with policy 'NEW'
                $this->trackDebug('2.2.1.1 save area(s) with policy \'NEW\'');
                // 2.2.1.2 save area(s) with other policies [TODO]
                $this->trackDebug('2.2.1.2 save area(s) with other policies [TODO]');
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
                // 2.2.2.1 save section(s) with policy 'NEW'
                $this->trackDebug('2.2.2.1 save section(s) with policy \'NEW\'');
                // 2.2.2.2 save section(s) with other policies [TODO]
                $this->trackDebug('2.2.2.2 save section(s) with other policies [TODO]');
                $this->saveSection($section);
            }

            $this->trackInfo('2.3 save objects');
            foreach ($this->import['source']['data']['objects'] as &$object) {
                // 2.3.1 save object with policy 'NEW'
                $this->trackDebug('2.3.1 save object with policy \'NEW\'');
                // 2.3.2 save object with other policies [TODO]
                $this->trackDebug('2.3.2 save object with other policies [TODO]');
                // 2.3.3 save object.customProperties [TODO]
                $this->trackDebug('2.3.3 save object.customProperties [TODO]');
                // 2.3.4 save object.categories [TODO]
                $this->trackDebug('2.3.4 save object.categories [TODO]');
                // 2.3.5 save object.tags [TODO]
                $this->trackDebug('2.3.5 save object.tags [TODO]');
                $this->saveObject($object);
            }

            // 2.4 save relations
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

            // 2.5 save media [TODO]
            $this->trackInfo('2.5 save media [TODO]');

            // 2.? [...] [TODO]
            $this->trackInfo('2.? [...] [TODO]');

            $this->trackInfo('2. import OK');
        } catch(Exception $e) {

            $this->trackError('ERROR: ' . $e->getMessage());

        }
        // 3. result
        $this->trackInfo('3. result');
        // 3.1 format / process result (?) [TODO]
        $this->trackDebug('3.1 format / process result (?) [TODO]');
        // 3.2 return result
        $this->trackDebug('3.2 return result');
        return $this->result;
    }

    public function export(array &$objects, $options = array()) {
        // setting log level - default ERROR
        if (!empty($options['logDebug'])) {
            if ($options['logDebug'] == true) {
                $this->export['logLevel'] = 3; // DEBUG
            } else {
                $this->export['logLevel'] = 0; // ERROR
            }
        }
        $this->logLevel = $this->export['logLevel'];

        // TODO: implement
    }

    /**
     * Validation of jsonString and related objects and semantics
     * 
     * 1 json
     * 1.1 not empty
     * 1.2 valid (json_decode / json_last_error)
     *
     * 2 config
     * 2.1 custom properties
     * 2.1.1 fields not empty: name, objectType, dataType
     * 2.1.2 valid objectType
     * 2.1.3 dataType can be 'number', 'date', 'text', 'options'
     * 2.1.4 existence [TODO]
     * 2.1.5 conflict [TODO]
     * 2.1.6 objects.customProperty consistence (objects.customProperty must be declared in config.customProperties) [TODO]
     * [...]
     *
     * 3 objects
     * 3.1 not empty
     * 3.2 necessary fields (defined in $this->objMinimalSet)
     * 3.3 objectType existence
     * 3.4 specific validation by objectType [TODO]
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
     * 6 media [TODO]
     * 7 [...] [TODO]
     */
    public function validate($jsonString, $options = array()) {

        // 1 json
        $this->import['source']['string'] = $jsonString;

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

        // convert source objects to alternative structure
        $this->import['source']['data']['objects'] = Set::combine($this->import['source']['data'], 'objects.{n}.id', 'objects.{n}');
        $this->import['objects']['ids'] = array_keys($this->import['source']['data']['objects']);

        // 2 config
        if (!empty($this->import['source']['data']['config'])) {

            // 2.1 custom properties
            if (!empty($this->import['source']['data']['config']['customProperties'])) {
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
                    $ot = Configure::read('objectTypes.' . $customProperty['objectType'] . '.id');
                    if (empty($ot)) {
                        throw new BeditaException('config.customProperties: objectType ' . $customProperty['objectType'] . ' not found');
                    }

                    // 2.1.3 dataType can be 'number', 'date', 'text', 'options'
                    if (!in_array($customProperty['dataType'],$this->customPropertyDataTypes)) {
                        throw new BeditaException('config.customProperties: dataType ' . $customProperty['dataType'] . ' not allowed | dataType should be number, date, text or options');
                    }
                    // 2.1.4 existence
                    // TODO: check existence

                    // 2.1.5 conflict
                    // TODO: check conflict
                }
            }
        }
        // 2.1.5 objects.customProperty consistence (objects.customProperty must be declared in config.customProperties)
        // TODO: check consistence

        // 3 objects consistence validation

        // 3.1 non empty
        if (empty($this->import['source']['data']) || empty($this->import['source']['data']['objects'])) {
            throw new BeditaException('empty objects set');
        }

        // 3.2 necessary fields (defined in $this->objMinimalSet)
        foreach ($this->objMinimalSet as $field) {
            foreach ($this->import['source']['data']['objects'] as $object) {
                if (empty($object[$field])) {
                    throw new BeditaException('missing field ' . $field . ' for object');
                }
            }
        }
        foreach ($this->import['source']['data']['objects'] as $object) {
            if (!in_array($object['objectType'], $this->import['objects']['types'])) {
                $this->import['objects']['types'][] = $object['objectType'];
            }
            $this->import['objects']['typeById'][$object['id']] = $object['objectType'];
        }

        // 3.3 objectType existence
        foreach ($this->import['objects']['types'] as $objType) {
            $ot = Configure::read('objectTypes.' . $objType . '.id');
            if (empty($ot)) {
                throw new BeditaException('missing objectType ' . $objType);
            }
        }

        // 3.4 specific validation by objectType
        // TODO: implement it / idea: use model reflection (i.e. if <modelClass> has method 'validateBeforeImport' then invoke it)

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

                        $cdl = $this->relationAllowed($objTypeLeft, $this->import['allRelations'][$relationName]['left']);
                        $cdr = $this->relationAllowed($objTypeRight, $this->import['allRelations'][$relationName]['right']);
                        $cil = $this->relationAllowed($objTypeLeft, $this->import['allRelations'][$relationName]['right']);
                        $cir = $this->relationAllowed($objTypeRight, $this->import['allRelations'][$relationName]['left']);

                        if ( !($cdl && $cdr) && !($symmetric && ($cil && $cir)) ) {
                             throw new BeditaException('relation switch ' . $relationName . ' object not allowed (idLeft: ' . $r['idLeft'] . ', idRight: ' . $r['idRight'] . ')');
                        }                        
                    }
                }
            }
        }
    }

    /* private methods for relation management */

    private function relationAllowed($objType, array $relType) {
        return empty($relType) || in_array($objType, $relType);
    }

    /* private methods saving objects */

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
            $mode = $this->import['saveMode'];
            $this->trackDebug('- saving object ' . $object['id'] . ' with mode ' . $mode . ' ... START');
            // TODO: manage different saving policies | now => direct save of NEW object
            $newObject = array_merge($this->objDefaults, $object);
            unset($newObject['id']);
            $model = ClassRegistry::init(Inflector::camelize($object['objectType']));
            $model->create();
            if (!$model->save($newObject)) {
                throw new BeditaException('error saving ' . $object['objectType'] . ' (import id ' . $object['id'] . ')');
            }
            $this->import['saveMap'][$object['id']] = $model->id;
            $this->trackDebug('- saving ' . $object['objectType'] . ' ' . $object['id'] . ' with BEdita id ' . $model->id . ' ... object saved');
            if (!empty($object['parents'])) {
                $tree = ClassRegistry::init('Tree');
                foreach ($object['parents'] as $parentId) {
                    $beParentId = $this->import['saveMap'][$parentId];
                    $this->trackDebug('-- saving tree record for ' . $object['objectType'] . ' ' . $object['id'] . ' (BEdita id ' . $model->id . ') - (position - import parent id ' . $parentId . ' / BEdita parent id ' . $beParentId . ') ... START');
                    $tree->appendChild($model->id,$beParentId);
                    $this->trackDebug('-- saving tree record for ' . $object['objectType'] . ' ' . $object['id'] . ' (BEdita id ' . $model->id . ') - (position - import parent id ' . $parentId . ' / BEdita parent id ' . $beParentId . ') ... END');
                }
            }
            $this->trackDebug('- saving object ' . $object['id'] . ' with BEdita id ' . $model->id . ' ... END');
        }
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
            echo "\n[$level] $message";
            $this->log($message, strtolower($level));
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
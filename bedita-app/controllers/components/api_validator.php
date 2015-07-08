<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2015 ChannelWeb Srl, Chialab Srl
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
 * ApiValidatorComponent class
 *
 * Used to validate data before save/delete and to test conditions on objects
 *
 */
class ApiValidatorComponent extends Object {

    /**
     * Controller instance
     *
     * @var Controller
     */
    public $controller = null;

    /**
     * List of object types writable
     *
     * @var array
     */
    protected $writableObjects = array();

    /**
     * Initialize function
     *
     * @param Controller $controller
     * @return void
     */
    public function initialize(Controller $controller, array $settings = array()) {
        $this->controller = &$controller;
        $this->_set($settings);
        $validateConf = Configure::read('api.validation');
        if (!empty($validateConf['writableObjects'])) {
            $this->writableObjects = $validateConf['writableObjects'];
        }
    }

    /**
     * Return true if an object type is writable
     *
     * @param string $objectType the object type
     * @return boolean
     */
    public function isObjectTypeWritable($objectType) {
        if (is_numeric($objectType)) {
            $objectType = Configure::read('objectTypes.' . $objectType . 'name');
        }
        return in_array($objectType, $this->writableObjects);
    }

    /**
     * Check if $object data are valid for save
     *
     * If some validation fails it throws a bad request exception
     *
     * @throws BeditaBadRequestException
     * @param array $object object data
     * @return void
     */
    public function checkObject($object) {
        if (empty($object['object_type_id'])) {
            throw new BeditaBadRequestException('Object type undefined');
        }

        $beObject = ClassRegistry::init('BEObject');

        // validate object type
        if (!empty($object['id'])) {
            $objectTypeId = $beObject->findObjectTypeId($object['id']);
            if ($objectTypeId != $object['object_type_id']) {
                throw new BeditaBadRequestException(
                    'Object type mismatch: object with id=' . $object['id'] . ' has not object_type_id=' . $object['object_type_id']
                );
            }
        }
        $objectType = Configure::read('objectTypes.' . $object['object_type_id'] . '.name');
        if (!empty($object['object_type']) && $object['object_type'] != $objectType) {
            throw new BeditaBadRequestException(
                'Object type mismatch: ' . $object['object_type'] . ' not correspond to ' . $object['object_type_id']
            );
        }
        if (!$this->isObjectTypeWritable($objectType)) {
            throw new BeditaBadRequestException($objectType . ' is not writable');
        }

        // prepare list of forbidden fields to avoid automatic save
        $forbiddenFields = array_merge(
            array_keys($beObject->getAssociated()),
            array('DateItem', 'destination')
        );
        foreach ($object as $key => $value) {
            if (in_array($key, $forbiddenFields)) {
                throw new BeditaBadRequestException($key . ' field is forbidden');
            }
        }

        $parentsEmpty = true;
        $relationsEmpty = true;
        if (!empty($object['relations'])) {
            $this->checkRelations($object['relations'], $objectType);
            foreach ($object['relations'] as $name => $relData) {
                if (!empty($relData)) {
                    $relationsEmpty = false;
                    break;
                }
            }
        }
        if (!empty($object['parents'])) {
            $parentsEmpty = false;
            $branches = array(
                Configure::read('objectTypes.area.id'),
                Configure::read('objectTypes.section.id')
            );
            if (in_array($object['object_type_id'], $branches) && count($object['parents']) > 1) {
                throw new BeditaBadRequestException($objectType . ' can have only one parent');
            }
            $this->checkObjectAccess($object['parents']);
        }

        // if new object parents or relations cannot be empty
        if (empty($object['id']) && $parentsEmpty && $relationsEmpty) {
            throw new BeditaBadRequestException('Parents and/or relations can not both be empty');
        }

        if (!empty($object['categories'])) {
            $this->checkCategories($object['categories'], $object['object_type_id']);
        }
        if (!empty($object['tags'])) {
            $this->checkTags($object['tags']);
        }
        if (!empty($object['geo_tags'])) {

        }
        if (!empty($object['date_items'])) {
            $modelName = Configure::read('objectTypes.' . $object['object_type_id'] . '.model');
            $objectModel = ClassRegistry::init($modelName);
            $associations = $objectModel->getAssociated();
            if (!array_key_exists('DateItem', $associations)) {
                throw new BeditaBadRequestException('date_items is invalid for ' . $objectType);
            }
            $objectId = !empty($object['id']) ? $object['id'] : null;
            $this->checkDateItems($object['date_items'], $objectId);
        }
    }

    /**
     * Check if an object is reachable:
     * - check if object is reacheable looking also permissions
     * - if it fails check again if it's reachable but without checking permissions
     *     - if it fails, then it throws 404
     *     - if it is, then it checks if user is logged
     *         - if it fails then it throws 401
     *         - if it is then it throws 403
     *
     * @see self::isObjectReachable()
     * @param int $objectId the object id
     * @return void
     */
    public function checkObjectReachable($objectId) {
        if (empty($objectId)) {
            throw new BeditaNotFoundException();
        }
        // check if object $id is reachable
        if (!$this->isObjectReachable($objectId)) {
            // redo without checking permissions to know if it has to return 404
            if (!$this->isObjectReachable($objectId, false)) {
                throw new BeditaNotFoundException();
            }
            if (!$this->controller->BeAuth->identify()) {
                throw new BeditaUnauthorizedException();
            }
            throw new BeditaForbiddenException('Object ' . $objectId . ' is forbidden');
        }
    }

    /**
     * Return true if object $objectId is reachable, false otherwise.
     * 'Reachable' means that object is on publication tree or have at least a related object on tree
     * If $checkPermission is true an additional check on 'frontend_acccess_with_block' permission is done
     *
     * @param int $objectId the object id
     * @param boolean $checkPermissions if permission has to be checked, default true
     * @return boolean
     */
    public function isObjectReachable($objectId, $checkPermissions = true) {
        $tree = ClassRegistry::init('Tree');
        $publication = $this->controller->getPublication();
        $isOnTree = $tree->isOnTree($objectId, $publication['id'], $this->controller->getStatus());
        // check position on tree and permission
        if ($checkPermissions) {
            if ($isOnTree && $this->isObjectAccessible($objectId)) {
                return true;
            }
            if ($this->hasRelatedObjectsAccessible($objectId)) {
                return true;
            }
            return false;
        // check only position on tree
        } else {
            if ($isOnTree) {
                return true;
            }
            return $tree->relatedObjectsOnTree($objectId, array(
                    'area_id' => $publication['id'],
                    'status' => $this->controller->getStatus(),
                    'count' => true
                ));
        }
    }

    /**
     * Return true if at least an object related to $objectId is on tree and it's accessible, false otherwise
     * 'Accessible' is defined in self::isObjectAccessible()
     *
     * @param int $objectId the object id
     * @return boolean
     */
    public function hasRelatedObjectsAccessible($objectId) {
        $tree = ClassRegistry::init('Tree');
        $publication = $this->controller->getPublication();
        $relatedObjects = $tree->relatedObjectsOnTree($objectId, array(
            'area_id' => $publication['id'],
            'status' => $this->controller->getStatus()
        ));

        $result = false;
        foreach ($relatedObjects as $id) {
            if ($this->isObjectAccessible($id)) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    /**
     * Return true if $objectId and its parents are accessible for authorized user, false otherwise.
     * 'Accessible' means without 'frontend_access_with_block' permission set for groups that the user doesn't belong.
     * If object hasn't parents is not accessible
     *
     * @param int $objectId the object id
     * @return boolean
     */
    public function isObjectAccessible($objectId) {
        $permission = ClassRegistry::init('Permission');
        $publication = $this->controller->getPublication();
        return $permission->isObjectsAndParentsAccessible($objectId,
                    array(
                       'status' => $this->controller->getStatus(),
                       'area_id' => $publication['id']
                    ),
                    $this->controller->BeAuthJwt->getUser()
                );
    }

    /**
     * Check if $objectId and its parents are accessible for authorized user.
     * 'Accessible' is defined in self::isObjectAccessible()
     *
     * If check fails it throws a bad request exception
     *
     * @throws BeditaBadRequesException
     * @param int|array $objectId the object id or an array of object ids
     * @return void
     */
    public function checkObjectAccess($objectId) {
        if (!is_array($objectId)) {
            $objectId = array($objectId);
        }
        foreach ($objectId as $id) {
            if (!$this->isObjectAccessible($id)) {
                throw new BeditaBadRequestException(
                    'Object ' . $id . ' or one of its parents is forbidden to user'
                );
            }
        }
    }

    /**
     * Return true if the relation $name is valid for $objectType
     *
     * @param string $name the relation name
     * @param string|int $objectType the object type name or id
     * @return boolean
     */
    public function isRelationValid($name, $objectType) {
        $isValid = false;
        if (is_numeric($objectType)) {
            $objectType = Configure::read('objectTypes.' . $objectType . '.name');
        }
        $objectRelation = ClassRegistry::init('ObjectRelation');
        return $objectRelation->isValid($name, $objectType);
    }

    /**
     * Check if an array of relations is valid
     *
     * The $relations array has to be in the form
     * ```
     * array(
     *     'attach' => array(
     *         array(
     *             'related_id' => 1,
     *             ...
     *         ),
     *         array(...)
     *     ),
     *     'seealso' => array(...)
     * )
     * ```
     *
     * If $objectType is passed then all relations are tested against that object type
     * All object ids inside relation are tested
     *
     * If check fails it throws a bad request exception
     *
     * @throws BeditaBadRequesException
     * @param array $relations the array of relations
     * @param string|int $objectType an object type name or id on which test all relations
     * @return void
     */
    public function checkRelations(array $relations, $objectType = null) {
        $beObject = ClassRegistry::init('BEObject');
        if (is_numeric($objectType)) {
            $objectType = Configure::read('objectTypes.' . $objectType . '.name');
        }
        foreach ($relations as $name => $data) {
            if ($objectType) {
                if (!$this->isRelationValid($name, $objectType)) {
                    throw new BeditaBadRequestException('Invalid relation ' . $name . ' for object type ' . $objectType);
                }
            }

            foreach ($data as $relData) {
                if (empty($relData['related_id'])) {
                    throw new BeditaBadRequestException('Missing related_id in relation data');
                }
                $relatedObjectType = $beObject->findObjectTypeId($relData['related_id']);
                if (!$this->isRelationValid($name, $relatedObjectType)) {
                    throw new BeditaBadRequestException('Invalid relation: ' . $name . ' for object type ' . $relatedObjectType);
                }
                if (!$this->isObjectReachable($relData['related_id'])) {
                    throw new BeditaBadRequestException('Invalid Relation: ' . $relData['related_id'] . ' is unreachable');
                }
            }
        }
    }

    /**
     * Check if an array of category names is valid for an object type id
     *
     * @throws BeditaBadRequesException
     * @param array $tags a list of category names
     * @param int $objectTypeId the object_type_id
     * @return void
     */
    public function checkCategories(array $categories, $objectTypeId = null) {
        $categoryCount = ClassRegistry::init('Category')->find('count', array(
            'conditions' => array(
                'name' => $categories,
                'object_type_id' => $objectTypeId,
                'status' => $this->controller->getStatus()
            )
        ));
        if ($categoryCount != count($categories)) {
            throw new BeditaBadRequestException('Some category does not exist. Categories must exist.');
        }
    }

    /**
     * Check if an array of tag names is valid.
     *
     * @throws BeditaBadRequestException
     * @param array $tags a list of tag names
     * @return void
     */
    public function checkTags(array $tags) {
        try {
            $this->checkCategories($tags);
        } catch (BeditaBadRequestException $ex) {
            throw new BeditaBadRequestException('Some tag does not exist. Tags must exist.');
        }
    }

    /**
     * Check if $date is in the right $format
     * If test passes it returns the DateTime object else it throws a BeditaBadRequestException
     *
     * @throws BeditaBadRequestException
     * @param string $date the date string to check
     * @param string $format the format against test $date (default ISO 8601)
     * @return DateTime
     */
    public function checkDate($date, $format = DateTime::ISO8601) {
        $dateTime = DateTime::createFromFormat($format, $date);
        if (!$dateTime) {
            $formatName = ($format == DateTime::ISO8601) ? 'ISO 8601' : $format;
            throw new BeditaBadRequestException($date . ' has to be in valid ' . $formatName . ' format');
        }
        return $dateTime;
    }

    /**
     * Check if $dateItems contains item with allowed and valid fields
     * $dateItems is an array as
     *
     * ```
     * array(
     *     0 => array(
     *         'start_date' => '2015-07-08T15:00:35+0200',
     *         'end_date' => '2015-08-08T15:00:35+0200',
     *         'params' => array(
     *             'days' => array()
     *         )
     *     )
     * )
     * ```
     *
     * If $objectId is passed and 'id' is present in some date items then check if it's valid for $objectId
     *
     * @param array $dateItems
     * @param int $objectId
     * @return void
     */
    public function checkDateItems(array $dateItems, $objectId = null) {
        $validFields = array('start_date', 'end_date', 'params');
        if (!empty($objectId)) {
            $validFields[] = 'id';
            $dateItemModel = ClassRegistry::init('DateItem');
        }
        foreach ($dateItems as $item) {
            foreach ($item as $field => $value) {
                if (!in_array($field, $validFields)) {
                    throw new BeditaBadRequesException('date_items: ' . $field . ' is not valid');
                }
                // check if id exists and corresponds to $objectId
                if ($field == 'id') {
                    $count = $dateItemModel->find('count', array(
                        'conditions' => array(
                            'id' => $value,
                            'object_id' =>$objectId
                        )
                    ));
                    if (empty($count)) {
                        throw new BeditaBadRequestException('date_items: ' . $field . '=' . $value .' is not valid');
                    }
                } elseif ($field == 'start_date' || $field == 'end_date') {
                    if (!empty($value)) {
                        $this->checkDate($value);
                    } elseif ($value !== null) {
                        throw new BeditaBadRequestException('date_items: ' . $field . ' has to be a valid date or null');
                    }
                } elseif ($field == 'params') {
                    $validateParams = true;
                    if ($value != null && !is_array($value)) {
                        $validateParams = false;
                    } elseif (is_array($value)) {
                        if (count(array_keys($value)) > 1 || !array_key_exists('days', $value)) {
                            $validateParams = false;
                        }
                    }
                    if (!$validateParams) {
                        throw new BeditaBadRequestException('date_items: ' . $field . ' has to be an object with just days key or null');
                    }
                }
            }
        }
    }

}

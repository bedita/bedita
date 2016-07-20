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
     * The supported query string parameters names for every endpoint.
     *
     * @see ApiBaseController::$defaultAllowedUrlParams to the right format
     * @var array
     */
    private $allowedUrlParams = array(
        '__all' => array()
    );

    /**
     * Initialize component (called before Controller::beforeFilter())
     *
     * @param Controller $controller
     * @return void
     */
    public function initialize(Controller $controller, array $settings = array()) {
        $this->controller = &$controller;
        $this->_set($settings);
    }

    /**
     * Startup component (called after Controller::beforeFilter())
     *
     * @param Controller $controller
     * @return void
     */
    public function startup(Controller $controller) {
        $validateConf = Configure::read('api.validation');
        if (!empty($validateConf['writableObjects'])) {
            $this->writableObjects = $validateConf['writableObjects'];
        }
        if (!empty($validateConf['allowedUrlParams'])) {
            $this->registerAllowedUrlParams($validateConf['allowedUrlParams']);
        }
    }

    /**
     * Check if url query string names of the request are valid for an endpoint
     *
     * @throws BeditaBadRequestException
     * @param string $endpoint the endpoint to check
     * @return void
     */
    public function checkUrlParams($endpoint) {
        if (!$this->isUrlParamsValid($endpoint)) {
            $validStringNames = !empty($this->allowedUrlParams[$endpoint]) ? $this->allowedUrlParams[$endpoint] : $this->allowedUrlParams['__all'];
            $endpointString = '';
            if (strpos($endpoint, '_') !== 0) {
                $endpointString = ' for /' . $endpoint;
            }
            throw new BeditaBadRequestException(
                'Url query string is not valid. Valid names' . $endpointString . ' are: ' . implode(', ', $validStringNames)
            );
        }
    }

    /**
     * Return true if url query string is valid for an endpoint, false otherwise
     * All allowed url params are valid for GET requests but '__all' values that are valid for all request types
     *
     * @param string $endpoint
     * @return boolean
     */
    public function isUrlParamsValid($endpoint) {
        $requestMethod = $this->controller->getRequestMethod();
        $queryStrings = $this->controller->params['url'];
        array_shift($queryStrings);
        if (!empty($queryStrings)) {
            foreach ($queryStrings as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $queryStrings[$key . '[' . $k . ']'] = $v;
                    }
                    unset($queryStrings[$key]);
                }
            }

            if ($requestMethod == 'get' && !empty($this->allowedUrlParams[$endpoint])) {
                $validStringNames = $this->allowedUrlParams[$endpoint];
            } else {
                $validStringNames = $this->allowedUrlParams['__all'];
            }

            $notValid = array_diff(array_keys($queryStrings), $validStringNames);
            if ($notValid) {
                return false;
            }
        }
        return true;
    }

    /**
     * Register an array of query string names in self::$allowedUrlParams
     * The array has to be divided by endpoint i.e.
     *
     * ```
     * array(
     *     'endpoint_1' => array('string_one', 'string_two', ...),
     *     'endpoint_2' => array(...)
     * )
     * ```
     *
     * @param array $stringNames
     * @param boolean $merge if $stringNames has to be merged to exisiting self::$allowedUrlParams
     * @return array
     */
    public function registerAllowedUrlParams(array $stringNames, $merge = true) {
        if (!$merge) {
            $this->allowedUrlParams = $stringNames;
        } else {
            // assure to analyze first special names starting with '_'
            ksort($stringNames);
            foreach ($stringNames as $endpoint => $names) {
                $this->setAllowedUrlParams($endpoint, $names, $merge);
            }
        }
        return $this->allowedUrlParams;
    }

    /**
     * Return the url query string names valid
     * Passing the endpoint the list is filtered by it
     *
     * @param string $endpoint the endpoint
     * @return array
     */
    public function getAllowedUrlParams($endpoint = null) {
        return !empty($this->allowedUrlParams[$endpoint]) ? $this->allowedUrlParams[$endpoint] : $this->allowedUrlParams;
    }

    /**
     * Set new valid url query string names
     *
     * @param string $endpoint the endpoint to modify
     * @param string|array $names the query string names to add
     * @param boolean $merge if the names have to be added or have to replace the old one
     */
    public function setAllowedUrlParams($endpoint, $names, $merge = true) {
        if (!is_array($names)) {
            $names = array($names);
        }
        if (!isset($this->allowedUrlParams[$endpoint])) {
            if (strpos($endpoint, '_') !== 0) {
                $names = array_merge($names, $this->allowedUrlParams['__all']);
            }
            $merge = false;
        }
        $namesToAdd = array();
        foreach ($names as $k => $n) {
            if (strpos($n, '_') === 0) {
                if (!empty($this->allowedUrlParams[$n])) {
                    $namesToAdd = array_merge($namesToAdd, $this->allowedUrlParams[$n]);
                }
            } else {
                $namesToAdd[] = $n;
            }
        }
        $this->allowedUrlParams[$endpoint] = ($merge) ? array_merge($this->allowedUrlParams[$endpoint], $namesToAdd) : $namesToAdd;
        sort($this->allowedUrlParams[$endpoint]);
        return $this->allowedUrlParams[$endpoint];
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
        $objectId = null;
        // validate object type
        if (!empty($object['id'])) {
            $objectTypeId = $beObject->findObjectTypeId($object['id']);
            if ($objectTypeId != $object['object_type_id']) {
                throw new BeditaBadRequestException(
                    'Object type mismatch: object with id=' . $object['id'] . ' has not object_type_id=' . $object['object_type_id']
                );
            }
            $objectId = $object['id'];
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
            $this->checkParents($object['parents']);
        } else {
            if ($objectType == 'section' && $objectId == null) {
                throw new BeditaBadRequestException($objectType . ' must have one and only one parent');
            }
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
            $this->checkGeoTags($object['geo_tags'], $objectId);
        }
        if (!empty($object['date_items'])) {
            $modelName = Configure::read('objectTypes.' . $object['object_type_id'] . '.model');
            $objectModel = ClassRegistry::init($modelName);
            $associations = $objectModel->getAssociated();
            if (!array_key_exists('DateItem', $associations)) {
                throw new BeditaBadRequestException('date_items is invalid for ' . $objectType);
            }
            $this->checkDateItems($object['date_items'], $objectId);
        }
        if (!empty($object['custom_properties'])) {
            $this->checkCustomProperties($object['custom_properties'], $object['object_type_id']);
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
                throw new BeditaNotFoundException('Object ' . $objectId . ' not found');
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
            // first check permission on object itself
            if (!$this->isObjectAccessible($objectId, false)) {
                return false;
            }
            // check if it's on tree and also its parents are accessible
            if ($isOnTree && $this->areObjectParentsAccessible($objectId)) {
                return true;
            }
            // if not, check if at least a related object is accessible
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
     * Return true if $objectId is accessible for authorized user, false otherwise.
     * 'Accessible' means without 'frontend_access_with_block' permission set for groups that the user doesn't belong.
     * When $parentsCheck is true permission on them is also checked
     * If object hasn't parents is not accessible
     *
     * @param int $objectId the object id
     * @param boolean $parentsCheck if parents must be checked (default true)
     * @return boolean
     */
    public function isObjectAccessible($objectId, $parentsCheck = true) {
        $permission = ClassRegistry::init('Permission');
        $user = $this->controller->ApiAuth->getUser();
        // if object itself is forbidden to user return false without any other check
        $access = $permission->frontendAccess($objectId, $user);
        if ($access == 'denied') {
            return false;
        }
        if (!$parentsCheck) {
            return true;
        }
        return $this->areObjectParentsAccessible($objectId);
    }

    /**
     * Return true if $objectId parents are accessible for authorized user, false otherwise.
     * 'Accessible' means without 'frontend_access_with_block' permission set for groups that the user doesn't belong.
     * 
     * @param int $objectId the object id
     * @return boolean
     */
    public function areObjectParentsAccessible($objectId) {
        $permission = ClassRegistry::init('Permission');
        $user = $this->controller->ApiAuth->getUser();
        $userGroups = !empty($user['groupsIds']) ? $user['groupsIds'] : array();
        $publication = $this->controller->getPublication();
        return $permission->objectParentsAccessible($objectId,
                    array(
                       'status' => $this->controller->getStatus(),
                       'area_id' => $publication['id']
                    ),
                    $userGroups
                );
    }

    /**
     * Check if $parentsId are valid parents for the saving object
     *
     * If check fails it throws a bad request exception
     *
     * @throws BeditaBadRequestException
     * @param int|array $objectId the object id or an array of object ids
     * @return void
     */
    public function checkParents($parentsId)
    {
        if (!is_array($parentsId)) {
            $parentsId = array($parentsId);
        }
        $branches = array(Configure::read('objectTypes.area.id'), Configure::read('objectTypes.section.id'));
        foreach ($parentsId as $parentId) {
            $beObject = ClassRegistry::init('BEObject');
            $parentTypeId = $beObject->findObjectTypeId($parentId);
            if (!in_array($parentTypeId, $branches)) {
                throw new BeditaBadRequestException('objects can only have parents of: area or section');
            }
        }
    }

    /**
     * Check if $objectId and its parents are accessible for authorized user.
     * 'Accessible' is defined in self::isObjectAccessible()
     *
     * If check fails it throws a bad request exception
     *
     * @throws BeditaBadRequestException
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
        $excludeRelationsFrontend = Configure::read('excludeRelationsFrontend');
        if ($excludeRelationsFrontend && in_array($name, $excludeRelationsFrontend)) {
            return false;
        }

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
     * @throws BeditaBadRequestException
     * @param array $relations the array of relations
     * @param string|int $objectType an object type name or id on which test all relations
     * @return void
     */
    public function checkRelations(array $relations, $objectType = null) {
        $beObject = ClassRegistry::init('BEObject');
        if (is_numeric($objectType)) {
            $objectType = Configure::read('objectTypes.' . $objectType . '.name');
        }
        $objectRelation = ClassRegistry::init('ObjectRelation');
        foreach ($relations as $name => $data) {
            if ($objectType) {
                if (!$this->isRelationValid($name, $objectType)) {
                    throw new BeditaBadRequestException('Invalid relation ' . $name . ' for object type ' . $objectType);
                }
            }

            $inverseName = $objectRelation->inverseOf($name);
            foreach ($data as $relData) {
                if (empty($relData['related_id'])) {
                    throw new BeditaBadRequestException('Missing related_id in relation data');
                }
                $this->checkPositiveInteger($relData['related_id'], true);
                $relatedObjectType = $beObject->findObjectTypeId($relData['related_id']);
                if (!$this->isRelationValid($inverseName, $relatedObjectType)) {
                    throw new BeditaBadRequestException('Invalid relation: ' . $name . ' for object type ' . $relatedObjectType);
                }
                if (!$this->isObjectReachable($relData['related_id'])) {
                    throw new BeditaBadRequestException('Invalid Relation: ' . $relData['related_id'] . ' is unreachable');
                }
            }
        }
    }

    /**
     * Check embed relations requested.
     * $relationsData must be in the form of 'relation_name' => number_requested,
     * for example
     *
     * ```
     * array(
     *     'attach' => 3,
     *     'seealso' => 1
     * )
     * ```
     *
     * It checks that:
     *
     * - the number requested is positive integer
     * - the total number of objects and relations embedded per page is less than max size
     *
     * @throws BeditaBadRequestException
     * @param array $relationsData array of relations info
     * @param int $pageSize the page size
     * @param int $maxSize the max results allowed
     * @return void
     */
    public function checkEmbedRelations(array $relationsData, $pageSize, $maxSize) {
        // count main object too
        $objAndRel = 1;
        foreach ($relationsData as $relName => $num) {
            $this->checkPositiveInteger($num, true);
            $objAndRel += $num;
        }
        if ($objAndRel * $pageSize > $maxSize) {
            throw new BeditaBadRequestException('Too many objects requested');
        }
    }

    /**
     * Return true if $test is a positive integer, false otherwise
     *
     * @param mixed $test the type to test
     * @return boolean
     */
    public function isPositiveInteger($test) {
        return is_int($test) && $test > 0;
    }

    /**
     * Check if $num is a positive integer
     *
     * @throws BeditaBadRequestException
     * @param mixed $test the type to test
     * @param boolean $cast set to true to trying to cast $test to int before check it
     * @return void
     */
    public function checkPositiveInteger($test, $cast = false) {
        if ($cast) {
            $casted = (int) $test;
            if (!is_numeric($test) || $casted != $test) {
                throw new BeditaBadRequestException($test . ' must be an integer');
            }
            $test = $casted;
        }
        if (!$this->isPositiveInteger($test)) {
            throw new BeditaBadRequestException($test . ' must be a positive integer, ' . gettype($test) . ' is given');
        }
    }

    /**
     * Check if an array of (possible) children is valid for a parent id
     *
     * The $children array has to be in the form
     * ```
     * array(
     *     array(
     *         'child_id' => 1,
     *         'priority' => 1
     *     ),
     *     array(...)
     * )
     * ```
     *
     * @throws BeditaBadRequestException
     * @param array $children array of chidlren data
     * @param int $parentId the parent object id
     * @return void
     */
    public function checkChildren(array $children, $parentId) {
        $objectTypeId = ClassRegistry::init('BEObject')->findObjectTypeId($parentId);
        $objectType = Configure::read('objectTypes.' . $objectTypeId . '.name');
        if ($objectType != 'section' && $objectType != 'area') {
            throw new BeditaBadRequestException($objectType . ' can not have children');
        }
        foreach ($children as $key => $child) {
            if (empty($child['child_id'])) {
                throw new BeditaBadRequestException('Missing child_id in children data');
            }
            $this->checkPositiveInteger($child['child_id'], true);
            if (array_key_exists('priority', $child)) {
                $this->checkPositiveInteger($child['priority'], true);
            }
            if (!$this->isObjectReachable($child['child_id'])) {
                throw new BeditaBadRequestException($child['child_id'] . ' can not be children of ' . $parentId);
            }
        }
    }

    /**
     * Check if an array of category names is valid for an object type id
     *
     * @throws BeditaBadRequestException
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
     * Check if $date is in the right $format and if it's a valid date
     * If test passes it returns the DateTime object else it throws a BeditaBadRequestException
     *
     * Default $format tested are the following ISO-8601 formats:
     * - 2005-08-15T15:52:01+02:00 (DateTime::ATOM)
     * - 2005-08-15T13:52:01.467Z (js Date().toISOString())
     *
     * @throws BeditaBadRequestException
     * @param string $date the date string to check
     * @param string $format the format against test $date (default ISO-8601)
     * @return DateTime
     */
    public function checkDate($date, $format = DateTime::ATOM) {
        $defaultTimezone = $timezone = new DateTimeZone(date_default_timezone_get());
        $zPattern = '/(.+)\.\d{3}Z$/';
        // if ISO-8601 and it's in js Date().toISOString() format
        if ($format == DateTime::ATOM && preg_match($zPattern, $date, $match)) {
            $format = 'Y-m-d\TH:i:s';
            $date = $match[1];
            $timezone = new DateTimeZone('UTC');
        }
        $dateTime = DateTime::createFromFormat($format, $date, $timezone);
        if (!$dateTime) {
            $formatName = ($format == DateTime::ATOM) ? 'ISO-8601' : $format;
            throw new BeditaBadRequestException($date . ' has to be in valid ' . $formatName . ' format');
        }
        // validate that formatted date is equal to start $date string
        if ($date != $dateTime->format($format)) {
            throw new BeditaBadRequestException($date . ' is not a valid date');
        }

        // if $dateTime was created using UTC time zone and it is different to default then change it
        if ($dateTime->getTimezone()->getName() == 'UTC' && $defaultTimezone->getName() != 'UTC') {
            $dateTime->setTimezone($defaultTimezone);
        }
        return $dateTime;
    }

    /**
     * Check if $dateItems contains item with allowed and valid fields
     * $dateItems has to be an array as
     *
     * ```
     * array(
     *     0 => array(
     *         'start_date' => '2015-07-08T15:00:35+0200',
     *         'end_date' => '2015-08-08T15:00:35+0200',
     *         'days' => array(0, 2) // integer values from 0 (Sunday) to 6 (Saturday)
     *     ),
     *     1 => array()
     * )
     * ```
     *
     * If $objectId is passed and 'id' is present in some date items then check if it's valid for $objectId
     *
     * @throws BeditaBadRequestException
     * @param array $dateItems
     * @param int $objectId
     * @return void
     */
    public function checkDateItems(array $dateItems, $objectId = null) {
        $validFields = array('start_date', 'end_date', 'days');
        if (!empty($objectId)) {
            $validFields[] = 'id';
            $dateItemModel = ClassRegistry::init('DateItem');
        }
        foreach ($dateItems as $item) {
            if (!is_array($item)) {
                throw new BeditaBadRequestException('date_items: malformed data');
            }
            foreach ($item as $field => $value) {
                if (!in_array($field, $validFields)) {
                    throw new BeditaBadRequestException('date_items: ' . $field . ' is not valid');
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
                } elseif ($field == 'days') {
                    $validateDays = true;
                    $test = array_filter($value, function($v) {
                        return (is_int($v) && $v >= 0 && $v <= 6);
                    });
                    if ($value !== $test) {
                        throw new BeditaBadRequestException(
                            'date_items: ' . $field . ' has to be an array of max seven positive integer values [0-6]'
                        );
                    }
                }
            }
        }
    }

    /**
     * Check if $geoTags contains item with allowed and valid fields
     * $geoTags has to be an array as
     *
     * ```
     * array(
     *     0 => array(
     *         'latitude' => 43.503815,
     *         'longitude' => '10.470861',
     *         'address' => 'lorem ipsum',
     *         'title' => 'title geo tag'
     *     )
     * )
     * ```
     *
     * Since in backend only one GeoTag is handled the array has to be contain only one geotag data
     *
     * If $objectId is passed and 'id' is present in some date items then check if it's valid for $objectId
     *
     * @throws BeditaBadRequestException
     * @param array $dateItems
     * @param int $objectId
     * @return void
     */
    public function checkGeoTags(array $geoTags, $objectId = null) {
        $validFields = array('latitude', 'longitude', 'address', 'title');
        if (!empty($objectId)) {
            $validFields[] = 'id';
            $geoTagModel = ClassRegistry::init('GeoTag');
        }
        $countItem = 1;
        foreach ($geoTags as $item) {
            if ($countItem++ > 1) {
                throw new BeditaBadRequestException('geo_tags: just one geotag can be saved');
            }
            if (!is_array($item)) {
                throw new BeditaBadRequestException('geo_tags: malformed data');
            }
            foreach ($item as $field => $value) {
                if (!in_array($field, $validFields)) {
                    throw new BeditaBadRequestException('geo_tags: ' . $field . ' is not valid');
                }
                // check if id exists and corresponds to $objectId
                if ($field == 'id') {
                    $count = $geoTagModel->find('count', array(
                        'conditions' => array(
                            'id' => $value,
                            'object_id' =>$objectId
                        )
                    ));
                    if (empty($count)) {
                        throw new BeditaBadRequestException('geo_tags: ' . $field . '=' . $value .' is not valid');
                    }
                }
            }
        }
    }

    /**
     * Check if custom properties are valid
     * The $customProperties array has to be in the form
     *
     * ```
     * array(
     *     'custom_prop_name_1' => 'value1',
     *     'custom_prop_name_2' => 'value2',
     *     'custom_prop_name_3' => array('value3', 'value4') // multiple choice
     * )
     * ```
     *
     * @param array $customProperties the custom properties to validate
     * @param int|string $objectTypeId the object type id or name
     * @return void
     */
    public function checkCustomProperties(array $customProperties, $objectTypeId) {
        if (!is_numeric($objectTypeId)) {
            $objectTypeId = Configure::read('objectTypes.' . $objectTypeId . '.id');
        }
        $property = ClassRegistry::init('Property');
        $properties = $property->find('all', array(
            'fields' => array('name', 'property_type', 'multiple_choice'),
            'conditions' => array(
                'name' => array_keys($customProperties),
                'object_type_id' => $objectTypeId
            ),
            'contain' => array('PropertyOption')
        ));
        $properties = Set::combine($properties, '{n}.name', '{n}');

        // check options closure
        $checkOptions = function(array $valuesToCheck, array $prop) {
            $availableOptions = Set::extract('/PropertyOption/property_option', $prop);
            $valuesForbidden = array_diff($valuesToCheck, $availableOptions);
            if (!empty($valuesForbidden)) {
                throw new BeditaBadRequestException(
                    'Custom property ' . $prop['name'] . ' values allowed are: ' . implode(',', $availableOptions)
                );
            }
        };

        foreach ($customProperties as $name => $value) {
            if (empty($properties[$name])) {
                throw new BeditaBadRequestException('Custom property ' . $name . ' not exists');
            }
            // null needsto delete custom property
            if ($value !== null) {
                $propData = $properties[$name];
                if (is_array($value)) {
                    if ($propData['property_type'] != 'options' || $propData['multiple_choice'] == 0) {
                        throw new BeditaBadRequestException('Custom property ' . $name . ' does not support multiple values');
                    }
                    // check if property values are options valid
                    $checkOptions($value, $propData);
                } else {
                    if ($propData['property_type'] == 'date') {
                        $this->checkDate($value);
                    } elseif ($propData['property_type'] == 'number') {
                        if (!is_numeric($value)) {
                            throw new BeditaBadRequestException('Custom property ' . $name . ' must be numeric');
                        }
                    } elseif ($propData['property_type'] == 'options') {
                        $checkOptions(array($value), $propData);
                    }
                }
            }
        }
    }

    /**
     * It says if an `$objectType` supports upload.
     *
     * To be accepted as uploadable:
     *
     * - it must be writable
     * - the related model must extends 'BeditaSimpleStreamModel', 'BeditaStreamModel' or implements an `apiUpload()` method
     *
     * @throws BeditaBadRequestException
     * @param string $objectType The object type 
     * @return void
     */
    public function isObjectTypeUploadable($objectType) {
        if (!$this->isObjectTypeWritable($objectType)) {
            return false;
        }

        $objectTypeClass = Configure::read('objectTypes.' . $objectType . '.model');
        $model = ClassRegistry::init($objectTypeClass);
        if (empty($model)) {
            return false;
        }

        $parentClass = get_parent_class($model);
        $validParentClasses = array('BeditaSimpleStreamModel', 'BeditaStreamModel');
        if (!in_array($parentClass, $validParentClasses) && !method_exists($model, 'apiUpload')) {
            return false;
        }

        return true;
    }
}

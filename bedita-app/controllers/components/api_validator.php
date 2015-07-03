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
     * Initialize function
     *
     * @param Controller $controller
     * @return void
     */
    public function initialize(Controller $controller, array $settings = array()) {
        $this->controller = $controller;
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
        // validate object_type_id
        if (!empty($object['id'])) {
            $objectTypeId = $beObject->findObjectTypeId($object['id']);
            if ($objectTypeId != $object['object_type_id']) {
                throw new BeditaBadRequestException('Object type mismatch');
            }
        }

        // prepare list of forbidden fields to avoid automatic save
        $forbiddenFields = array_merge(
            $beObject->hasOne,
            $beObject->belongsTo,
            $beObject->hasMany,
            $beObject->hasAndBelongsToMany,
            array('DateItem', 'destination')
        );
        foreach ($object as $key => $value) {
            if (in_array($key, $forbiddenFields)) {
                throw new BeditaBadRequestException($key . ' field is forbidden');
            }
        }

        $objectType = Configure::read('objectTypes.' . $object['object_type_id'] . '.name');

        if (!empty($object['relations'])) {
            $this->checkRelations($object['relations'], $objectType);
        }
        if (!empty($object['parents'])) {
            $branches = array(
                Configure::read('objectTypes.area.id'),
                Configure::read('objectTypes.section.id')
            );
            if (in_array($object['object_type_id'], $branches) && count($object['parents']) > 1) {
                throw new BeditaBadRequestException($objectType . ' can have only one parent');
            }
            $this->checkObjectAccess($object['parents']);
        }
        if (!empty($object['categories'])) {

        }
        if (!empty($object['tags'])) {

        }
        if (!empty($object['geo_tags'])) {

        }
        if (!empty($object['date_items'])) {

        }
    }

    /**
     * Return true of object $objectId is reachable, false otherwise.
     * 'Reachable' means that object is on publication tree or have at least a related object on tree
     * If $checkPermission is true an additional check on 'frontend_acccess_with_block' permission is done
     *
     * @param int $objectId the object id
     * @param boolean $checkPermissions if permission has to be checked, default true
     * @return boolean
     */
    public function isObjectReachable($objectId, $checkPermissions = true) {
        // check position on tree and permission
        if ($checkPermissions) {
            if (!$this->isObjectAccessible($objectId)) {
                if (!$this->hasRelatedObjectsAccessible($objectId)) {
                    return false;
                }
            }
            return true;
        // check only position on tree
        } else {
            $tree = ClassRegistry::init('Tree');
            $publication = $this->controller->getPublication();
            $isOnTree = $tree->isOnTree($objectId, $publication['id'], $this->controller->getStatus());
            $isRelatedObjectsOnTree = $tree->relatedObjectsOnTree($objectId, array(
                'area_id' => $publication['id'],
                'status' => $this->controller->getStatus(),
                'count' => true
            ));
            return $isOnTree || $isRelatedObjectsOnTree;
        }
    }

    /**
     * Return true if at least an object related to $objectId is accessible, false otherwise
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
            }
        }
        return $result;
    }

    /**
     * Return true if object $id and its parents are accessible for authorized user, false otherwise.
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
     * Check if object $id and its parents are accessible for authorized user.
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
                    'Object ' . $id . ' or one of its parents is forbidden to user ' . $this->controller->BeAuthJwt->userid()
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

}

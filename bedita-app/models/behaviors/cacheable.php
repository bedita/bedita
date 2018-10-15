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

App::import('Lib', 'BeLib');

/**
 * CacheableBehavior class
 *
 * Used from BEObject to clean cached data when save or delete object
 * Data are meant to be cached through BeObjectCache class in libs
 *
 */
class CacheableBehavior extends ModelBehavior {

    /**
     * If the behavior is active or not
     * @var boolean
     */
    private $on = false;

    /**
     * If object cache is activate by objectCakeCache config param
     * contain instance of BeObjectCache class in libs
     *
     * @var BeObjectCache
     */
    private $BeObjectCache = null;

    private $objectsToClean = array();

    public function setup(&$model, $settings = array()) {
        if (Configure::read('objectCakeCache')) {
            $this->on = true;
            $this->BeObjectCache = BeLib::getObject('BeObjectCache');
        }
    }

    /**
     * Is CacheableBehavior on?
     *
     * @param Model $model
     * @return boolean
     */
    public function isCacheableOn(&$model) {
        return $this->on;
    }

    /**
     * Reset self::objectsToClean
     *
     * @param Model $model
     */
    public function resetObjectsToClean(&$model) {
        $this->objectsToClean = array();
    }

    /**
     * Set an array of object id to clean from cache starting from an object id
     *
     * @param Model $model
     * @param integer $objectId
     * @param array $excludeIds object ids to not clean
     * @see CacheableBehavior::getObjectsToCleanById() to see which object ids are set
     */
    public function setObjectsToClean(&$model, $objectId, array $excludeIds = array()) {
        if (!$this->on) {
            return;
        }
        $this->resetObjectsToClean($model);
        $idsToAdd = $this->getObjectsToCleanById($model, $objectId, $excludeIds);
        $this->addObjectsToClean($model, $idsToAdd);
    }

    /**
     * Return an array of object ids to clean from cache starting from $objectId
     *
     * Objects to clean:
     *  - object itself
     *  - parents
     *  - related objects
     *
     * @param Model $model
     * @param integer $objectId
     * @param array $excludeIds object ids to not clean
     */
    public function getObjectsToCleanById(&$model, $objectId, array $excludeIds = array()) {
        // get parents to clean
        $tree = ClassRegistry::init('Tree');
        $treeConditions = array('id' => $objectId);
        if (!empty($excludeIds)) {
            $treeConditions['NOT'] = array('parent_id' => $excludeIds);
        }
        $parents = $tree->find('list', array(
            'fields' => array('parent_id'),
            'conditions' => $treeConditions
        ));

        // get related object to clean
        $objectRelation = ClassRegistry::init('ObjectRelation');
        $relConditions = array('id' => $objectId);
        if (!empty($excludeIds)) {
            $relConditions['NOT'] = array('object_id' => $excludeIds);
        }
        $relatedObjects = $objectRelation->find('list', array(
            'fields' => array('object_id', 'id'),
            'conditions' => $relConditions
        ));
        $relatedObjects = array_keys($relatedObjects);

        $objectIdsToClean = array_merge(array($objectId), $parents, $relatedObjects);
        return $objectIdsToClean;
    }

    /**
     * Add object ids to delete from cache to self::objectsToClean array
     *
     * @param Model $model
     * @param array|int $ids array of ids or single id
     */
    public function addObjectsToClean(&$model, $ids = array()) {
        if (!$this->on) {
            return;
        }
        if (is_numeric($ids)) {
            $ids = array($ids);
        }
        if (!is_array($ids)) {
            return;
        }
        $this->objectsToClean = array_filter(
            array_values(
                array_unique(
                    array_merge($this->objectsToClean, $ids)
                )
            )
        );
    }

    /**
     * Get a list of IDs of all descendant sections.
     *
     * @param int $id Object ID.
     * @return array
     */
    protected function getDescendantSections($id) {
        $descendants = ClassRegistry::init('Tree')->find('all', array(
            'fields' => array('id'),
            'conditions' => array(
                'object_path LIKE' => '%/' . (int)$id . '/%',
            ),
            'joins' => array(
                array(
                    'table' => 'sections',
                    'alias' => 'Section',
                    'type' => 'INNER',
                    'conditions' => array('Section.id = Tree.id'),
                ),
            ),
        ));
        $descendants = Set::extract('/Tree/id', $descendants);

        return $descendants;
    }

    /**
     * Clear object cache
     *
     * If objectId is passed start from it to obtain all object ids to delete from cache
     * else use self::objectsToClean array that has to be build previously
     *
     * @param Model $model
     * @param integer $objectId
     * @param bool $reset
     */
    public function clearCache(&$model, $objectId, $reset = true, $descendants = true) {
        if ($objectId) {
            if ($reset) {
                $this->resetObjectsToClean($model);
            }

            $this->addObjectsToClean($model, $this->getObjectsToCleanById($model, $objectId));
        }

        foreach ($this->objectsToClean as $id) {
            $this->BeObjectCache->delete($id);
        }

        $notLeafs = array(Configure::read('objectTypes.area.id'), Configure::read('objectTypes.section.id'));
        if (empty($model->data['BEObject']['object_type_id']) || in_array($model->data['BEObject']['object_type_id'], $notLeafs)) {
            $this->BeObjectCache->deletePathCache($objectId, $descendants ? $this->getDescendantSections($objectId) : array());
        }

        if (!empty($this->objectsToClean)) {
            BeLib::eventManager()->trigger('ObjectCache.clear', array($this->objectsToClean));
        }
    }

    /**
     * Clear cache of an array of objects calculating from $objectIds
     *
     * @param Model $model
     * @param array $objectIds object ids from which calculate the objects to clean
     * @see CacheableBehavior::getObjectsToCleanById() to see which how object ids to clean are calculated
     */
    public function clearCacheByIds(&$model, array $objectIds) {
        if (empty($objectIds) || !$this->on) {
            return;
        }
        $this->resetObjectsToClean($model);
        $allObjectsToClean = array();
        foreach ($objectIds as $objectId) {
            $allObjectsToClean = array_merge($allObjectsToClean, $this->getObjectsToCleanById($model, $objectId));
        }
        $this->addObjectsToClean($model, $allObjectsToClean);
        foreach ($objectIds as $objectId) {
            $this->clearCache($model, $objectId, false);
        }
    }

    /**
     * beforeSave callback
     *
     * Prepare object ids that must to be cleaned from cache
     *
     * @param Model $model
     * @return boolean
     */
    public function beforeSave(&$model) {
        if ($this->on) {
            $data = $model->data[$model->name];

            if (!empty($data['id'])) {
                $this->BeObjectCache->deleteNicknameCache($model->getNicknameFromId($data['id']));
            }

            $relatedIds = array();
            if (!empty($data['RelatedObject'])) {
                foreach ($data['RelatedObject'] as $rel => $value) {
                    $relatedIds = array_merge($relatedIds, array_keys($value));
                }
                // get unique values and filter falsy values
                $relatedIds = array_filter(array_unique($relatedIds));
            }

            $treeIds = (!empty($data['destination'])) ? $data['destination'] : array();
            $excludeIdsFromQuery = array_merge($treeIds, $relatedIds);

            // prepare objects to clean
            if (!empty($data['id'])) {
                $this->setObjectsToClean($model, $data['id'], $excludeIdsFromQuery);
            }
            $this->addObjectsToClean($model, $excludeIdsFromQuery);
        }
        return true;
    }

    /**
     * afterSave callback
     *
     * If object already exists then delete cached objects listed in self::objectsToClean
     *
     * @param Model $model
     * @param boolean $created
     */
    public function afterSave(&$model, $created) {
        // if it's an update remove cache
        if ($this->on) {
            $this->addObjectsToClean($model, $model->id);
            $this->clearCache($model, $model->id, false);
        }
    }

    /**
     * beforeDelete callback
     *
     * Prepare object ids that must to be cleaned from cache
     *
     * @param Model $model
     * @param boolean $cascade
     * @return boolean
     */
    public function beforeDelete(&$model, $cascade) {
        if ($this->on) {
            $this->BeObjectCache->deleteNicknameCache($model->getNicknameFromId($model->id));
            $this->setObjectsToClean($model, $model->id);
        }
        return true;
    }

    /**
     * afterDelete callback
     *
     * Delete cached objects listed in self::objectsToClean
     *
     * @param Model $model
     */
    public function afterDelete(&$model) {
        if ($this->on) {
            $this->clearCache($model, $model->id, false);
        }
    }

}

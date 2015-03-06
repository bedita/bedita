<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2014 ChannelWeb Srl, Chialab Srl
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
 * DeleteObjectBehavior class
 *
 * Delete object using dependence of foreign key.
 * Delete only the record of base table then database's referential integrity do the rest
 *
 */
class DeleteObjectBehavior extends ModelBehavior {

    /**
     * contain base table
     */
    public $config = array();

    public function setup(&$model, $config = array()) {
        $this->config[$model->name] = $config;
    }

    /**
     * Delete all associations, they will be re-established after deleting
     * Considering foreignKeys among tables, force deleting records on table 'objects'
     *
     * if specified delete related object too
     *
     * @param Model $model
     * @return boolean
     */
    public function beforeDelete(&$model) {
        // prepare objects to delete from cache
        if ($model->BEObject->isCacheableOn()) {
            $model->BEObject->setObjectsToClean($model->id);
        }

        $model->tmpAssociations = array();
        $model->tmpTable = $model->table;

        $associations = array('hasOne', 'hasMany', 'belongsTo', 'hasAndBelongsToMany');
        foreach ($associations as $association) {
            $model->tmpAssociations[$association] = $model->$association;
            $model->$association = array();
        }
        $configure = $this->config[$model->name];

        if (!empty($configure)) {
            if (is_string($configure)) {
                $model->table = $configure;
            } elseif (is_array($configure) && count($configure) == 1) {

                if (is_string(key($configure))) {

                    $model->table = key($configure);
                    if (!empty($configure[$model->table]['relatedObjects'])) {
                        $this->delRelatedObjects($configure[$model->table]['relatedObjects'], $model->id);
                    }

                } else {
                    $model->table = array_shift($configure);
                }
            }
        }

        $model->table = (isset($configure) && is_string($configure)) ? $configure : $model->table;

        // Delete object references on tree as well
        $Tree = ClassRegistry::init('Tree');
        $ok = true;
        if ($model instanceof BeditaCollectionModel) {
            $ok = $model->deleteCollection($model->id);
        } else {
            $parentIds = $Tree->getParents($model->id);
            foreach ($parentIds as $parentId) {
                $ok = $Tree->removeChild($model->id, $parentId) && $ok;
            }
        }
        if (!$ok) {
            throw new BeditaException(__('Error deleting tree references', true));
        }

        $st = ClassRegistry::init('SearchText');
        $st->removeObject($model->id);
        $this->deleteAnnotations($model->id);
        return true;
    }

    /**
     * Re-establish associations (insert associations)
     *
     */
    public function afterDelete(&$model) {
        if (!empty($model->tmpTable)) {
            $model->table = $model->tmpTable;
            unset($model->tmpTable);
        }
        if (!empty($model->tmpAssociations)) {
            // Re-establish associations
            foreach ($model->tmpAssociations as $association => $v) {
                $model->$association = $v;
            }
            unset($model->tmpAssociations);
        }

        // clear cache
        if ($model->BEObject->isCacheableOn()) {
            $model->BEObject->clearCache();
        }
    }

    /**
     * Delete related objects
     *
     * @param array $relations: array of relations type.
     *                          The object related to main object by a relation in $relations will be deleted
     * @param int $object_id: main object that has to be deleted
     */
    private function delRelatedObjects($relations, $object_id) {
        $o = ClassRegistry::init('BEObject') ;
        $res = $o->find('first', array(
            'contain' => array('RelatedObject'),
            'conditions' => array('BEObject.id' => $object_id)
        ));

        if (!empty($res['RelatedObject'])) {
            $conf = Configure::getInstance();
            foreach ($res['RelatedObject'] as $obj) {
                if (in_array($obj['switch'], $relations)) {
                    $modelClass = $o->getType($obj['object_id']);
                    $model = ClassRegistry::init($modelClass);
                    if (!$model->delete($obj['object_id'])) {
                        throw new BeditaException(__('Error deleting related object ', true), 'id: ' . $obj['object_id'] . ', switch: ' . $obj['switch']);
                    }
                }
            }
        }
    }

    /**
     * delete annotation objects like Comment, EditorNote, etc... related to main object
     *
     * @param int $object_id main object
     */
    private function deleteAnnotations($object_id) {
        // delete annotations
        $annotation = ClassRegistry::init('Annotation');
        $aList = $annotation->find('list', array(
            'fields' => array('id'),
            'conditions' => array('object_id' => $object_id)
        ));

        if (!empty($aList)) {
            $o = ClassRegistry::init('BEObject');
            foreach ($aList as $id) {
                $modelClass = $o->getType($id);
                $model = ClassRegistry::init($modelClass);
                if (!$model->delete($id)) {
                    throw new BeditaException(__('Error deleting annotation ' . $modelClass, true), 'id: ' . $id);
                }
            }
        }
    }

}

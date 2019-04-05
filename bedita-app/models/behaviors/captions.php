<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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
 * Captions behavior
 */
class CaptionsBehavior extends ModelBehavior {

    protected $config = array();

    function setup($model, $config = array()) {
    }

    /**
     * Load captions in model data.
     *
     * @param object $model The model
     * @param array $results Fetched models data
     * @param bool $primary Is primary
     * @return array
     */
    public function afterFind($model, $results, $primary)
    {
        foreach ($results as &$result) {
            if (!empty($result['id'])) {
                $result['captions'] = $this->getCaptions($result['id']);
            }
        }
        unset($result);

        return $results;
    }

    /**
     * Save captions after video has been saved.
     *
     * @param object $model The model
     * @param bool $created Is this a freshly created entity?
     * @return void
     */
    public function afterSave($model, $created)
    {
        if (isset($model->data[$model->alias]['captions'])) {
            $this->saveCaptions($model->id, $model->data[$model->alias]['captions']);
        }
    }

    /**
     * Return list of captions for a video.
     *
     * @param int $objectId Object ID.
     * @return array
     */
    protected function getCaptions($objectId)
    {
        $CaptionModel = ClassRegistry::init('Caption');
        $found = $CaptionModel->find('all', array(
            'conditions' => array(
                'object_id' => $objectId,
                'object_type_id' => Configure::read('objectTypes.caption.id'),
            ),
            'contain' => array('BEObject'),
        ));

        return $found;
    }

    /**
     * Save captions for a video.
     *
     * @param int $objectId Object ID.
     * @param array $data List of captions data.
     * @return void
     */
    protected function saveCaptions($objectId, array $data)
    {
        $CaptionModel = ClassRegistry::init('Caption');

        $kept = array();
        $data = array_filter(
            $data,
            function ($datum) {
                return !empty($datum['description']);
            }
        );
        foreach ($data as $datum) {
            if (empty($datum['id'])) {
                $CaptionModel->create();
            }

            $datum['object_id'] = $objectId;
            $CaptionModel->save($datum);

            $kept[] = $CaptionModel->id;
        }

        $conditions = array(
            'object_id' => $objectId,
            'object_type_id' => Configure::read('objectTypes.caption.id'),
        );
        if (!empty($kept)) {
            $conditions['NOT'] = array(
                'BEObject.id' => $kept,
            );
        }
        $toBeDeleted = $CaptionModel->find('all', array(
            'fields' => array('BEObject.id'),
            'conditions' => $conditions,
            'contain' => array('BEObject'),
        ));
        $toBeDeleted = Set::classicExtract($toBeDeleted, '{n}.BEObject.id');

        foreach ($toBeDeleted as $id) {
            $CaptionModel->delete($id);
        }
    }
}

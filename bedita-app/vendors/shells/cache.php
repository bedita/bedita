<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
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

App::import('Core', 'Controller');

require_once 'bedita_base.php';

/**
 * Default shell script for cache operations, mainly cleanup 
 */
class CacheShell extends BeditaBaseShell {

    /**
     * Remove object cache 
     */
    public function remove() {
        $id = !empty($this->params['id']) ? $this->params['id'] : null;
        if ($id) {
            $this->cleanupObjectIdCache($id);
            $this->out('Cache of object id ' . $id . ' removed');
            return;
        }
        if (empty($this->params['t'])) {
            $this->out('Please select object id (-id) or object type (-t)');
            return;
        }
        $type = $this->params['t'];
        $objectTypeId = Configure::read('objectTypes.' . $type . '.id');
        if (empty($objectTypeId)) {
            $this->out('Object type "' . $type . '" not found');
            return;
        }
        $BEObject = ClassRegistry::init('BEObject');
        $BEObject->contain();
        $conditions = array('object_type_id' => $objectTypeId);
        $nObj = $BEObject->find('count', array('conditions' => $conditions));
        $pageSize = 1000;
        $pageNum = 0;
        $this->out('Number of objects cache to cleanup: '. $nObj);
        $count = 0;
        while( ($pageSize * $pageNum) < $nObj ) {
            $res = $BEObject->find('list',array(
                'fields' => array('id'),
                'conditions' => $conditions,
                'order' => array('id' => 'asc'),
                'limit' => $pageSize,
                'offset' => $pageNum * $pageSize,
            ));
            $pageNum++;
            foreach ($res as $id) {
                $count++;
                $this->cleanupObjectIdCache($id);
                $this->out('Cache of object id ' . $id . ' removed');
            }
        }
        $this->out('Done');
    }

    /**
     * Remove cache of object id
     */
    private function cleanupObjectIdCache($id) {
        $BEObject = ClassRegistry::init('BEObject');
        $BEObject->clearCache($id);
    }

    function help() {
        $this->out('Available functions:');
        $this->out(' ');
        $this->out('1. remove: clean object cache by type or id');
        $this->out(' ');
        $this->out('   Usage: remove [-id <object-id>] [-t <object-type>');
		$this->out(' ');
		$this->out("    -t <object-type>\t object type cache to clean - image, video, document,...");
        $this->out("    -id <object-id>\t object id cache to clean");
        $this->out(' ');
    }
}

<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
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
 * Comments module controller
 */
class CommentsController extends ModulesController {

    var $helpers = ['BeTree', 'BeToolbar'];
    var $components = ['BeTree', 'BeLangText', 'BeSecurity'];
    var $uses = ['BannedIp', 'BEObject', 'Comment'];

    protected $moduleName = 'comments';

    public function index($id = null, $order = '', $dir = true, $page = 1, $dim = 20) {
        $filter = [
            'object_type_id' => Configure::read('objectTypes.comment.id'),
            'ref_object_details' => 'Comment',
        ];
        $filter['Comment.email'] = !empty($this->passedArgs['email']) ? $this->passedArgs['email'] : '';
        if (!empty($this->passedArgs['ip_created'])) {
            $filter['ip_created'] = $this->passedArgs['ip_created'];
        }
        $this->paginatedList($id, $filter, $order, $dir, $page, $dim);
    }
 
    public function view($id = null) {
        if (empty($id)) {
            return;
        }
        $type = $this->BEObject->findObjectTypeId($id);
        $types = $this->getModuleObjectTypes('comments');
        if (in_array($type, $types)) {
            $modelClass = $this->loadModelByObjectTypeId($type);
            $this->viewObject($modelClass, $id);
        }
        if ($this->BannedIp->isBanned($this->viewVars['object']['ip_created'])) {
            $this->set('banned', true);
        }
    }

    public function save() {
        $this->checkWriteModulePermission();
        if (empty($this->data)) {
            throw new BeditaException( __('No data', true));
        }
        $this->Transaction->begin() ;
        if (!$this->Comment->save($this->data)) {
            throw new BeditaException(__('Error saving comment', true), $this->Comment->validationErrors);
        }
        $this->Transaction->commit() ;
        $this->userInfoMessage(__('Comment saved', true).' - '.$this->data['title']);
        $this->eventInfo('comment ['. $this->data['title'].'] saved');
    }
    
    public function banIp() {
        $this->checkWriteModulePermission();
        if (empty($this->data)) {
            throw new BeditaException(__('No data', true));
        }
        $ip = $this->data['ip_to_ban'];
        $this->BannedIp->ban($ip, $this->data['ban_status']);
        if ($this->data['ban_status'] === 'ban') {
            $this->userInfoMessage(__('IP banned', true).' - '.$ip);
            $this->eventInfo('IP ['. $ip.'] banned');
        } else {
            $this->userInfoMessage(__('IP accepted', true).' - '.$ip);
            $this->eventInfo('IP ['. $ip.'] accepted');
        }
    }

    public function delete() {
        $this->checkWriteModulePermission();
        $objectsListDeleted = $this->deleteObjects('Comment');
        $this->userInfoMessage(__('Comments deleted', true) . ' -  ' . $objectsListDeleted);
        $this->eventInfo("Comments $objectsListDeleted deleted");
    } 

    public function deleteSelected() {
        $this->checkWriteModulePermission();
        $objectsListDeleted = $this->deleteObjects('Comment');
        $this->userInfoMessage(__('Comments deleted', true) . ' -  ' . $objectsListDeleted);
        $this->eventInfo("Comments $objectsListDeleted deleted");
    } 

    protected function forward($action, $result) {
        $moduleRedirect = [
            'banIp' => [
                'OK' => "/comments/view/{$this->data['id']}",
                'ERROR' => '/comments/view'
            ]
        ];
        return $this->moduleForward($action, $result, $moduleRedirect);
    }
}

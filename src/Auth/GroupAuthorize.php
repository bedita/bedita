<?php
/**-----8<--------------------------------------------------------------------
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
namespace BEdita\Auth;

use Cake\Auth\BaseAuthorize;
use Cake\Network\Request;
use Cake\Collection\Collection;

/**
 * GroupAuthorize class
 *
 * Check if user groups are authorized to access
 */
class GroupAuthorize extends BaseAuthorize {

    /**
     * Checks user authorization usign groups
     *
     * @param array $user Active user data
     * @param \Cake\Network\Request $request Request instance.
     * @return bool
     */
    public function authorize($user, Request $request) {
        if (empty($user['groups'])) {
            return false;
        }

        $collection = new Collection($user['groups']);
        $authGroups = $collection->filter(function($group, $key) {
            return $group['backend_auth'] === true;
        });

        if (count($authGroups->toArray()) == 0) {
            return false;
        }

        return true;
    }
}

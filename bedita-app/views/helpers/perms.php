<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009-2015 ChannelWeb Srl, Chialab Srl
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
 * Permission helper
 */
class PermsHelper extends AppHelper {

    /**
     * Check if user has permission on an action reading $config['actionPermission']
     *
     * @param array $authUser,
     *            user data with groups (like $BEAuthuser)
     * @param string $action,
     *            action to check in the form 'ControllerName.actionName'
     * @return boolean, true if user has access permissions, false otherwise
     */
    public function userActionAccess($authUser, $action) {
        $actionPerms = Configure::read('actionPermissions');
        if (!empty($actionPerms[$action]) && 
            !empty(array_intersect($authUser['groups'], $actionPerms[$action]))) {
            return true;
        } else {
            return false;
        }
    }

}

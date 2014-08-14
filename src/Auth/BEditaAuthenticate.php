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

use Cake\Auth\FormAuthenticate;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Core\Configure;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;

/**
 * BEditaAuthenticate class
 *
 * Authenticate users based on form POST data
 * Check username and password and login policy defined in configuration
 */
class BEditaAuthenticate extends FormAuthenticate {

    use LogTrait;

    /**
     * Authenticates the identity contained in a request. Will use the `config.userModel`, and `config.fields`
     * to find POST data that is used to find a matching record in the `config.userModel`. Will return false if
     * there is no post data, either username or password is missing, or if the scope conditions have not been met.
     * Additionally check login policy defined in configuration
     *
     * @param \Cake\Network\Request $request The request that contains login information.
     * @param \Cake\Network\Response $response Unused response object.
     * @return mixed False on login failure.  An array of User data on success.
     */
    public function authenticate(Request $request, Response $response) {
        $user = parent::authenticate($request, $response);
        $usersTable = TableRegistry::get('Users');
        if ($user !== false) {
            // check login policy
            $policy = Configure::read('loginPolicy');
            if (!isset($user['last_login'])) {
                $user['last_login'] = date('Y-m-d H:i:s');
            }
            $daysFromLastLogin = (time() - strtotime($user['last_login'])) / 86400000;

            if ($user['num_login_err'] >= $policy['maxLoginAttempts']) {
                $this->log('Max login attempts reached for user: ' . $user['userid'], 'notice');
                return false;
            } elseif ($daysFromLastLogin > $policy['maxNumDaysInactivity']) {
                $this->log('Max num days inactivity for user: ' . $user['userid'] . ', days: ' . $daysFromLastLogin, 'notice');
                return false;
            //} elseif ($daysFromLastLogin > $policy['maxNumDaysValidity']) {
            }

            // update user
            $user['num_login_err'] = 0;
            $userEntity = $usersTable->get($user['id']);
            $userEntity->num_login_err = $user['num_login_err'];
            $userEntity->last_login = $user['last_login'];

            // migrate old hashed password to new algorithm
            if ($this->needsPasswordRehash()) {
                $userEntity->passwd = $request->data('passwd');
            }

            $usersTable->save($userEntity);

        // else if userid exists update num_login_err
        } else {
            $userEntity = $usersTable->find()
                ->where(['userid' => $request->data('userid')])
                ->first();
            if ($userEntity) {
                $userEntity->num_login_err++;
                $userEntity->last_login_err = date('Y-m-d H:i:s');
                $usersTable->save($userEntity);
            }
        }

        return $user;
    }

}

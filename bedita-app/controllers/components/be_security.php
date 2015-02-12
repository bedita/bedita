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

App::import('Core', array('Security'));

/**
 * BeSecurityComponent class
 *
 * Handle token to avoid CSFR attack
 */
class BeSecurityComponent extends Object {

    /**
     * list of components used
     * @var array
     */
    public $components = array('Session', 'RequestHandler');

    /**
     * the controller that use this component
     * @var Controller
     */
    public $controller = null;

    /**
    * Whether to validate POST data. Set to false to disable for data coming from 3rd party
    * services, etc.
    *
    * @var boolean
    */
    public $validatePost = true;

    /**
     * Controller actions on which csrf validation is disabled
     *
     * @var array
     */
    public $disableActions = array();

    /**
     * initialize component
     *
     * @param  Controller $controller
     * @param  array  $settings
     * @return void
     */
    public function initialize($controller, $settings = array()) {
        $this->controller = &$controller;
        $this->_set($settings);
    }

    /**
     * startup component
     *
     * @param  Controller $controller
     * @return void
     */
    public function startup($controller) {
        $isPost = ($this->RequestHandler->isPost() || $this->RequestHandler->isPut());
        $isNotRequestAction = (
            !isset($controller->params['requested']) ||
            $controller->params['requested'] != 1
        );
        $disableActions = (!is_array($this->disableActions)) ? array($this->disableActions) : $this->disableActions;

        if ($isPost && $isNotRequestAction && $this->validatePost && !in_array($controller->action, $disableActions)) {
            if ($this->validateCsrf() === false) {
                throw new BeditaException(__('Security error: CSRF token is invalid. Please try to resubmit the form', true));
            }
        }
        $this->generateToken();
    }

    /**
     * generate token and put it in session
     * @return void
     */
    protected function generateToken() {
        if (!$this->Session->started()) {
            return false;
        }
        if (isset($this->controller->params['requested']) && $this->controller->params['requested'] === 1) {
            if ($this->Session->check('_csrfToken')) {
                $tokenData = unserialize($this->Session->read('_csrfToken'));
                $this->controller->params['_csrfToken'] = $tokenData;
            }
            return false;
        }
        $authKey = Security::generateAuthKey();
        $expires = strtotime('+' . Security::inactiveMins() . ' minutes');
        $token = array(
            'key' => $authKey,
            'expires' => $expires
        );

        if ($this->Session->check('_csrfToken')) {
            $tokenData = unserialize($this->Session->read('_csrfToken'));
            $valid = (
                isset($tokenData['expires']) &&
                $tokenData['expires'] > time() &&
                isset($tokenData['key'])
            );

            if ($valid) {
                $token['key'] = $tokenData['key'];
            }
        }
        $this->controller->params['_csrfToken'] = $token;
        $this->Session->write('_csrfToken', serialize($token));
        return true;
    }

    /**
     * Validate that the controller has a CSRF token in the POST data
     * and that the token is legit/not expired
     *
     * @return bool Valid csrf token.
     */
    protected function validateCsrf() {
        if (empty($this->controller->data) && empty($this->controller->params['form'])) {
            return true;
        }
        $data = $this->controller->data;

        if (!isset($data['_csrfToken']) || !isset($data['_csrfToken']['key'])) {
            return false;
        }
        $token = $data['_csrfToken']['key'];

        if ($this->Session->check('_csrfToken')) {
            $tokenData = unserialize($this->Session->read('_csrfToken'));

            if ($tokenData['expires'] < time() || $tokenData['key'] !== $token) {
                return false;
            }
        } else {
            return false;
        }
    }

}

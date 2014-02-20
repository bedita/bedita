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

App::import('Sanitize');

/**
 * SessionFilterComponent class
 *
 * Handle filter session used to list BEdita objects
 */
class SessionFilterComponent extends Object {

    /**
     * list of components used
     * @var array
     */
    public $components = array('Session');

    /**
     * the controller that use this component
     * @var Controller
     */
    public $controller = null;

    /**
     * the session key used to store filter
     * It's built as 'filter.ControllerName.actionName'
     * @var string
     */
    private $sessionKey = null;

    /**
     * initialize component
     *  - setup self::sessionKey
     *  - clean filter if not empty $controller->params['form']['cleanFilter']
     *  - if not to clean, setup filter if not empty $controller->params['form']['filter']
     *
     * @param  Controller $controller
     * @param  array  $settings
     */
    public function initialize(&$controller, $settings = array()) {
        $this->controller =& $controller;
        $this->sessionKey = 'filter.' . $controller->name . '.' . $controller->action;
        if (!empty($controller->params['form']['cleanFilter'])) {
            $this->clean();
        } elseif (!empty($controller->params['form']['filter'])) {
            $this->addBulk($controller->params['form']['filter']);
        }
        $this->controller->set('sessionFilterKey', $this->sessionKey);
    }

    public function startup(&$controller) {}

    /**
     * return filter session key
     * @return string
     */
    public function which() {
        return $this->sessionKey;
    }

    /**
     * setup filter session reading some params from url
     *
     * @param boolean $merge true to merge with existing filter (default)
     *                       false to override all filter
     */
    public function setFromUrl($merge = true) {
        $argsAccepted = array('id', 'category', 'relation', 'rel_object_id', 'rel_detail', 'comment_object_id', 'mail_group', 'tag', 'query', 'substring');
        $urlFilter = array();
        foreach ($this->controller->params['named'] as $key => $value) {
            if (in_array($key, $argsAccepted)) {
                if ($key == 'id') {
                    $key = 'parent_id';
                }
                if ($key == 'query') {
                    $value = addslashes(urldecode($value));
                }
                $urlFilter[$key] = $value;
            }
        }
        $this->addBulk($urlFilter, $merge);
        return $this->read();
    }

    /**
     * add a key to current filter session
     *
     * @param string $key
     * @param mixed $value
     */
    public function add($key, $value) {
        $filterToAdd = array($key => $value);
        $this->arrange($filterToAdd);
        $key = key($filterToAdd);
        $value = current($filterToAdd);
        $filter = $this->read();
        $filter[$key] = $value;
        $this->Session->write($this->sessionKey, $filter);
    }

    /**
     * add bulk filters to session
     *
     * @param array   $filter array of filters to add
     * @param boolean $merge  false to override old filter (default)
     *                        true to merge with old filter
     */
    public function addBulk(array $filter, $merge = false) {
        $this->arrange($filter);
        if ($merge) {
            $sessionFilter = $this->read();
            if (empty($sessionFilter)) {
                $sessionFilter = array();
            }
            $filter = array_merge($sessionFilter, $filter);
        }
        $this->Session->write($this->sessionKey, $filter);
    }

    /**
     * arrange data before insert in session
     * empty values are removed
     * other values are sanitized
     *
     * @param  array  $filter
     */
    private function arrange(array &$filter) {
        foreach ($filter as $key => $value) {
            if (empty($value)) {
                unset($filter[$key]);
            } else {
                $value = Sanitize::html($value, array('remove' => true));
            }
        }
    }

    /**
     * read a filter key
     *
     * @param  string $key
     * @return array
     */
    public function read($key = null) {
        $sessionKey = (!empty($key))? $this->sessionKey . '.' . $key : $this->sessionKey;
        $filter = $this->Session->read($sessionKey);
        if (!$filter) {
            $filter = array();
        }
        return $filter;
    }

    /**
     * delete a key from filter
     *
     * @param  string $key
     * @return boolean true if session variable is set and can be deleted, false if variable was not set
     */
    public function delete($key) {
        return $this->Session->delete($this->sessionKey . '.' . $key);
    }

    /**
     * clean self::$sessionKey from session
     *
     * @return boolean true if session variable is set and can be deleted, false if variable was not set
     */
    public function clean() {
        return $this->Session->delete($this->sessionKey);
    }

    /**
     * clean all filter in session
     *
     * @return boolean true if session variable is set and can be deleted, false if variable was not set
     */
    public function cleanAll() {
        return $this->Session->delete('filter');
    }

}
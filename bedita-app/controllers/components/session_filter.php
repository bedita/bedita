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
 * Handle filter session used to list BEdita objects.
 * The session key used to store filter
 * is usually built as self::$sessionKeyPrefix . '.ControllerName.actionName'
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
     * the session key prefix
     * @var string
     */
    private $sessionKeyPrefix = 'beditaFilter';

    /**
     * the session key used to store filter.
     * It's usually built as self::$sessionKeyPrefix . '.ControllerName.actionName'
     * @var string
     */
    private $sessionKey = null;

    /**
     * url args that are accepted to be put in session filter
     * used in self::getFromUrl() method
     * @var array
     */
    private $urlArgsAccepted = array(
        'id',
        'category',
        'relation',
        'rel_object_id',
        'rel_detail',
        'comment_object_id',
        'mail_group',
        'tag',
        'query',
        'substring',
        'status'
    );

    /**
     * initialize component
     *
     * @param  Controller $controller
     * @param  array  $settings
     */
    public function initialize(&$controller, $settings = array()) {
        $this->controller =& $controller;
    }

    /**
     * startup component
     *
     * starts the setup in BEdita backend
     * for frontend apps the setup starts in FrontendController::route()
     * if the config var 'enableSessionFilter' is set to true
     *
     * @param  Controller $controller
     */
    public function startup(&$controller) {
        // automatic setup only in backend
        if (BACKEND_APP) {
            $this->setup();
        }
    }

    /**
     *  setup Session Filter
     *
     * - setup self::sessionKey
     * - clean filter if not empty $controller->params['form']['cleanFilter']
     * - if not to clean, setup filter if not empty $controller->params['form']['filter']
     * @param  string $name suffix to assign to session key
     *                      if empty ControllerName.actionName was used
     */
    public function setup($name = null) {
        $this->setSessionKey($name);
        if (!empty($this->controller->params['form']['cleanFilter']) || !empty($this->controller->params['named']['cleanFilter'])) {
            $this->clean();
            // unset cleanFilter to avoid to use it writing url for pagination
            if (isset($this->controller->params['named']['cleanFilter'])) {
                unset($this->controller->params['named']['cleanFilter']);
            }
        } elseif (!empty($this->controller->params['form']['filter'])) {
            $this->addBulk($this->controller->params['form']['filter']);
        }
    }

    /**
     * set session key prefixing it with self::sessionKeyPrefix
     *
     * @param string $name suffix to assign to session key
     *                     if empty ControllerName.actionName was used
     */
    private function setSessionKey($name = null) {
        $suffix = (!empty($name))? $name : $this->controller->name . '.' . $this->controller->action;
        $this->sessionKey = $this->sessionKeyPrefix . '.' . $suffix;
        $this->controller->set('sessionFilterKey', $this->sessionKey);
    }

    /**
     * return filter session key
     * @return string
     */
    public function which() {
        return $this->sessionKey;
    }

    /**
     * get filter from url
     * only named params inside self::urlArgsAccepted will be returned
     *
     * @return array
     */
    public function getFromUrl() {
        $urlFilter = array();
        foreach ($this->controller->params['named'] as $key => $value) {
            if (in_array($key, $this->urlArgsAccepted)) {
                if ($key == 'id') {
                    $key = 'parent_id';
                }
                $urlFilter[$key] = urldecode($value);
            }
        }
        return $urlFilter;
    }

    /**
     * setup filter session reading some params from url
     *
     * @param boolean $merge true to merge with existing filter (default)
     *                       false to override all filter
     */
    public function setFromUrl($merge = true) {
        $urlFilter = $this->getFromUrl();
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
        reset($filterToAdd);
        $key = key($filterToAdd);
        $value = current($filterToAdd);
        $filter = $this->read();
        $filter[$key] = $value;
        return $this->Session->write($this->sessionKey, $filter);
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
        return $this->Session->write($this->sessionKey, $filter);
    }

    /**
     * arrange data before insert in session
     * empty values are removed
     *
     * @param  array  $filter
     */
    public function arrange(array &$filter) {
        foreach ($filter as $key => &$value) {
            if (empty($value)) {
                unset($filter[$key]);
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
        $filter = $this->Session->read($this->sessionKey);
        if (!empty($key) && !empty($filter[$key])) {
            $filter = $filter[$key];
        } elseif (!$filter) {
            $filter = array();
        }
        $filter = Sanitize::clean($filter, 
            array('escape' => false, 'encode' => false));
        // #532 add custom strip_tags - in Sanitize::clean withou 'encode' is not used
        if (is_array($filter)) {
            foreach ($filter as $k => $v) {
                if (!is_array($v)) {
                    $filter[$k] = strip_tags($v);
                }
            }
        } else {
            $filter = strip_tags($filter);
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
        $filter = $this->Session->read($this->sessionKey);
        $ret = false;
        if (isset($filter[$key])) {
            unset($filter[$key]);
            $ret = $this->addBulk($filter);
        }
        return $ret;
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
        return $this->Session->delete($this->sessionKeyPrefix);
    }

}
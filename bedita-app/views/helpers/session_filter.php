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

App::import('Helper', 'Session');

/**
 * SessionFilterHelper class
 *
 * work on filters put in session by SessionFilterComponent class
 */
class SessionFilterHelper extends AppHelper {

    /**
     * list of helpers used
     * @var array
     */
    public $helpers = array('Session');

    /**
     * the session key prefix
     * @var string
     */
    private $sessionKeyPrefix = 'beditaFilter';

    /**
     * the session key as filter.ControllerName.actionName
     * if it's defined the view var $sessionFilterKey then use its value as key
     * @var string
     */
    private $sessionKey = null;

    public function __construct() {
        parent::__construct();
        $view = ClassRegistry::getObject('view');
        if (!empty($view->viewVars['sessionFilterKey'])) {
            $this->sessionKey = $view->viewVars['sessionFilterKey'];
        } elseif (!empty($view->params['controller']) && !empty($view->params['action'])) {
            $this->sessionKey = $this->sessionKeyPrefix . '.' . $view->params['controller'] . '.' . $view->params['action'];
        }
    }

    /**
     * read all filters in session or a specific value for a key
     *
     * @param  string $key the filter session key
     * @return mixed false if $key doesn't exist in session
     */
    public function read($key = null) {
        $activeFilter = $this->Session->read($this->sessionKey);
        $value = false;
        if ($key) {
            if (!empty($activeFilter[$key])) {
                $value = $activeFilter[$key];
            }
        } else {
            $value = $activeFilter;
        }
        $value = Sanitize::clean($value, array('encode' => true, 'remove_html' => true));
        return $value;
    }

    /**
     * check the existence of $key in the filter
     * or if no $key is specified check if a filter is active
     *
     * @param  string $key
     * @return boolean
     */
    public function check($key = null) {
        $filter = $this->read($key);
        return (!empty($filter));
    }

}

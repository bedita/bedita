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

/**
 * Response handler component
 *
 * Handle json/xml response preparing data to serialize and setting up the right View class to use.
 * Help to set headers for the client too.
 *
 */
class ResponseHandlerComponent extends Object {

    /**
     * Other Components used
     *
     * @var array
     */
    public $components = array('RequestHandler');

    /**
     * The Controller
     *
     * @var Controller
     */
    public $controller = null;

    /**
     * Determines whether or not callbacks will be fired on this component
     * i.e. self::initialize(), self::startup(), self::beforeRender()
     *
     * @var boolean
     */
    public $enabled = true;

    /**
     * The response type as 'json', 'xml'
     * @var string|null
     */
    protected $type = null;

    /**
     * The types that are automatically handled.
     * For these types the right content type and the right View class are set.
     * Moreover all view variables found in '_serialize' key will be prepared to be serialized in the right View
     *
     * @var array
     */
    private $typesHandled = array('json', 'xml');

    /**
     * Xml format (tags or attributes)
     *
     * @var string
     */
    public $xmlFormat = 'tags';

    /**
     * Initialize self::type from $settings or from extension or from request ACCEPT header
     *
     * @param Controller $controller
     * @param array $settings
     * @return void
     */
    public function initialize($controller, $settings = array()) {
        $this->controller = &$controller;
        $this->_set($settings);
        if ($this->RequestHandler->isAjax()) {
           $this->controller->layout = 'ajax';
        }
        if (!$this->type || !in_array($this->type, $this->typesHandled)) {
            $action = $controller->params['action'];
            if (!BACKEND_APP && $action == 'route' && !empty($controller->params['pass'][0])) {
                $action = $controller->params['pass'][0];
            }
            $ext = strtolower(pathinfo($action, PATHINFO_EXTENSION));
            if (in_array($ext, $this->typesHandled)) {
                $this->type = $ext;
            } else {
                if ($this->RequestHandler->prefers('json')) {
                    $this->type = 'json';
                } elseif ($this->RequestHandler->prefers('xml')) {
                    $this->type = 'xml';
                } else {
                    $this->type = null;
                }
            }
        }
    }

    /**
     * The startup Component callback
     *
     * @param Controller $controller
     * @return void
     */
    public function startup($controller) {
    }

    /**
     * The beforeRender Component callback
     * If self::type was set and is supported (self::typesHandled) set content type header, the right View to use and data for view.
     *
     * @param Controller $controller
     * @return void
     */
    public function beforeRender($controller) {
        if ($this->type) {
            $this->RequestHandler->respondAs($this->type);
            $type = Inflector::camelize($this->type);
            $this->controller->view = $type;
            if (method_exists($this, 'setup' . $type)) {
                $this->{'setup' . $type}();
            }
        }
    }

    /**
     * Return the response type set
     *
     * @return string|null
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set the response type that will be used in self::beforeRender()
     *
     * @param string $type
     */
    public function setType($type) {
        if (in_array($type, $this->typesHandled)) {
            $this->type = $type;
        }
    }

    /**
     * Send status code to client
     *
     * @param int $status the http status code as 200, 404, 500, etc...
     * @return void
     */
    public function sendStatus($status) {
        if (!empty($status)) {
            $httpCode = $this->controller->httpCodes($status);
            if (!empty($httpCode)) {
                $header = 'HTTP/1.1 ' . $status . ' ' . $httpCode[$status];
                $this->sendHeader($header);
            }
        }
    }

    /**
     * Send header to client.
     *
     * @param string $name the header name
     * @param string $value the header value
     * @return void
     */
    public function sendHeader($name, $value = null) {
        if ($value === null) {
            header($name);
        } else {
            header($name . ': ' . $value);
        }
    }

    /**
     * setup specific data for Xml View
     *
     * @return void
     */
    private function setupXml() {
        $this->controller->set('options', array('format' => $this->xmlFormat));
    }

}

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
 * Response handler component for handling ajax and json/xml response
 * Also help to set headers for the client
 *
 * Setup the right View class to use and prepare data for view
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
     * The types that are automatically handled.
     * For these types the right content type and the right View class are set.
     *
     * @var array
     */
    private $typesHandled = array('json', 'xml');

    /**
     * The response type as 'json', 'xml'
     * @var string|null
     */
    public $type = null;

    /**
     * Xml format (tags or attributes)
     *
     * @var string
     */
    private $xmlFormat = 'tags';

    /**
     * Initialize self::type from $settings or from request ACCEPT header or from extension
     *
     * @param Controller $controller
     * @param array $settings
     * @return void
     */
    public function initialize($controller, $settings = array()) {
        $this->controller = &$controller;
        $this->_set($settings);
        if ($this->RequestHandler->isAjax()) {
           // $this->controller->layout = 'ajax';
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
                if ($this->RequestHandler->accepts('json')) {
                    $this->type = 'json';
                } elseif ($this->RequestHandler->accepts('xml')) {
                    $this->type = 'xml';
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
        if ($this->type && in_array($this->type, $this->typesHandled)) {
            $this->RequestHandler->respondAs($this->type);
            $type = Inflector::camelize($this->type);
            $this->controller->view = $type;
            if (method_exists($this, 'setup' . $type)) {
                $this->{'setup' . $type}();
            }
            $this->setData();
        }
    }

    /**
     * Send status code to client
     *
     * @param int $status the http status code as 200, 404, 500, etc...
     * @return void
     */
    public function sendStatus($status) {
        $httpCode = $this->controller->httpCodes($status);
        if (!empty($httpCode)) {
            $header = 'HTTP/1.1 ' . $status . ' ' . $httpCode[$status];
            $this->sendHeader($header);
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
        $availableFormat = array('attributes', 'tags');
        $paramsNamed = $this->controller->params['named'];
        if (!empty($paramsNamed['format']) && in_array($paramsNamed['format'], $availableFormat)) {
            $options = array('format' => $paramsNamed['format']);
        } else {
            $options = array('format' => $this->xmlFormat);
        }
        $this->controller->set('options', $options);
    }

    /**
     * Set 'data' View var with all var set in '_serialize' array
     */
    private function setData() {
        $data = array();
        $viewVars = $this->controller->viewVars;
        if (!empty($viewVars['_serialize'])) {
            foreach ($viewVars['_serialize'] as $varName) {
                if (array_key_exists($varName, $viewVars)) {
                    $data[$varName] = $viewVars[$varName];
                }
            }
        }
        $this->controller->set('data', $data);
    }
}

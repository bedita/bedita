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

/**
 * ApiBaseController class
 *
 * Abstract Base Api Controller
 * It must to be extened from ApiController in frontend apps
 */
abstract class ApiBaseController extends FrontendController {

    /**
     * The default end points
     *
     * @var array
     */
    private $defaultEndPoints = array('objects');

    /**
     * Other end points specified in the frontend app
     * They will be merged with self::defaultEndPoints()
     *
     * @var array
     */
    protected $endPoints = array();

    /**
     * The response data for client
     *
     * @var array
     */
    protected $responseData = array();

    /**
     * An array of filter to apply to objects
     *
     * @var array
     */
    protected $filter = array();

    /**
     * Constructor
     * Merge self::defaultEndPoints, self::endPoints and object types whitelist end points
     */
    public function __construct() {
        $this->endPoints = array_unique(array_merge($this->defaultEndPoints, $this->endPoints));
        $objectTypes = Configure::read('objectTypes');
        foreach ($objectTypes as $key => $value) {
            if (is_numeric($key)) {
                $this->endPoints[] = Inflector::pluralize($value['name']);
            }
        }
        parent::__construct();
    }

    /**
     * Enables calling methods for object types as /documents, /events, etc... delegate the action to slef::objects()
     *
     * @param string $method name of the method to be invoked
     * @param array $arguments list of arguments passed to the function
     * @return mixed
     */
    public function __call($method, $arguments) {
        $objectType = Configure::read('objectTypes.' . Inflector::singularize($method));
        if (!empty($objectType)) {
            $this->filter['object_type_id'] = $objectType['id'];
            return call_user_func_array(array($this, 'objects'), $arguments);
        }

        // missing end point action throw exception
    }

    /**
     * Set View and response (json)
     *
     * If method is overridden in ApiController remember to call parent::beforeCheckLogin()
     *
     * @return void
     */
    protected function beforeCheckLogin() {
        $this->view = 'View';
        $this->RequestHandler->respondAs('json');
    }

    /**
     * Set common meta data for response
     * Meta data are:
     *  - url
     *  - params
     *  - api
     */
    private function setBaseResponse() {
        $this->responseData['url'] = $this->params['url']['url'];
        $urlParams = array_slice($this->params['url'], 1);
        $this->responseData['params'] = array_merge($this->params['pass'], $urlParams, $this->params['named']);
        $this->responseData['api'] = $this->action;
    }

    /**
     * Any Api request has to pass from this method (see frontend app routes.php)
     * Override FrontendController::route()
     *
     * The method checks for valid api end points and call method or fallback to self::__call()
     *
     * @return void
     */
    public function route() {
        $args = func_get_args();
        $name = array_shift($args);
        // generic methodName
        $methodName = str_replace(".", "_", $name);
        // avoid to call methods that aren't end points
        if (!in_array($methodName, $this->endPoints)) {
            throw new BeditaException(__('Call undefined api end point', true));
        }

        $this->action = $methodName;
        call_user_func_array(array($this, $methodName), $args);

        $this->buildResponse();
    }

    /**
     * objects end point method
     *
     * If $name is passed try to load an object with that id or nickname
     *
     * @param int|string $name an object id or nickname
     * @return void
     */
    protected function objects($name = null) {
        if (!empty($name)) {
            $id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
            $object = $this->loadObj($id);
            // check if id correspond to object type requested (if any)
            if (!empty($this->filter['object_type_id']) && $object['object_type_id'] != $this->filter['object_type_id']) {
                throw new BeditaException('Object type mismatch');
            }
            $this->responseData['data']['object'] = $object;
        // @todo list of objects
        } else {

        }
    }

    protected function objectType($objectType) {
        die();
    }

    /**
     * Build repsonse data for client
     *
     * @return void
     */
    protected function buildResponse() {
        $this->setBaseResponse();
        $this->set('data', $this->responseData);
        $this->render('/pages/json');
    }

}

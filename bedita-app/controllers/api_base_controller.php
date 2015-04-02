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
 * It must to be extended by ApiController in frontend apps
 */
abstract class ApiBaseController extends FrontendController {

    public $uses = array();

    public $components = array(
        'ResponseHandler' => array('type' => 'json')
    );

    protected $loginRedirect = null;

    /**
     * The default endpoints
     *
     * @var array
     */
    private $defaultEndPoints = array('objects', 'session', 'me', 'poster');

    /**
     * Default allowed model bindings
     *
     * @var array
     */
    private $defaultModelBindings = array('default', 'frontend', 'minimum');

    /**
     * Other endpoints specified in the frontend app
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
     * The POST data in request
     *
     * @var array
     */
    private $postData = array();

    /**
     * An array of filter to apply to objects
     *
     * @var array
     */
    protected $filter = array();

    /**
     * The request method invoked
     *
     * @var string
     */
    protected $requestMethod = null;

    /**
     * Constructor
     * Merge self::defaultEndPoints, self::endPoints and object types whitelist endpoints
     */
    public function __construct() {
        $this->components[] = 'ApiFormatter';
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
     * Enables calling methods for object types as /documents, /events, etc... delegating the action to self::objects()
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

        throw new BeditaMethodNotAllowedException();
    }

    /**
     * Normalize POST data
     *
     * This function searches for POST data in the global var $_POST and in 'php://input' alias file
     * Some Javascript XHR wrappers POSTs data are passed through 'php://input'
     *
     * @return array
     */
    private function handlePOST() {
        if (empty($this->params['form'])) {
            try {
                $postdata = file_get_contents('php://input');
                $this->params['form'] = json_decode($postdata, true);
                if (!empty(json_last_error())) {
                    $this->params['form'] = array();
                }
            } catch(Excpetion $ex) {
                $this->params['form'] = array();
            }
        }
    }

    /**
     * Start Session if authorization token is found
     * If method is overridden in ApiController remember to call parent::beforeCheckLogin()
     *
     * @return void
     */
    protected function beforeCheckLogin() {
        $this->requestMethod = strtolower(env('REQUEST_METHOD'));
        if ($this->requestMethod == 'post') {
            $this->handlePOST();
        }

        $token = null;
        //@todo clean and move to component?
        if (function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (!empty($h['Authorization'])) {
                $token = $h['Authorization'];
            }
        } elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            // fastcgi + rewrite rule
            $token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            //$this->log("Http header token " . $token, LOG_DEBUG);
        } elseif (!empty($_SERVER['REDIRECT_REDIRECT_HTTP_AUTHORIZATION'])) {
            // fastcgi + rewrite rule
            $token = $_SERVER['REDIRECT_REDIRECT_HTTP_AUTHORIZATION'];
            //$this->log("Http header token " . $token, LOG_DEBUG);
        }

        // @todo remove token pass in named
        if (!empty($this->params["named"]["token"])) {
            $token = $this->params["named"]["token"];
            //$this->log("URL token " . $token, LOG_DEBUG);
        } elseif (!empty($this->params["url"]["accessToken"])) {
            $token = $this->params["url"]["accessToken"];
        }

        if ($token) {
            $this->BeAuth->startSession($token);
        } else {
            if (!empty($this->params['form']) && !empty($this->params['form']['username']) && !empty($this->params['form']['password'])) {
                $this->params['form']['login'] = array('username' => $this->params['form']['username'], 'password' => $this->params['form']['password']);
                unset($this->params['form']['username']);
                unset($this->params['form']['password']);
            }
        }
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
        $getParams = array_slice($_GET, 0);
        unset($getParams['url']);
        $this->responseData['params'] = array_merge($urlParams, $this->params['named'], $getParams);
        $this->responseData['api'] = $this->action;
        $this->responseData['method'] = $this->requestMethod;
    }

    /**
     * Any Api request has to pass from this method (see frontend app routes.php)
     * Override FrontendController::route()
     *
     * The method checks for valid api endpoints and call method or fallback to self::__call()
     *
     * @return void
     */
    public function route() {
        $args = func_get_args();
        $name = array_shift($args);
        // generic methodName
        $methodName = str_replace(".", "_", $name);
        // avoid to call methods that aren't endpoints
        if (!in_array($methodName, $this->endPoints)) {
            $this->action = $methodName;
            throw new BeditaMethodNotAllowedException();
        } else {
            $this->action = $methodName;
            $specificMethodName = Inflector::camelize($this->requestMethod . '_' . $methodName);
            if (method_exists($this, $specificMethodName)) {
                call_user_func_array(array($this, $specificMethodName), $args);
            } else {
                call_user_func_array(array($this, $methodName), $args);
            }
        }
        $this->response();
    }

    /**
     * objects endpoint method
     *
     * If $name is passed try to load an object with that id or nickname
     *
     * @param int|string $name an object id or nickname
     * @param string $kinship
     * @return void
     */
    protected function objects($name = null, $kinship = null) {
        if (!empty($name)) {
            $id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
            $kinshipTypes = array('ancestors', 'parents', 'children', 'descendants', 'siblings');
            if (!empty($kinship)) {
                if (!in_array($kinship, $kinshipTypes)) {
                    throw new BeditaBadRequestException();
                } else {
                    $method = 'load' . Inflector::camelize($kinship);
                    $this->{$method}($id);
                }
            } else {
                $options = array();
                if (!empty($this->params['url']['binding']) && in_array($this->params['url']['binding'], $this->defaultModelBindings)) {
                    $options['bindingLevel'] = $this->params['url']['binding'];
                }
                $object = $this->loadObj($id, true, $options);
                if ($object == parent::UNLOGGED) {
                    throw new BeditaUnauthorizedException();
                }
                if ($object == parent::UNAUTHORIZED) {
                    throw new BeditaForbiddenException();
                }
                // check if id correspond to object type requested (if any)
                if (!empty($this->filter['object_type_id']) && $object['object_type_id'] != $this->filter['object_type_id']) {
                    throw new BeditaInternalErrorException('Object type mismatch');
                }

                $collections = array(
                    Configure::read('objectTypes.area.id'),
                    Configure::read('objectTypes.section.id')
                );
                if (in_array($object['object_type_id'], $collections)) {
                    $object['children'] = $this->loadSectionObjects($object['id']);
                }

                $this->responseData['data'] = $this->ApiFormatter->formatObject($object);
            }
        // @todo list of objects
        } else {

        }
    }

    /**
     * Load children of object $id
     *
     * @param int $id
     * @return void
     */
    protected function loadChildren($id, array $options = array()) {
        $objects = $this->loadSectionObjects($id, $options);
        if (empty($objects['childContents'])) {
            $this->responseData['data'] = array();
        } else {
            $objectsData = $this->ApiFormatter->formatObjects($objects['childContents']);
            $this->responseData['data'] = $objectsData;
            $this->responseData['paging'] = $this->ApiFormatter->formatPaging($objects['toolbar']);
        }
    }

    /**
     * Load descendants of object $id
     *
     * @param int $id
     * @return void
     */
    protected function loadDescendants($id) {
        $this->loadChildren($id, array(
            'filter' => array('descendants' => true)
        ));
    }

    /**
     * Load siblings of object $id
     *
     * @param int $id
     * @return void
     */
    protected function loadSiblings($id) {
        // get only first parent?
        $parentIds = ClassRegistry::init('Tree')->getParents($id, $this->publication['id'], $this->getStatus());
        if (empty($parentIds)) {
            throw new BeditaNotFoundException('The object ' . $id . ' have no parents');
        }
        $this->loadChildren($parentIds[0], array(
            'filter' => array('NOT' => array('BEObject.id' => $id))
        ));
    }

    /**
     * user profile end point method
     *
     * @param int|string $userid an user id or userid
     * @return void
     */
    protected function profile($userid = null) {
        if (!empty($userid)) {
            $id = is_numeric($userid) ? intval($userid) : $userid;
            $userModel = ClassRegistry::init('User');
            $cardId = $userModel->findCardId($userid);
            if ($cardId !== false) {
                $this->objects($cardId);
            } else {
                throw new BeditaNotFoundException(); 
            }
        } else {
            throw new BeditaBadRequestException();
        }
    }

    /**
     * logged user profile end point method
     *
     * @return void
     */
    protected function me() {
        if ($this->Session->valid()) {
            $user = $this->BeAuth->getUserSession();
            $this->profile($user['id']);
        } else {
            throw new BeditaUnauthorizedException();
        }
    }

    protected function poster($id = null) {
        if (!empty($id)) {
            $objectModel = ClassRegistry::init('BEObject');
            $obj = $objectModel->field('id', array(
                'OR' => array(
                    'id' => $id,
                    'nickname' => $id
                )
            ));
            if (!empty($obj)) {
                $poster = $objectModel->getPoster($obj);
                if ($poster !== false) {
                    $thumbConf = array();
                    if (!empty($this->params['url'])) {
                        $acceptConf = array(
                            'width' => true,
                            'height' => true,
                            'preset' => true
                        );
                        $thumbConf = array_intersect_key($this->params['url'], $acceptConf);
                        if (isset($thumbConf['preset'])) {
                            $presetConf = Configure::read('thumbnails.' . $thumbConf['preset']);
                            if (!empty($presetConf)) {
                                $thumbConf = $presetConf;
                            }
                        }
                        $thumbConf['URLonly'] = true;
                    }

                    try {
                        $beThumb = BeLib::getObject('BeThumb');
                        $poster['id'] = (int) $poster['id']; 
                        $poster['uri'] = $beThumb->image($poster, $thumbConf);
                        $this->responseData['data'] = $poster;
                    } catch(Excpetion $ex) {
                        $this->responseData['data'] = array();
                    }
                } else {
                    $this->responseData['data'] = array();
                }
            } else {
                throw new BeditaNotFoundException();
            }
        } else {
            throw new BeditaBadRequestException();
        }
    }

    /**
     * Create user session
     *
     * @return void
     */
    protected function postSession() {
        if ($this->Session->valid()) {
            $this->getSession();
        } else {
            throw new BeditaUnauthorizedException();
        }
    }

    /**
     * Check user session
     *
     * @return void
     */
    protected function getSession() {
        if ($this->Session->valid()) {
            $this->responseData['data'] = array(
                'accessToken' => $this->Session->id(),
                'expiresIn' => $this->Session->cookieLifeTime - (time() - $this->Session->sessionTime),
                'valid' => true
            );
        } else {
            throw new BeditaUnauthorizedException('Invalid or expired session.');
        }
    }

    /**
     * Destroy session
     *
     * @return void
     */
    protected function deleteSession() {
        if ($this->Session->valid()) {
            $this->logout(false);
            $this->responseData['data'] = array('logout' => true);
        } else {
            throw new BeditaUnauthorizedException('Invalid or expired session');
        }
    }

    /**
     * Build response data for client
     *
     * @return void
     */
    protected function response() {
        $this->setBaseResponse();
        ksort($this->responseData);
        $this->set($this->responseData);
        $this->set('_serialize', array_keys($this->responseData));
    }

}

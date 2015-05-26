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
        'ResponseHandler' => array('type' => 'json'),
        'ApiFormatter',
        'BeAuthJwt'
    );

    protected $loginRedirect = null;

    /**
     * The default endpoints
     *
     * @var array
     */
    private $defaultEndPoints = array('objects', 'auth', 'me', 'poster');

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
     * Endpoints blacklisted
     * Useful for blacklisting self::defaultEndPoints or BEdita objects type as documents, events, ...
     *
     * @var array
     */
    protected $blacklistEndPoints = array();

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
     * Merge self::defaultEndPoints, self::endPoints and remove self::blacklistEndPoints
     */
    public function __construct() {
        Configure::write('Session.start', false);
        $this->endPoints = array_unique(array_merge($this->defaultEndPoints, $this->endPoints));
        $objectTypes = Configure::read('objectTypes');
        foreach ($objectTypes as $key => $value) {
            if (is_numeric($key)) {
                $this->endPoints[] = Inflector::pluralize($value['name']);
            }
        }
        $this->endPoints = array_diff($this->endPoints, $this->blacklistEndPoints);
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
                $jsonError = json_last_error();
                if (!empty($jsonError)) {
                    $this->params['form'] = array();
                }
            } catch(Exception $ex) {
                $this->params['form'] = array();
            }
        }
    }

    /**
     * Common operations that every call must do:
     *
     * - replace self::BeAuth with self::BeAuthJwt to work properly in FrontendController.
     *   BeAuthComponent is not used in api context. JWT is used instead via BeAuthJwtComponent
     * - check origin
     * - setup self::requestMethod to http verb used
     * - normalize post data
     * - normalize authentication form fields
     *
     * If method is overridden in frontend ApiController remember to call parent::beforeCheckLogin()
     *
     * @return void
     */
    protected function beforeCheckLogin() {
        $this->BeAuth = $this->BeAuthJwt;
        // Cross origin check.
        if (!$this->checkOrigin()) {
            throw new BeditaForbiddenException('Unallowed Origin');
        }

        $this->ResponseHandler->sendHeader('Access-Control-Allow-Methods', "POST, GET, PUT, DELETE, OPTIONS, HEAD");
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (!empty($headers['Access-Control-Request-Headers'])) {
                $this->ResponseHandler->sendHeader('Access-Control-Allow-Headers', $headers['Access-Control-Request-Headers']);
            }
        }

        $this->requestMethod = strtolower(env('REQUEST_METHOD'));
        if ($this->requestMethod == 'post') {
            $this->handlePOST();
        } elseif ($this->requestMethod == 'options' || $this->requestMethod == 'head') {
            $this->_stop();
        }

        if (!empty($this->params['form']) && !empty($this->params['form']['username']) && !empty($this->params['form']['password'])) {
            $this->params['form']['login'] = array('username' => $this->params['form']['username'], 'password' => $this->params['form']['password']);
            unset($this->params['form']['username']);
            unset($this->params['form']['password']);
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
     * set self::responseData['data'] array used as output data by self::response()
     *
     * @param array $data
     * @param boolean $merge true if $data has to be merged with previous set
     */
    protected function setData(array $data = array(), $merge = false) {
        $this->responseData['data'] = ($merge && isset($this->responseData['data'])) ? array_merge($this->responseData['data'], $data) : $data;
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
        if (!empty($methodName)) {
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
        } else {
            $this->baseUrlResponse();
            return $this->response(false);
        }
        $this->response();
    }

    /**
     * prepare response data for base api url
     *
     * default response: show list of available endpoints with urls
     * override in subclasses for custom response
     */
    protected function baseUrlResponse() {
        $baseUrl = Router::url($this->here, true);
        $rPos = strrpos($baseUrl, '/');
        if ($rPos !== (strlen($baseUrl) - 1)) {
            $baseUrl .= '/';
        }
        foreach ($this->endPoints as $endPoint) {
            $this->responseData[$endPoint] = $baseUrl . $endPoint;
        }
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

                $object = $this->ApiFormatter->formatObject($object);
                $this->setData($object);
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
            $this->setData();
        } else {
            $objectsData = $this->ApiFormatter->formatObjects($objects['childContents']);
            $this->setData($objectsData);
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
        $user = $this->BeAuthJwt->identify();
        if ($user) {
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
                        $this->setData($poster);
                    } catch (Exception $ex) {
                        $this->setData();
                    }
                } else {
                    $this->setData();
                }
            } else {
                throw new BeditaNotFoundException();
            }
        } else {
            throw new BeditaBadRequestException();
        }
    }

    /**
     * Auth POST actions.
     * Depending from 'grant_type':
     * - if 'grant_type' is 'password' and credentials are good then generate 'access_token' (JWT) and refresh token
     * - if 'grant_type' is 'refresh_token' it expects a 'refresh_token' and if it's valid renew 'access_token'
     *
     * @return void
     */
    protected function postAuth() {
        $params = $this->params['form'];
        $grantType = (!empty($params['grant_type'])) ? $params['grant_type'] : 'password';
        if ($grantType == 'password') {
            if (empty($params['login']['username']) || empty($params['login']['password'])) {
                throw new BeditaBadRequestException();
            }
            $user = $this->BeAuthJwt->identify();
            if (!$user) {
                throw new BeditaUnauthorizedException();
            }

            $token = $this->BeAuthJwt->generateToken();
            $refreshToken = $this->BeAuthJwt->generateRefreshToken();
            $data = array(
                'access_token' => $token,
                'expires_in' => $this->BeAuthJwt->config['expiresIn'],
                'refresh_token' => $refreshToken
            );
        } elseif ($grantType == 'refresh_token') {
            if (empty($params['refresh_token'])) {
                throw new BeditaBadRequestException();
            }

            $token = $this->BeAuthJwt->renewToken($params['refresh_token']);
            if (!$token) {
                throw new BeditaUnauthorizedException('invalid refresh token');
            }

            $data = array(
                'access_token' => $token,
                'expires_in' => $this->BeAuthJwt->config['expiresIn'],
                'refresh_token' => $params['refresh_token']
            );
        } else {
            throw new BeditaBadRequestException('invalid grant');
        }

        $this->setData($data);
    }

    /**
     * If user identified it responds with current access_token
     * and the updated time to expiration
     *
     * @return void
     */
    protected function getAuth() {
        $user = $this->BeAuthJwt->identify();
        if (!$user) {
            throw new BeditaUnauthorizedException();
        }
        $this->setData(array(
            'access_token' => $this->BeAuthJwt->getToken(),
            'expires_in' => $this->BeAuthJwt->expiresIn()
        ));
    }

    /**
     * Revoke authentication removing refresh token
     *
     * @param string $refreshToken the refresh token to revoke
     * @return void
     */
    protected function deleteAuth($refreshToken) {
        if ($this->BeAuthJwt->revokeRefreshToken($refreshToken)) {
            $this->setData(array('logout' => true));
        } else {
            throw new BeditaInternalErrorException();
        }
    }

    /**
     * Build response data for client
     *
     * @param boolean $setBase should set generic api response info
     * @return void
     */
    protected function response($setBase = true) {
        if ($setBase) {
            $this->setBaseResponse();
        }
        ksort($this->responseData);
        $this->set($this->responseData);
        $this->set('_serialize', array_keys($this->responseData));
    }

    /**
     * Checks if an origin is allowed.
     * Allowed origins are set in `$conf['api']['allowedOrigins']`.
     * Use `*` to allow any origin. Use `http://*.example.com` to allow any third-level subdomain,
     * use `http://**.example.com` to allow any subdomain, sub-subdomain, ...
     *
     * @return bool
     */
    private function checkOrigin() {
        $allowed = Configure::read('api.allowedOrigins');
        if (!is_array($allowed)) {
            $allowed = (!empty($allowed)) ? array($allowed) : array('*');
        }
        if (in_array('*', $allowed)) {
            $this->ResponseHandler->sendHeader('Access-Control-Allow-Origin', '*');
            return true;
        }

        $parsed = parse_url(array_key_exists('HTTP_ORIGIN', $_SERVER) ? $_SERVER['HTTP_ORIGIN'] : null);
        $origin = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
        $origin .= isset($parsed['host']) ? $parsed['host'] : '';

        $replace = array(
            '\*\*\.' => '(([a-z0-9_\-]+\.)*[a-z0-9_\-]+\.)?',
            '\*\.' => '([a-z0-9_\-]+\.)?',
        );
        foreach ($allowed as $allow) {
            $regex = '/^' . str_replace(array_keys($replace), array_values($replace), preg_quote($allow, '/')) . '$/i';
            if (preg_match($regex, $origin)) {
                $this->ResponseHandler->sendHeader('Access-Control-Allow-Origin', $origin);
                $this->ResponseHandler->sendHeader('Vary', 'Origin');
                return true;
            }
        }
        return false;
    }
}

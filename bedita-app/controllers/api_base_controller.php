<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2014-2015 ChannelWeb Srl, Chialab Srl
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

    /**
     * The Models used
     *
     * @var array
     */
    public $uses = array();

    /**
     * The Components used
     *
     * @var array
     */
    public $components = array(
        'ResponseHandler' => array('type' => 'json'),
        'ApiFormatter',
        'ApiValidator',
        'ApiUpload'
    );

    /**
     * Contain the instance of API auth component used
     * Normally it corresponds to ApiAuthComponent but it can contain another auth component
     * To do it a custom component, named for example 'MyAuth', has to be activated via conf
     *
     * ```
     * $config['api'] = array(
     *     'baseUrl' => '/api',
     *     'auth' => array(
     *         'component' => 'MyAuth'
     *     ),
     *     ...
     * );
     * ```
     *
     * Note that the custom auth component should implements ApiAuthInterface
     *
     * @var Object
     */
    public $ApiAuth = null;

    /**
     * The default endpoints
     *
     * @var array
     */
    private $defaultEndPoints = array('objects', 'auth', 'me', 'posters', 'files');

    /**
     * The default binding level
     *
     * @see FrontendController::defaultBindingLevel
     * @var string
     */
    protected $defaultBindingLevel = 'api';

    /**
     * Allowed model bindings
     * Used to get more or less fields and associations through GET /objects param 'binding'
     * By default no one is permit but it is overridable in ApiController
     *
     * Example:
     *```
     * protected $allowedModelBindings = array('default', 'frontend', 'minimum');
     * ```
     *
     * and call GET /objects/:name?binding=minimum
     *
     * @var array
     */
    protected $allowedModelBindings = array();

    /**
     * Other endpoints specified in the frontend app
     * They will be merged with self::defaultEndPoints()
     *
     * @var array
     */
    protected $endPoints = array();

    /**
     * Endpoints blacklisted
     * Useful for blacklisting self::defaultEndPoints
     *
     * @var array
     */
    protected $blacklistEndPoints = array();

    /**
     * White list of object types that have to be mapped to endpoints
     * For example setting
     *
     * ```
     * $whitelistObjectTypes = array('document', 'event')`;
     * ```
     *
     * enable '/documents' and '/events' endpoints that filter objects respectively by document and event object type.
     *
     * @var array
     */
    protected $whitelistObjectTypes = array();

    /**
     * The response data for client
     *
     * @var array
     */
    protected $responseData = array();

    /**
     * If response has to be built automatically at the end of the action
     *
     * @see self::response() set autoResponse to false
     * @see self::route() if autoResponse is true call self::response()
     * @var boolean
     */
    protected $autoResponse = true;

    /**
     * Pagination options used to paginate objects
     * Default values are
     *
     *  ```
     * 'page' => 1, // the page to load
     * 'pageSize' => 20, // the dimension of the page
     * 'maxPageSize' => 100 // the max page dimension in a request
     * ```
     *
     * If 'page' or 'page_size' are in query url then they override those default
     *
     * @var array
     */
    protected $paginationOptions = array(
        'page' => 1,
        'pageSize' => 20,
        'maxPageSize' => 100
    );

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
    protected $objectsFilter = array();

    /**
     * The request method invoked (get, post, put, delete)
     *
     * @var string
     */
    protected $requestMethod = null;

    /**
     * The complete base url for API
     * i.e. https://example.com/api/v1
     * It is filled the first time self::baseUrl() is called
     *
     * @var string
     */
    private $fullApiBaseUrl = null;

    /**
     * The allowed url path you can apply to /objects endpoint.
     * The url path is divided by request type 'get', 'post', 'put' and 'delete'
     *
     * For example GET /objects/1/children search the children of object with id = 1
     *
     * Override in ApiController to limit or add functionality to /objects endpoint
     * For example adding to 'get' array 'foo' filter and adding ApiController::getObjectsFoo() you can call /objects/1/foo
     *
     * All filters must have a corresponding class method built as self::requestMethod + Objects + filter camelized, for example:
     * - getObjectsChildren() maps $allowedObjectsUrlPath['get']['children']
     * - postObjectsRelations() maps $allowedObjectsUrlPath['post']['relations']
     *
     * @var array
     */
    protected $allowedObjectsUrlPath = array(
        'get' => array(
            'relations',
            'children',
            'contents',
            'sections',
            'descendants',
            'siblings',
            //'ancestors',
            //'parents'
        ),
        'post' => array(
            'relations',
            'children'
        ),
        'put' => array(
            'relations',
            'children'
        ),
        'delete' => array(
            'relations',
            'children'
        )
    );

    /**
     * The default supported url query string parameters names for every endpoint
     * It's an array as
     *
     * ```
     * array(
     *     'endpoint_1' => array('name_one'),
     *     'endpoint_2' => array('name_one', 'name_two'),
     *     ...
     * )
     * ```
     *
     * keys starting with '_' are special words that defines groups of string names to reuse in endpoints i.e.
     *
     * ```
     * array(
     *     '_groupOne' => array('name1', 'name2')
     *     'endpoint_1' => array('name_one', '_groupOne'), // it's like array('name_one', 'name1', 'name2')
     *     'endpoint_2' => array('_groupOne') // it's like array('name1', 'name2')
     * )
     * ```
     *
     * Key '__all' it's a special key that contains query string names valid for every endpoint and every request method.
     * Other endpoints params are only valid for GET requests.
     *
     * @var array
     */
    private $defaultAllowedUrlParams = array(
        '__all' => array('access_token'),
        '_pagination' => array('page', 'page_size'),
        'objects' => array('id', 'filter[object_type]', 'filter[substring]', 'filter[query]', 'embed[relations]', '_pagination'),
        'posters' => array('id', 'width', 'height', 'mode')
    );

    /**
     * Other supported query string parameters names for every endpoint.
     * Override it according to your needs.
     *
     * @see self::$defaultAllowedUrlParams to the right format
     * @var array
     */
    protected $allowedUrlParams = array();

    /**
     * Constructor
     *
     * - Add auth component (default 'ApiAuth') to self::$components
     * - Setup endpoints available:
     *  - Merge self::defaultEndPoints, self::endPoints
     *  - Add to endpoints object types whitelisted
     *  - remove blacklisted endpoints (self::blacklistEndPoints)
     */
    public function __construct() {
        Configure::write('Session.start', false);
        $authComponent = Configure::read('api.auth.component');
        if (empty($authComponent)) {
            $authComponent = 'ApiAuth';
        }
        $this->components[] = $authComponent;
        parent::__construct();
        $this->endPoints = array_unique(array_merge($this->defaultEndPoints, $this->endPoints));
        $objectTypeQueryString = array_diff($this->defaultAllowedUrlParams['objects'], array('filter[object_type]'));
        $objectTypes = Configure::read('objectTypes');
        foreach ($objectTypes as $key => $value) {
            if (is_numeric($key) && in_array($value['name'], $this->whitelistObjectTypes)) {
                $objectTypeEndPoint = Inflector::pluralize($value['name']);
                $this->endPoints[] = $objectTypeEndPoint;
                $this->defaultAllowedUrlParams[$objectTypeEndPoint] = $objectTypeQueryString;
            }
        }
        $this->endPoints = array_diff($this->endPoints, $this->blacklistEndPoints);

        // for backward compatibility with 3.6.0
        if (!empty($this->allowedObjectsFilter)) {
            $this->$allowedObjectsUrlPath = $this->allowedObjectsFilter;
        }
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
        if (!empty($objectType) && $this->requestMethod == 'get') {
            $this->objectsFilter['object_type_id'] = $objectType['id'];
            return call_user_func_array(array($this, $this->requestMethod . 'Objects'), $arguments);
        }

        throw new BeditaMethodNotAllowedException();
    }

    /**
     * Return the HTTP verb of the request
     *
     * @return string
     */
    public function getRequestMethod() {
        return $this->requestMethod;
    }

    /**
     * Normalize POST/PUT data
     *
     * This function searches for POST/PUT data in the global var $_POST and in 'php://input' alias file
     * Some Javascript XHR wrappers POSTs, PUTs data are passed through 'php://input'
     * If CONTENT_TYPE in request headers is 'application/x-www-form-urlencoded' then it parses string
     * else it tries to json encode string
     *
     * @return array
     */
    private function handleInputData() {
        if (!empty($this->params['form'])) {
            return;
        }

        $contentType = env('CONTENT_TYPE');
        if (strpos($contentType, 'application/x-www-form-urlencoded') === false
            && strpos($contentType, 'application/json') === false) {
            return;
        }

        try {
            $inputData = file_get_contents('php://input');
            if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
                parse_str($inputData, $this->params['form']);
            } else {
                $this->params['form'] = json_decode($inputData, true);
                $jsonError = json_last_error();
                if (!empty($jsonError)) {
                    $this->params['form'] = array();
                }
            }
        } catch (Exception $ex) {
            $this->params['form'] = array();
        }
        // set self::data
        if (!empty($this->params['form']['data'])) {
            $this->data = $this->params['form']['data'];
            unset($this->params['form']['data']);
        }
    }

    /**
     * Setup the pagination options self:paginationOptions
     * Merging default with query url params
     *
     * @return void
     */
    private function setupPagination() {
        $paramsUrl = $this->params['url'];
        if (isset($paramsUrl['page'])) {
            // check that 'page' is positive integer
            $intVal = (int) $paramsUrl['page'];
            $floatVal = (float) $paramsUrl['page'];
            if (!is_numeric($paramsUrl['page']) || $paramsUrl['page'] < 1 || $intVal != $floatVal) {
                throw new BeditaBadRequestException('page param must be a positive integer');
            }
            $this->paginationOptions['page'] = (int) $paramsUrl['page'];
        }

        if (isset($paramsUrl['page_size'])) {
            // check that 'page_size' is positive integer
            $intVal = (int) $paramsUrl['page_size'];
            $floatVal = (float) $paramsUrl['page_size'];
            if (!is_numeric($paramsUrl['page_size']) || $paramsUrl['page_size'] < 1 || $intVal != $floatVal) {
                throw new BeditaBadRequestException('page_size param must be a positive integer');
            }
            $this->paginationOptions['pageSize'] = (int) $paramsUrl['page_size'];
        }
        if ($this->paginationOptions['pageSize'] > $this->paginationOptions['maxPageSize']) {
            throw new BeditaBadRequestException('Max page_size supported is ' . $this->paginationOptions['maxPageSize']);
        }
        $this->paginationOptions['dim'] = $this->paginationOptions['pageSize'];
    }

    /**
     * Common operations that every call must do:
     *
     * - setup auth component
     * - check origin
     * - setup self::requestMethod to http verb used
     * - normalize post data
     *
     * If method is overridden in frontend ApiController remember to call parent::beforeCheckLogin()
     *
     * @return void
     */
    protected function beforeCheckLogin() {
        $this->setupAuthComponent();
        $this->setupValidatorComponent();
        $this->setupFormatterComponent();
        // Cross origin check.
        if (!$this->checkOrigin()) {
            throw new BeditaForbiddenException('Unallowed Origin');
        }

        $this->ResponseHandler->sendHeader('Access-Control-Allow-Methods', "POST, GET, PUT, DELETE, OPTIONS, HEAD");
        $acrh = env('HTTP_ACCESS_CONTROL_REQUEST_HEADERS');
        if (!$acrh && function_exists('getallheaders')) {
            $headers = getallheaders();
            if (!empty($headers['Access-Control-Request-Headers'])) {
                $acrh = $headers['Access-Control-Request-Headers'];
            }
        }
        if (!empty($acrh)) {
            $this->ResponseHandler->sendHeader('Access-Control-Allow-Headers', $acrh);
        }

        $this->requestMethod = strtolower(env('REQUEST_METHOD'));
        if ($this->requestMethod == 'post' || $this->requestMethod == 'put') {
            $this->handleInputData();
        } elseif ($this->requestMethod == 'options' || $this->requestMethod == 'head') {
            $this->_stop();
        }

        $this->setupPagination();
        $this->ApiValidator->registerAllowedUrlParams($this->defaultAllowedUrlParams);
        $this->ApiValidator->registerAllowedUrlParams($this->allowedUrlParams);
    }

    /**
     * Override FrontendController::checkLogin()
     *
     * @return mixed
     */
    protected function checkLogin() {
        if ($this->ApiAuth->identify()) {
            $this->logged = true;
        }
    }

    /**
     * Override FrontendController::checkPublicationPermissions()
     *
     * Return true if the publication is authorized for user
     * Return null if client is trying to authenticate via `POST /auth`
     *
     * @throws BeditaUnauthorizedException
     * @throws BeditaForbiddenException
     * @return mixed
     */
    protected function checkPublicationPermissions() {
        if ($this->publication['authorized']) {
            return true;
        }
        if ($this->params['pass']['0'] == 'auth' && $this->requestMethod == 'post' && in_array('auth', $this->endPoints)) {
            return null;
        }

        if (!$this->ApiAuth->identify()) {
            throw new BeditaUnauthorizedException();
        }
        throw new BeditaForbiddenException();
    }

    /**
     * Setup component used for authentication:
     *
     * - check configuration (api.auth.component) to see if adhoc component should be used and assign it to self::$ApiAuth
     * - replace self::BeAuth with self::ApiAuth to work properly in FrontendController.
     *   BeAuthComponent is not used in API context. JWT is usually used instead via ApiAuthComponent
     *
     * @return void
     */
    private function setupAuthComponent() {
        $componentName = Configure::read('api.auth.component');
        if (!empty($componentName) && $componentName != 'ApiAuth' && !empty($this->{$componentName})) {
            $this->ApiAuth = &$this->{$componentName};
        }
        if (empty($this->ApiAuth)) {
            throw new BeditaInternalErrorException('API auth component is not properly loaded in API controller');
        }
        $this->BeAuth = &$this->ApiAuth;
    }

    /**
     * Setup component used for validation:
     *
     * - check configuration (api.validator.component) to see if adhoc component should be used and assign it to self::$ApiValidator
     *
     * @return void
     */
    private function setupValidatorComponent() {
        $componentName = Configure::read('api.validator.component');
        if (!empty($componentName) && $componentName != 'ApiValidator' && !empty($this->{$componentName})) {
            $this->ApiValidator = &$this->{$componentName};
        }
        if (empty($this->ApiValidator)) {
            throw new BeditaInternalErrorException('API validator component is not properly loaded in API controller');
        }
    }

    /**
     * Setup component used for authentication:
     *
     * - check configuration (api.formatter.component) to see if adhoc component should be used and assign it to self::$ApiFormatter
     *
     * @return void
     */
    private function setupFormatterComponent() {
        $componentName = Configure::read('api.formatter.component');
        if (!empty($componentName) && $componentName != 'ApiFormatter' && !empty($this->{$componentName})) {
            $this->ApiFormatter = &$this->{$componentName};
        }
        if (empty($this->ApiFormatter)) {
            throw new BeditaInternalErrorException('API formatter component is not properly loaded in API controller');
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
        $this->responseData['url'] = Router::url('/', true) . $this->params['url']['url'];
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
     * @return void
     */
    protected function setData(array $data = array(), $merge = false) {
        $this->responseData['data'] = ($merge && isset($this->responseData['data'])) ? array_merge($this->responseData['data'], $data) : $data;
    }

    /**
     * set self::responseData['paging'] array used by self::response() to output pagination data
     *
     * @param array $paginationData
     * @return void
     */
    protected function setPaging(array $paginationData) {
        $this->responseData['paging'] = $paginationData;
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
        $methodName = str_replace('.', '_', $name);
        if (!empty($methodName)) {
            // avoid to call methods that aren't endpoints
            if (!in_array($methodName, $this->endPoints)) {
                $this->action = $methodName;
                throw new BeditaMethodNotAllowedException();
            } else {
                $this->ApiValidator->checkUrlParams($methodName);
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
            return $this->response(array('setBase' => false));
        }

        if ($this->autoResponse) {
            $this->response();
        }
    }

    /**
     * Return the full or partial API base url
     * If $full is true set self::fullApiBaseUrl too and reuse it for the next time
     *
     * @param boolean $full if the url should be complete or partial
     * @return string
     */
    public function baseUrl($full = true) {
        if (!$full) {
            return Configure::read('api.baseUrl');
        }
        if (!$this->fullApiBaseUrl) {
            $baseUrl = Configure::read('api.baseUrl');
            $url = Router::url($baseUrl, true);
            $this->fullApiBaseUrl = trim($url, '/');
        }
        return $this->fullApiBaseUrl;
    }

    /**
     * prepare response data for base api url
     *
     * default response: show list of available endpoints with urls
     * override in subclasses for custom response
     */
    protected function baseUrlResponse() {
        foreach ($this->endPoints as $endPoint) {
            $this->responseData[$endPoint] = $this->baseUrl() . '/' . $endPoint;
        }
    }

    /**
     * setup self::$objectsFilter from url params
     *
     * @return void
     */
    protected function setupObjectsFilter() {
        $urlParams = $this->ApiFormatter->formatUrlParams();
        if (!empty($urlParams['filter'])) {
            $this->objectsFilter = array_merge($this->objectsFilter, $urlParams['filter']);
        }
    }

    /**
     * GET /objects
     *
     * If $name is passed try to load an object with that id or nickname
     *
     * @param int|string $name an object id or nickname
     * @param string $filterType can be a value between those defined in self::allowedObjectsUrlPath['get']
     * @return void
     */
    protected function getObjects($name = null, $filterType = null) {
        $this->setupObjectsFilter();
        $urlParams = $this->ApiFormatter->formatUrlParams();
        if (!empty($name)) {
            // GET /objects/:id supports only '__all' params
            if (empty($filterType)) {
                $this->ApiValidator->setAllowedUrlParams('objects', array('embed[relations]', '__all'), false);
                if (!$this->ApiValidator->isUrlParamsValid('objects')) {
                    $validParams = implode(', ', $this->ApiValidator->getAllowedUrlParams('objects'));
                    throw new BeditaBadRequestException(
                        'GET /objects/:id supports url params: ' . $validParams
                    );
                }
            }
            // GET /objects/:id/$filterType?id=x not valid
            if (array_key_exists('id', $this->params['url'])) {
                throw new BeditaBadRequestException(
                    'GET /objects/:id/' . $filterType . ' does not support url params: id'
                );
            }
            // check embed[relations] params
            if (!empty($urlParams['embed']['relations'])) {
                $this->ApiValidator->checkEmbedRelations(
                    $urlParams['embed']['relations'],
                    1,
                    $this->paginationOptions['maxPageSize']
                );
            }
            $id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
            $this->ApiValidator->checkObjectReachable($id);
            if (!empty($filterType)) {
                $args = func_get_args();
                $args[0] = $id;
                call_user_func_array(array($this, 'routeObjectsFilterType'), $args);
            } else {
                $options = array('explodeRelations' => false);
                if (!empty($this->params['url']['binding']) && in_array($this->params['url']['binding'], $this->allowedModelBindings)) {
                    $options['bindingLevel'] = $this->params['url']['binding'];
                }
                $object = $this->loadObj($id, true, $options);
                if ($object == parent::UNLOGGED) {
                    throw new BeditaUnauthorizedException();
                }
                if ($object == parent::UNAUTHORIZED) {
                    throw new BeditaForbiddenException();
                }
                // check if id corresponds to object type requested (if any)
                if (!empty($this->objectsFilter['object_type_id']) && $object['object_type_id'] != $this->objectsFilter['object_type_id']) {
                    throw new BeditaInternalErrorException('Object type mismatch');
                }

                $object = $this->ApiFormatter->formatObject(
                    $object,
                    array('countRelations' => true, 'countChildren' => true)
                );
                if (!empty($urlParams['embed']['relations'])) {
                    $object['object'] = $this->addRelatedObjects($object['object'], $urlParams['embed']['relations']);
                }
                $this->setData($object);
            }
        } else {
            // get list of object ids (check reachability)
            if (!empty($urlParams['id'])) {
                $this->ApiValidator->setAllowedUrlParams('objects', array('id', 'embed[relations]', '__all'), false);
                if (!$this->ApiValidator->isUrlParamsValid('objects')) {
                    $validParams = implode(', ', $this->ApiValidator->getAllowedUrlParams('__all'));
                    throw new BeditaBadRequestException(
                        'GET /objects?id=xx,yy,... supports only these other url params: ' . $validParams
                    );
                }
                $ids = is_array($urlParams['id']) ? $urlParams['id'] : array($urlParams['id']);
                if (count($ids) > $this->paginationOptions['maxPageSize']) {
                    throw new BeditaBadRequestException('Too objects requested. Max is ' . $this->paginationOptions['maxPageSize']);
                }
                // check embed[relations] params
                if (!empty($urlParams['embed']['relations'])) {
                    $this->ApiValidator->checkEmbedRelations(
                        $urlParams['embed']['relations'],
                        count($ids),
                        $this->paginationOptions['maxPageSize']
                    );
                }
                $objects = array();
                foreach ($ids as $id) {
                    $this->ApiValidator->checkObjectReachable($id);
                    $objects[] = $this->loadObj($id, true, array('explodeRelations' => false));
                }
                $objects = $this->ApiFormatter->formatObjects(
                    $objects,
                    array('countRelations' => true, 'countChildren' => true)
                );
                if (!empty($urlParams['embed']['relations'])) {
                    foreach ($objects['objects'] as &$o) {
                        $o = $this->addRelatedObjects($o, $urlParams['embed']['relations']);
                    }
                }
                $this->setData($objects);
            // list of publication descendants
            } else {
                // check embed[relations] params
                if (!empty($urlParams['embed']['relations'])) {
                    $this->ApiValidator->checkEmbedRelations(
                        $urlParams['embed']['relations'],
                        $this->paginationOptions['pageSize'],
                        $this->paginationOptions['maxPageSize']
                    );
                }
                $publication = $this->getPublication();
                $this->responseChildren($publication['id'], array(
                    'filter' => array('descendants' => true)
                ));
            }
        }
    }

    /**
     * Add related objects to $object
     * The $relations is an array that contains info
     * about the number of objects to get for each relation
     * For example
     *
     * ```
     * array(
     *     'attach' => 5,
     *     'seealso' => 2,
     *     'poster' => 1
     * )
     * ```
     *
     * @param array $object the object
     * @param array $relations the relations info
     * @return array
     */
    protected function addRelatedObjects(array $object, array $relations) {
        foreach ($relations as $relName => $dim) {
            if ($this->ApiValidator->isRelationValid($relName, $object['object_type'])) {
                $relObj = $this->loadRelatedObjects($object['id'], $relName, array('dim' => $dim));
                if (!empty($relObj['items'])) {
                    $relObjs = $this->ApiFormatter->formatObjects(
                        $relObj['items'],
                        array('countRelations' => true, 'countChildren' => true)
                    );
                    $object['relations'][$relName]['objects'] = $relObjs['objects'];
                }
            }
        }
        return $object;
    }

    /**
     * Route calls made by /objects endpoint using $filterType and request method self::requestMethod
     *
     * @see self:objects() for param description
     * @param int $id
     * @param string $filterType
     * @return void
     */
    private function routeObjectsFilterType($id, $filterType) {
        if (empty($this->allowedObjectsUrlPath[$this->requestMethod])) {
            throw new BeditaMethodNotAllowedException();
        }
        $allowedFilterTypes = $this->allowedObjectsUrlPath[$this->requestMethod];
        if (!in_array($filterType, $allowedFilterTypes)) {
            $allowedFilter = implode(', ', $allowedFilterTypes);
            throw new BeditaBadRequestException($filterType . ' not valid. Valid options are: ' . $allowedFilter);
        }
        $method = $this->requestMethod . 'Objects' . Inflector::camelize($filterType);
        $args = func_get_args();
        unset($args[1]);
        return call_user_func_array(array($this, $method), $args);
    }

    /**
     * POST /objects
     *
     * @param int|string $name the object id or nickname
     * @param string $filterType can be a value between those defined in self::allowedObjectsUrlPath['post']
     * @return void
     */
    protected function postObjects($name = null, $filterType = null) {
        if (!$this->ApiAuth->identify()) {
            throw new BeditaUnauthorizedException();
        }

        if (empty($this->data)) {
            throw new BeditaBadRequestException('Missing data to save');
        }

        if (!empty($name)) {
            if (func_num_args() == 1) {
                throw new BeditaMethodNotAllowedException('POST /objects/:id is not supported');
            }
            $id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
            $this->ApiValidator->checkObjectReachable($id);
            $args = func_get_args();
            $args[0] = $id;
            call_user_func_array(array($this, 'routeObjectsFilterType'), $args);
            return;
        }

        $isNew = (empty($this->data['id'])) ? true : false;
        if ($isNew) {
            if (empty($this->data['object_type'])) {
                throw new BeditaBadRequestException('Missing object_type');
            }
            $confType = $this->data['object_type'];

            if (array_key_exists('upload_token', $this->data)) {
                $uploadToken = $this->data['upload_token'];
                unset($this->data['upload_token']);
                $this->data = array_merge(
                    $this->data,
                    $this->ApiUpload->uploadedFileData($uploadToken, $confType)
                );
            }
        } else {
            $confType = $this->BEObject->findObjectTypeId($this->data['id']);
        }

        $objectTypeConf = Configure::read('objectTypes.' . $confType);
        if (empty($objectTypeConf)) {
            throw new BeditaBadRequestException('Invalid object type');
        }
        $objectModel = $this->loadModelByType($objectTypeConf['model']);

        $this->Transaction->begin();
        $this->saveObject($objectModel);
        $savedObjectId = $objectModel->id;
        if (isset($uploadToken) && !$this->ApiUpload->removeToken($uploadToken)) {
            throw new BeditaInternalErrorException('Error removing upload token');
        }
        $this->Transaction->commit();

        $this->getObjects($savedObjectId);
        if ($isNew) {
            $this->ResponseHandler->sendStatus(201);
            $this->ResponseHandler->sendHeader('Location', $this->baseUrl() . '/objects/' . $savedObjectId);
        }
    }

    /**
     * PUT /objects/:id
     * PUT of entire object is not allowed. If you want modify an object you should use POST
     *
     * @param int|string $name the object id or nickname
     * @param string $filterType can be a value between those defined in self::allowedObjectsUrlPath['put']
     * @return void
     */
    protected function putObjects($name = null, $filterType = null) {
        if (!$this->ApiAuth->identify()) {
            throw new BeditaUnauthorizedException();
        }
        if (empty($name) || empty($filterType)) {
            throw new BeditaBadRequestException();
        }
        $id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
        $this->ApiValidator->checkObjectReachable($id);
        $args = func_get_args();
        $args[0] = $id;
        call_user_func_array(array($this, 'routeObjectsFilterType'), $args);
    }

    /**
     * DELETE /objects/:id
     *
     * @param int|string $name
     * @param string $filterType can be a value between those defined in self::allowedObjectsUrlPath['delete']
     * @return void
     */
    protected function deleteObjects($name = null, $filterType = null) {
        if (!$this->ApiAuth->identify()) {
            throw new BeditaUnauthorizedException();
        }
        if (empty($name)) {
            throw new BeditaMethodNotAllowedException('Unsupported endpoint for DELETE request. It should be /objects/:id');
        }
        if (!empty($this->params['form']) || !empty($this->data)) {
            throw new BeditaBadRequestException('DELETE do not support input data');
        }
        $id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
        $this->ApiValidator->checkObjectReachable($id);
        // delete object
        if (empty($filterType)) {
            $modelName = $this->BEObject->getType($id);
            $this->data['id'] = $id;
            parent::deleteObjects($modelName);
            $this->emptyResponse();
        } else {
            $args = func_get_args();
            $args[0] = $id;
            call_user_func_array(array($this, 'routeObjectsFilterType'), $args);
        }
    }

    /**
     * Override AppController::saveObject()
     *
     * - set default $options different from AppController::saveObject()
     * - set additional data (status, user_created, user_modified, object_type_id,...)
     * - check object data through ApiValidator
     * - format object data through ApiFormatter
     * - save object using parent::saveObject()
     * - save parents in case (remove old parents and add new one)
     * - save relations in case
     *
     * @param BEAppModel $beModel
     * @param array $options
     * @return void
     */
    protected function saveObject(BEAppModel $beModel, array $options = array()) {
        $user = $this->ApiAuth->identify();
        if (!$user) {
            throw new BeditaUnauthorizedException();
        }
        $options += array(
            'handleTagList' => false,
            'emptyPermission' => false,
            'saveTree' => false
        );

        if (empty($this->data['object_type_id'])) {
            if (empty($this->data['object_type'])) {
                if (empty($this->data['id'])) {
                    throw new BeditaBadRequestException('Missing object type or it can not be retrieved');
                }
                $this->data['object_type_id'] = $this->BEObject->findObjectTypeId($this->data['id']);
            } else {
                $this->data['object_type_id'] = Configure::read('objectTypes.' . $this->data['object_type'] . '.id');
            }
        }

        $this->data['status'] = 'on';
        if (empty($this->data['id'])) {
            $this->data['user_created'] = $user['id'];
        }
        $this->data['user_modified'] = $user['id'];

        // validate and format data for save
        $this->ApiValidator->checkObject($this->data);
        $this->data = $this->ApiFormatter->formatObjectForSave($this->data);

        // #883 skip tree update when saving section
        if ($this->data['object_type'] == 'section') {
            $this->data['skipTreeUpdate'] = true;
        }

        parent::saveObject($beModel, $options);

        // save parents
        if (!empty($this->data['parents'])) {
            $tree = ClassRegistry::init('Tree');
            $tree->updateTree(
                $beModel->id,
                $this->data['parents'],
                array(
                    'area_id' => $this->publication['id'],
                    'status' => $this->getStatus()
                )
            );

            if ($this->data['object_type'] == 'section') {
                $menu = (isset($this->data[$this->name]['menu']) && $this->data[$this->name]['menu'] == 0) ? 0 : 1;
                $tree->saveMenuVisibility($beModel->id, $this->data['parents'][0], $menu);
            }

            $this->BEObject->clearCacheByIds($this->data['parents']);
        }

        // save custom properties
        if (!empty($this->data['custom_properties'])) {
            $propertyIds = Set::extract('/property_id', $this->data['custom_properties']);
            // delete previous custom properties
            $delRes = $this->BEObject->ObjectProperty->deleteAll(array(
                'property_id' => $propertyIds,
                'object_id' => $beModel->id
            ));
            if (!$delRes) {
                throw BeditaInternalErrorException('Error saving custom properties');
            }
            foreach ($this->data['custom_properties'] as $customProp) {
                // save not null custom properties
                if ($customProp['property_value'] !== null) {
                    $customProp['object_id'] = $beModel->id;
                    $this->BEObject->ObjectProperty->create();
                    if (!$this->BEObject->ObjectProperty->save($customProp)) {
                        throw new BeditaInternalErrorException('Error saving custom property ' . $customProp['property_id']);
                    }
                }
            }
        }
    }

    /**
     * Save relations $relationName between $objectId and related objects in $this->data
     *
     * If you want to save only one relation $this->data should be
     * ```
     * array(
     *     'related_id' => 10, // required
     *     'priority' => 1, // optional
     *     'params' => array() // optional
     * )
     * ```
     *
     * If you want to save many relations $this->data should be
     * ```
     * array(
     *     array(
     *         'related_id' => 10, // required
     *         'priority' => 1, // optional
     *         'params' => array() // optional
     *     ),
     *     array(...)
     * )
     * ```
     *
     * @see ApiValidatorComponent::checkRelations()
     * @param int $objectId the main object id
     * @param string $relationName the relation name (direct or inverse)
     * @return void
     */
    protected function postObjectsRelations($objectId, $relationName) {
        if (func_num_args() > 2) {
            throw new BeditaBadRequestException();
        }
        $objectTypeId = $this->BEObject->findObjectTypeId($objectId);
        $this->data = isset($this->data[0]) ? $this->data : array($this->data);
        $this->ApiValidator->checkRelations(
            array($relationName => $this->data),
            $objectTypeId
        );
        $objectRelation = ClassRegistry::init('ObjectRelation');
        $inverseName = $objectRelation->inverseOf($relationName);
        $created = false;
        $responseData = array();
        $this->Transaction->begin();
        foreach ($this->data as $relData) {
            $exists = $objectRelation->relationExists($objectId, $relData['related_id'], $relationName);
            // create
            if (!$exists) {
                $params = isset($relData['params']) ? $relData['params'] : array();
                $priority = isset($relData['priority']) ? $relData['priority'] : null;
                $result = $objectRelation->createRelationAndInverse($objectId, $relData['related_id'], $relationName, $inverseName, $priority, $params);
                if ($result === false) {
                    throw new BeditaInternalErrorException(
                        'Error saving relation ' . $relationName . ' between ' . $objectId . ' and ' . $relData['related_id']
                    );
                }
                $created = true;
            // update
            } else {
                // update direct (params and priority) and inverse (only params) relation
                $set = array();
                if (array_key_exists('params', $relData)) {
                    $set['params']  = $relData['params'];
                }
                if (array_key_exists('priority', $relData)) {
                    $set['priority'] = $relData['priority'];
                }
                if (array_key_exists('params', $set) || array_key_exists('priority', $set)) {
                    $result = $objectRelation->updateRelation($objectId, $relData['related_id'], $relationName, $set);
                    if ($result === false) {
                        throw new BeditaInternalErrorException(
                            'Error updating relation ' . $relationName . ' between ' . $objectId . ' and ' . $relData['related_id']
                        );
                    }
                }
            }

            // get added/updated relations to build response for client
            $result = $objectRelation->find('first', array(
                'conditions' => array(
                    'id' => $objectId,
                    'object_id' => $relData['related_id'],
                    'switch' => $relationName
                )
            ));
            if (empty($result)) {
                throw new BeditaInternalErrorException(
                    'Error fetching relation ' . $relationName . ' between ' . $objectId . ' and ' . $relData['related_id']
                );
            }
            $d = array(
                'related_id' => (int) $result['ObjectRelation']['object_id'],
                'priority' => (int) $result['ObjectRelation']['priority']
            );
            if (!empty($result['ObjectRelation']['params'])) {
                $d['params'] = $result['ObjectRelation']['params'];
            }
            $responseData[] = $d;
        }
        $this->Transaction->commit();
        if ($created) {
            $this->ResponseHandler->sendStatus(201);
            $this->ResponseHandler->sendHeader('Location', $this->baseUrl() . '/objects/' . $objectId .'/relations/' . $relationName);
        }
        $this->setData($responseData);
    }

    /**
     * Save (insert or update) children ($this->data) of $objectId
     *
     * If you want to save only one child $this->data should be
     * ```
     * array(
     *     'child_id' => 10,
     *     'priority' => 1
     * )
     * ```
     *
     * If you want to save children $this->data should be
     * ```
     * array(
     *     array(
     *         'child_id' => 10,
     *         'priority' => 1
     *     ),
     *     array(...)
     * )
     * ```
     *
     * @see ApiValidatorComponent::checkChildren() to see the right format
     * @param int $objectId the object id
     * @return void
     */
    protected function postObjectsChildren($objectId) {
        if (func_num_args() > 1) {
            throw new BeditaBadRequestException();
        }
        $this->data = isset($this->data[0]) ? $this->data : array($this->data);
        $this->ApiValidator->checkChildren($this->data, $objectId);
        // append children
        $this->Transaction->begin();
        $tree = ClassRegistry::init('Tree');
        $created = false;
        $responseData = array();
        foreach ($this->data as $key => $child) {
            $row = $tree->find('first', array(
                'conditions' => array(
                    'parent_id' => $objectId,
                    'id' => $child['child_id']
                )
            ));
            // append child (insert)
            if (empty($row)) {
                $priority = !empty($child['priority']) ? $child['priority'] : null;
                if (!$tree->appendChild($child['child_id'], $objectId, $priority)) {
                    throw new BeditaInternalErrorException('Error appending ' . $child['child_id'] . ' to ' . $objectId);
                }
                $created = true;
            // update priority if any and different from current value
            } elseif (!empty($child['priority']) && $child['priority'] != $row['Tree']['priority']) {
                $row['Tree']['priority'] = $child['priority'];
                if (!$tree->save($row)) {
                    throw new BeditaInternalErrorException('Error updating priority ' . $priority . ' for child ' . $child['child_id']);
                }
            }

            $this->BEObject->clearCacheByIds(array($child['child_id']));

            $d = array('child_id' => $child['child_id']);
            // get current priority to prepare response
            $d['priority'] = empty($child['priority']) ? $tree->getPriority($child['child_id'], $objectId) : $child['priority'];
            $d['priority'] = (int) $d['priority'];
            $responseData[] = $d;

        }
        $this->BEObject->clearCacheByIds(array($objectId));
        $this->Transaction->commit();
        if ($created) {
            $this->ResponseHandler->sendStatus(201);
            $this->ResponseHandler->sendHeader('Location', $this->baseUrl() . '/objects/' . $objectId .'/children');
        }
        $this->setData($responseData);
    }

    /**
     * Update relation $relationName between $objectId and $relatedId objects
     *
     * $this->data should be
     * ```
     * array(
     *     'priority' => 1,
     *     'params' => array()
     * )
     * ```
     *
     * If 'priority' or 'params' is not passed then they are set to null to update db field to NULL.
     * Indeed PUT replaces all relation data with new one
     *
     * @param int $objectId the main object id
     * @param string $relationName the relation name (direct or inverse)
     * @param int $relatedId the related object id
     * @return void
     */
    protected function putObjectsRelations($objectId, $relationName = null, $relatedId = null) {
        if (func_num_args() != 3) {
            throw new BeditaBadRequestException();
        }
        if (empty($this->data['priority']) && empty($this->data['params'])) {
            throw new BeditaBadRequestException('No data to use in PUT request');
        }
        $this->ApiValidator->checkPositiveInteger($relatedId, true);
        $this->data['related_id'] = (int) $relatedId;
        $objectTypeId = $this->BEObject->findObjectTypeId($objectId);
        $this->ApiValidator->checkRelations(
            array($relationName => array($this->data)),
            $objectTypeId
        );

        $objectRelation = ClassRegistry::init('ObjectRelation');
        $exists = $objectRelation->relationExists($objectId, $relatedId, $relationName);
        if (!$exists) {
            throw new BeditaBadRequestException('You can not modify a relation that not exists.');
        }

        // set default value to null if not defined
        if (empty($this->data['priority'])) {
            $this->data['priority'] = null;
        }
        if (empty($this->data['params'])) {
            $this->data['params'] = null;
        }

        $this->Transaction->begin();
        $result = $objectRelation->updateRelation($objectId, $relatedId, $relationName, $this->data);
        if ($result === false) {
            throw new BeditaInternalErrorException(
                'Error updating relation ' . $relationName . ' between ' . $objectId . ' and ' . $relatedId
            );
        }
        $this->Transaction->commit();
        $this->getObjectsRelations($objectId, $relationName, $relatedId);
    }

    /**
     * Update 'priority' (position relative to all children) of $childId son of $objectId
     *
     * $this->data should be
     * ```
     * array(
     *     'priority' => 1
     * )
     * ```
     *
     * If 'priority' is not passed then a 400 is thrown
     * $childId must already be a child of $objectId
     *
     *
     * @param int $objectId the parent object id
     * @param int $childId the child object id
     * @return void
     */
    protected function putObjectsChildren($objectId, $childId = null) {
        if (func_num_args() != 2) {
            throw new BeditaBadRequestException();
        }
        if (empty($this->data['priority'])) {
            throw new BeditaBadRequestException('No data to use in PUT request');
        }
        $this->ApiValidator->checkPositiveInteger($childId, true);
        $this->data['child_id'] = (int) $childId;
        $this->ApiValidator->checkChildren(array($this->data), $objectId);

        $tree = ClassRegistry::init('Tree');
        $row = $tree->find('first', array(
            'conditions' => array(
                'parent_id' => $objectId,
                'id' => $childId
            )
        ));
        // if $objectId is not a parent of $childId throw 400
        if (empty($row)) {
            throw new BeditaBadRequestException($childId . ' must be a child of ' . $objectId);
        }
        $row['Tree']['priority'] = $this->data['priority'];
        if (!$tree->save($row)) {
            throw new BeditaInternalErrorException('Error updating priority');
        }

        $this->BEObject->clearCacheByIds(array($objectId, $childId));
        $this->getObjectsChildren($objectId, $childId);
    }

    /**
     * Delete a relation named $relation between $objectId and $relatedId
     *
     * @param int $objectId the object id
     * @param string $relation the relation name
     * @param int $relatedId the related id
     * @return void
     */
    protected function deleteObjectsRelations($objectId, $relation, $relatedId) {
        if (func_num_args() != 3) {
            throw new BeditaBadRequestException();
        }
        $this->ApiValidator->checkPositiveInteger($relatedId, true);
        $objectTypeId = $this->BEObject->findObjectTypeId($objectId);
        if (!$this->ApiValidator->isRelationValid($relation, $objectTypeId)) {
            throw new BeditaBadRequestException($relation . ' is not valid for object id ' . $objectId);
        }
        $objectRelation = ClassRegistry::init('ObjectRelation');
        $exists = $objectRelation->relationExists($objectId, $relatedId, $relation);
        if (!$exists) {
            throw new BeditaNotFoundException('Relation ' . $relation . ' between ' . $objectId . ' and ' . $relatedId . ' not found');
        }
        if (!$objectRelation->deleteRelationAndInverse($objectId, $relatedId, $relation)) {
            throw new BeditaInternalErrorException();
        }
        $this->emptyResponse();
    }

    /**
     * Delete from trees object $childId with $parentId as parent
     *
     * @param int $parentId the object parent id
     * @param int $childId the object child id
     * @return void
     */
    protected function deleteObjectsChildren($parentId, $childId) {
        if (func_num_args() != 2) {
            throw new BeditaBadRequestException();
        }
        $this->ApiValidator->checkPositiveInteger($childId, true);
        $objectTypeId = $this->BEObject->findObjectTypeId($childId);
        if ($objectTypeId == Configure::read('objectTypes.section.id')) {
            throw new BeditaBadRequestException('Section ' .$childId . ' can not be removed from parents');
        }
        $this->ApiValidator->checkObjectAccess($childId);
        $tree = ClassRegistry::init('Tree');
        $count = $tree->find('count', array(
            'conditions' => array(
                'id' => $childId,
                'parent_id' => $parentId
            )
        ));
        if (!$count) {
            throw new BeditaNotFoundException($childId . ' is not child of ' . $parentId);
        }
        if (!$tree->removeChild($childId, $parentId)) {
            throw new BeditaInternalErrorException();
        }

        $this->BEObject->clearCacheByIds(array($parentId, $childId));
        $this->emptyResponse();
    }

    /**
     * Get list of parent children with access restricted to $user
     *
     * @param int $parentId the parent id
     * @param array $user array with user data, empty if no user is logged
     * @return array list of forbidden object ids, may be empty
     */
    protected function forbiddenChildren($parentId, array $user = array()) {
        // add conditions on not accessible objects (frontend_access_with_block)
        // @todo move to FrontendController::loadSectionObjects()?
        $objectsForbidden = array();
        $childrenForbidden = false;
        if ($this->BeObjectCache) {
            $cacheOpts = array();
            $childrenForbidden = $this->BeObjectCache->read($parentId, $cacheOpts, 'children-forbidden');
        }
        if ($childrenForbidden === false) {
            $treeJoin = array(
                'table' => 'trees',
                'alias' => 'Tree',
                'type' => 'inner',
                'conditions' => array(
                    'Tree.id = Permission.object_id',
                    'Tree.parent_id' => $parentId,
                )
            );

            $fields = array('Tree.id', 'Permission.ugid');
            $conditions = array(
                'Permission.flag' => Configure::read('objectPermissions.frontend_access_with_block'),
                'Permission.switch' => 'group',
            );

            $permission = ClassRegistry::init('Permission');
            $perms = $permission->find('all', array(
                'fields' => $fields,
                'joins' => array($treeJoin),
                'conditions' => $conditions,
            ));

            $childrenForbidden = array();
            foreach ($perms as $value) {
                $objId = $value['Tree']['id'];
                $childrenForbidden[$objId][] = $value['Permission']['ugid'];
            }

            if ($this->BeObjectCache) {
                $this->BeObjectCache->write($parentId, $cacheOpts, $childrenForbidden, 'children-forbidden');
            }
        }

        // check user allowed if there are forbidden objs
        if (!empty($childrenForbidden)) {
            if (!empty($user)) {
                $groupIds = (!empty($user['groupsIds'])) ? $user['groupsIds'] : array();
                foreach ($childrenForbidden as $id => $groups) {
                    $intersect = array_intersect($groups, $groupIds); 
                    if (empty($intersect)) {
                        $objectsForbidden[] = $id;
                    }
                }
            } else {
                $objectsForbidden = array_keys($childrenForbidden);
            }
        }
        return $objectsForbidden;
    }

    /**
     * Get children of $parentId object, prepare and set response data
     * The response is automatically paginated using self::paginationOptions
     * self::$objectsFilter is used to populate $options['filter']
     *
     * @see FrontendController::loadSectionObjects()
     * @param int $parentId the parent id
     * @param array $options an array of options for filter results
     * @return void
     */
    protected function responseChildren($parentId, array $options = array()) {
        $defaultOptions = array('explodeRelations' => false);
        $options = array_merge($defaultOptions, $this->paginationOptions, $options);
        $options['filter'] = !empty($options['filter']) ? array_merge($this->objectsFilter, $options['filter']) : $this->objectsFilter;
        // assure to have result in 'children' key
        $options['itemsTogether'] = true;
        $user = $this->ApiAuth->getUser();
        $objectsForbidden = $this->forbiddenChildren($parentId, $user);
        if (!empty($objectsForbidden)) {
            $options['filter']['NOT']['BEObject.id'] = $objectsForbidden;
        }

        $result = $this->loadSectionObjects($parentId, $options);
        if (empty($result['children'])) {
            $this->setData();
        } else {
            $objects = $this->ApiFormatter->formatObjects(
                $result['children'],
                array('countRelations' => true, 'countChildren' => true)
            );
            // embed related objects if request
            $urlParams = $this->ApiFormatter->formatUrlParams();
            if (!empty($urlParams['embed']['relations'])) {
                foreach ($objects['objects'] as &$o) {
                    $o = $this->addRelatedObjects($o, $urlParams['embed']['relations']);
                }
            }
            $this->setData($objects);
            $this->setPaging($this->ApiFormatter->formatPaging($result['toolbar']));
        }
    }

    /**
     * Load children of object $id setting data for response
     *
     * @param int $id
     * @return void
     */
    protected function getObjectsChildren($id, $childId = null) {
        if (func_num_args() > 2) {
            throw new BeditaBadRequestException();
        }
        // get list of children
        if (empty($childId)) {
            $this->responseChildren($id);
        // get children position i.e. 'priority' value
        } else {
            if (!$this->ApiValidator->isUrlParamsValid('__all')) {
                $validParams = implode(', ', $this->ApiValidator->getAllowedUrlParams('__all'));
                throw new BeditaBadRequestException(
                    'GET /objects/:id/children/:child_id supports url params: ' . $validParams
                );
            }
            $priority = ClassRegistry::init('Tree')->getPriority($childId, $id);
            if (empty($priority)) {
                throw new BeditaNotFoundException();
            }
            $this->setData(array(
                'priority' => (int) $priority
            ));
        }
    }

    /**
     * Load sections children of object $id setting data for response
     *
     * @param int $id
     * @return void
     */
    protected function getObjectsSections($id) {
        if (func_num_args() > 1) {
            throw new BeditaBadRequestException();
        }
        if (isset($this->objectsFilter['object_type'])) {
            throw new BeditaBadRequestException('GET /objects/:id/sections does not support filter[object_type] param');
        }
        $sectionObjectTypeId = Configure::read('objectTypes.section.id');
        $result = $this->responseChildren($id, array(
            'filter' => array(
                'BEObject.object_type_id' => array($sectionObjectTypeId)
            )
        ));
    }

    /**
     * Load contents children of object $id setting data for response
     *
     * @param int $id
     * @return void
     */
    protected function getObjectsContents($id) {
        if (func_num_args() > 1) {
            throw new BeditaBadRequestException();
        }
        if (!empty($this->objectsFilter['object_type'])) {
            $ot = is_array($this->objectsFilter['object_type']) ? $this->objectsFilter['object_type'] : array($this->objectsFilter['object_type']);
            if (in_array('section', $ot)) {
                throw new BeditaBadRequestException('GET /objects/:id/contents does not support filter[object_type] section value');
            }
        }
        $sectionObjectTypeId = Configure::read('objectTypes.section.id');
        $result = $this->responseChildren($id, array(
            'filter' => array(
                'NOT' => array(
                    'BEObject.object_type_id' => array($sectionObjectTypeId)
                )
            )
        ));
    }

    /**
     * Load descendants of object $id setting data for response
     *
     * @param int $id
     * @return void
     */
    protected function getObjectsDescendants($id) {
        if (func_num_args() > 1) {
            throw new BeditaBadRequestException();
        }
        $this->responseChildren($id, array(
            'filter' => array('descendants' => true)
        ));
    }

    /**
     * Load siblings of object $id setting data for response
     *
     * @param int $id
     * @return void
     */
    protected function getObjectsSiblings($id) {
        if (func_num_args() > 1) {
            throw new BeditaBadRequestException();
        }
        // get only first parent?
        $parentIds = ClassRegistry::init('Tree')->getParents($id, $this->publication['id'], $this->getStatus());
        if (empty($parentIds)) {
            throw new BeditaNotFoundException('The object ' . $id . ' have no parents');
        }
        $this->responseChildren($parentIds[0], array(
            'filter' => array('NOT' => array('BEObject.id' => $id))
        ));
    }

    /**
     * Load relations of object $id setting data for response
     *
     * @param int $id the main object id
     * @param string $relation the relation name
     * @param int $relatedId the related object id
     * @return void
     */
    protected function getObjectsRelations($id, $relation = null, $relatedId = null) {
        if (func_num_args() > 3) {
            throw new BeditaBadRequestException();
        }
        // count relations of object $id
        if ($relation === null) {
            $relCount = $this->ApiFormatter->formatRelationsCount(array('id' => $id));
            $this->setData($relCount);
        } else {
            $objectTypeId = $this->BEObject->findObjectTypeId($id);
            if (!$this->ApiValidator->isRelationValid($relation, $objectTypeId)) {
                throw new BeditaBadRequestException($relation . ' is not valid for object id ' . $id);
            }

            // detail of related objects
            if ($relatedId === null) {
                $defaultOptions = array(
                    'explodeRelations' => false,
                    'filter' => $this->objectsFilter
                );
                $options = array_merge($defaultOptions, $this->paginationOptions);
                $result = $this->loadRelatedObjects($id, $relation, $options);
                if (empty($result['items'])) {
                    $this->setData();
                } else {
                    $objects = $this->ApiFormatter->formatObjects(
                        $result['items'],
                        array('countRelations' => true)
                    );
                    $this->setData($objects);
                    $this->setPaging($this->ApiFormatter->formatPaging($result['toolbar']));
                }
            // relation detail (params and priority)
            } else {
                if (!$this->ApiValidator->isUrlParamsValid('__all')) {
                    $validParams = implode(', ', $this->ApiValidator->getAllowedUrlParams('__all'));
                    throw new BeditaBadRequestException(
                        'GET /objects/:id/relations/:rel_name/:related_id supports url params: ' . $validParams
                    );
                }
                $this->ApiValidator->checkPositiveInteger($relatedId, true);
                $objectTypeId = $this->BEObject->findObjectTypeId($id);
                if (!$this->ApiValidator->isRelationValid($relation, $objectTypeId)) {
                    throw new BeditaBadRequestException($relation . ' is not valid for object id ' . $id);
                }
                $objectRelation = ClassRegistry::init('ObjectRelation');
                $relationData = $objectRelation->find('first', array(
                    'conditions' => array(
                        'switch' => $relation,
                        'id' => $id,
                        'object_id' => $relatedId
                    )
                ));
                if (empty($relationData['ObjectRelation'])) {
                    throw new BeditaNotFoundException();
                }
                $data = array();
                $data['priority'] = (int) $relationData['ObjectRelation']['priority'];
                if (!empty($relationData['ObjectRelation']['params'])) {
                    $data['params'] = $relationData['ObjectRelation']['params'];
                }
                $this->setData($data);
            }
        }
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
                $this->getObjects($cardId);
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
        $user = $this->ApiAuth->identify();
        if ($user) {
            $this->profile($user['id']);
        } else {
            throw new BeditaUnauthorizedException();
        }
    }

    /**
     * GET /posters endpoint
     * Return a poster thumbnail url of object $id or list of id's
     * using 'id' parameter with a comma separated list of id's
     * As 'posters' an image object is retrived using following order:
     * 1. if object $id has a 'poster' relation return that image object
     * 2. else if object $id is an image object type return it
     * 3. else if object $id has an 'attach' relation with an image return that image
     *
     * Possible query url paramters are:
     *
     * - 'width' the thumbnail width
     * - 'height' the thumbnail height
     *
     * @param int|string $id the object id or object nickname
     * @return void
     */
    protected function getPosters($id = null) {
        $thumbConf = $this->posterThumbConf();
        if (!empty($id)) {
            if (func_num_args() != 1) {
                throw new BeditaBadRequestException();
            }
            $id = is_numeric($id) ? $id : $this->BEObject->getIdFromNickname($id);
            if (empty($id)) {
                throw new BeditaNotFoundException();
            }
            try {
                $poster = $this->posterData($id, $thumbConf);
                $this->setData($poster);
            } catch (Exception $ex) {
                $this->setData();
            }
        } else {
            $urlParams = $this->ApiFormatter->formatUrlParams();
            if (empty($urlParams['id'])) {
                throw new BeditaBadRequestException('GET /posters requires at least one id');
            }
            $ids = is_array($urlParams['id']) ? $urlParams['id'] : array($urlParams['id']);
            if (count($ids) > $this->paginationOptions['maxPageSize']) {
                throw new BeditaBadRequestException(
                    'Too many ids requested. Max is ' . $this->paginationOptions['maxPageSize']
                );
            }
            $poster = array();
            try {
                foreach ($ids as $id) {
                    $poster[] = $this->posterData($id, $thumbConf);
                }
                $this->setData($poster);
            } catch (Exception $ex) {
                $this->setData();
            }
        }
    }

    /**
     * Returns thumbnail configuration array from URL and general configuration (used in /posters)
     * @return thumb conf array
     */
    private function posterThumbConf() {
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
        return $thumbConf;
    }
    
    /**
     * Returns poster data for a single object (used in /posters)
     * @param int $id
     * @param array $thumbConf
     * @return poster data array
     */
    private function posterData($id, array $thumbConf = array()) {
        $objectType = $this->BEObject->getType($id);
        $model = ClassRegistry::init($objectType);

        if ($model && $model instanceof UploadableInterface){
            $poster = $model->apiCreateThumbnail($id, $thumbConf);
        } else {
            $poster = $this->BEObject->getPoster($id);
            if (!$poster) {
                $poster['id'] = $id;
            }
            $beThumb = BeLib::getObject('BeThumb');
            $poster['uri'] = $beThumb->image($poster, $thumbConf);
        }
        if (!$poster) {
            throw new BeditaBadRequestException();
        }
        $poster['id'] = (int) $poster['id'];

        return $poster;
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
            if (empty($params['username']) || empty($params['password'])) {
                throw new BeditaBadRequestException();
            }
            // authenticate user
            if (Configure::read('staging') === true) {
                $authorizedGroups = array(); // only backend authorized groups
            } else {
                // frontend only authorized groups (default empty)
                $confGroups = Configure::read('authorizedGroups');
                // which groups? authorized groups if defined, or any group
                $group = ClassRegistry::init('Group');
                $authorizedGroups = (!empty($confGroups))? $confGroups : $group->getList(array('backend_auth' => 0));
            }
            $authResponse = $this->ApiAuth->authenticate($params['username'], $params['password'], $authorizedGroups);
            if (!$authResponse) {
                throw new BeditaUnauthorizedException();
            }

            $token = $this->ApiAuth->generateToken();
            $refreshToken = $this->ApiAuth->generateRefreshToken();
            if (!$token || !$refreshToken) {
                throw new BeditaUnauthorizedException();
            }
            $data = array(
                'access_token' => $token,
                'expires_in' => $this->ApiAuth->config['expiresIn'],
                'refresh_token' => $refreshToken
            );
        } elseif ($grantType == 'refresh_token') {
            if (empty($params['refresh_token'])) {
                throw new BeditaBadRequestException();
            }

            $token = $this->ApiAuth->renewToken($params['refresh_token']);
            if (!$token) {
                throw new BeditaUnauthorizedException('invalid refresh token');
            }

            $data = array(
                'access_token' => $token,
                'expires_in' => $this->ApiAuth->config['expiresIn'],
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
        $user = $this->ApiAuth->identify();
        if (!$user) {
            throw new BeditaUnauthorizedException();
        }
        $this->setData(array(
            'access_token' => $this->ApiAuth->getToken(),
            'expires_in' => $this->ApiAuth->expiresIn()
        ));
    }

    /**
     * Revoke authentication removing refresh token
     * If refresh token was removed successufully a 204 NO CONTENT status code returns
     *
     * @param string $refreshToken the refresh token to revoke
     * @return void
     */
    protected function deleteAuth($refreshToken) {
        if (!$this->ApiAuth->identify()) {
            throw new BeditaUnauthorizedException();
        }
        if ($this->ApiAuth->revokeRefreshToken($refreshToken)) {
            $this->emptyResponse();
        } else {
            throw new BeditaInternalErrorException();
        }
    }

    /**
     * Upload a file.
     * Respond with an upload_token that it must be used to link a new object to the uploaded file. 
     *
     * @param string $objectType The corresponding object type
     * @param string $fileName The file name
     * @return void
     */
    protected function postFiles($objectType = null, $fileName = null) {
        if (!$this->ApiAuth->identify()) {
            throw new BeditaUnauthorizedException();
        }

        if (empty($objectType)) {
            throw new BeditaBadRequestException('Missing object_type in url path');
        }

        if (empty($fileName)) {
            throw new BeditaBadRequestException('Missing file name in url path');
        }

        $uploadToken = $this->ApiUpload->upload($fileName, $objectType);
        $this->setData(array(
            'upload_token' => $uploadToken
        ));
    }

    /**
     * Build response data for client
     * $options array permits to customize the response.
     * Possible values are:
     * - 'emptyBody' true to send empty body to client (default false)
     * - 'statusCode' the HTTP status code you want to send to client
     * - 'setBase' false to avoid to set base response metadata (default true)
     *
     * self::autoResponse is set to false
     *
     * @param array $options should set generic api response info
     * @return void
     */
    protected function response(array $options = array()) {
        $options += array(
            'emptyBody' => false,
            'statusCode' => null,
            'setBase' => true
        );
        $this->autoResponse = false;
        if ($options['statusCode'] && is_int($options['statusCode'])) {
            $this->ResponseHandler->sendStatus($options['statusCode']);
        }
        if ($options['emptyBody']) {
            $this->set('_serialize', null);
        } else {
            if ($options['setBase']) {
                $this->setBaseResponse();
            }
            ksort($this->responseData);
            $this->set($this->responseData);
            $this->set('_serialize', array_keys($this->responseData));
        }
    }

    /**
     * Send an empty response body to client
     * Optionally it can send an HTTP status code
     *
     * @param int $statusCode a status code to send to client (default 204 No Content)
     *                        set it to null or other empty values to avoid to send status code
     * @return void
     */
    protected function emptyResponse($statusCode = 204) {
        $this->response(array(
            'statusCode' => $statusCode,
            'emptyBody' => true
        ));
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

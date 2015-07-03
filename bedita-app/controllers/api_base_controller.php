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
        'ApiValidator',
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
     * The default binding level
     *
     * @see FrontendController::defaultBindingLevel
     * @var string
     */
    protected $defaultBindingLevel = 'api';

    /**
     * Allowed model bindings
     *
     * @var array
     */
    protected $allowedModelBindings = array('default', 'frontend', 'minimum');

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
    protected $filter = array();

    /**
     * The request method invoked
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
     * The allowed filters you can apply to /objects endpoint
     * For example /objects/1/children search the children of object with id = 1
     *
     * Override in ApiController to limit or add functionality to /objects endpoint
     * For example adding to array 'foo' search type and adding ApiController::loadFoo() you can call /objects/1/foo
     *
     * @var array
     */
    protected $allowedObjectsFilter = array(
        'relations',
        'children',
        'contents',
        'sections',
        'descendants',
        'siblings',
        //'ancestors',
        //'parents'
    );

    protected $writableObjects = array();

    /**
     * Constructor
     * Setup endpoints available:
     *
     * - Merge self::defaultEndPoints, self::endPoints
     * - Add to endpoints object types whitelisted
     * - remove blacklisted endpoints (self::blacklistEndPoints)
     */
    public function __construct() {
        Configure::write('Session.start', false);
        $this->endPoints = array_unique(array_merge($this->defaultEndPoints, $this->endPoints));
        $objectTypes = Configure::read('objectTypes');
        foreach ($objectTypes as $key => $value) {
            if (is_numeric($key) && in_array($value['name'], $this->whitelistObjectTypes)) {
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

        $this->setupPagination();

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
     * objects endpoint method
     *
     * If $name is passed try to load an object with that id or nickname
     *
     * @param int|string $name an object id or nickname
     * @param string $filterType can be a value between those defined in self::allowedObjectsFilter
     * @param string $filterValue define a value for $filterType
     * @return void
     */
    protected function objects($name = null, $filterType = null, $filterValue = null) {
        if (!empty($name)) {
            $id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
            if (empty($id)) {
                throw new BeditaNotFoundException();
            }
            // check if object $id is reachable
            if (!$this->ApiValidator->isObjectReachable($id)) {
                // redo without checking permissions to know if it has to return 404
                if (!$this->ApiValidator->isObjectReachable($id, false)) {
                    throw new BeditaNotFoundException();
                }
                if (!$this->BeAuth->identify()) {
                    throw new BeditaUnauthorizedException();
                }
                throw new BeditaForbiddenException('Object ' . $name . ' is forbidden');
            }
            if (!empty($filterType)) {
                if (!in_array($filterType, $this->allowedObjectsFilter)) {
                    $allowedFilter = implode(', ', $this->allowedObjectsFilter);
                    throw new BeditaBadRequestException($filterType . ' not valid. Valid options are: ' . $allowedFilter);
                } else {
                    $method = 'load' . Inflector::camelize($filterType);
                    $args = func_get_args();
                    $args[0] = $id;
                    unset($args[1]);
                    call_user_func_array(array($this, $method), $args);
                }
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
                // check if id correspond to object type requested (if any)
                if (!empty($this->filter['object_type_id']) && $object['object_type_id'] != $this->filter['object_type_id']) {
                    throw new BeditaInternalErrorException('Object type mismatch');
                }

                $object = $this->ApiFormatter->formatObject(
                    $object,
                    array('countRelations' => true, 'countChildren' => true)
                );
                $this->setData($object);
            }
        // @todo list of objects
        } else {

        }
    }

    /**
     * POST /objects
     *
     * @param int|string $name an object id or nickname
     * @param string $filterType can be a value between those defined in self::allowedObjectsFilter
     * @param string $filterValue define a value for $filterType
     * @return void
     */
    protected function postObjects($name = null, $filterType = null, $filterValue = null) {
        $user = $this->BeAuthJwt->identify();
        if (!$user) {
            throw new BeditaUnauthorizedException();
        }
        if (empty($this->params['form']['object'])) {
            throw new BeditaBadRequestException('Missing object data to save');
        }

        // save object
        if (empty($name)) {
            $this->data = $this->params['form']['object'];
            if (empty($this->data['object_type'])) {
                throw new BeditaBadRequestException('Missing object type');
            }
            $objectTypeConf = Configure::read('objectTypes.' . $this->data['object_type']);
            if (empty($objectTypeConf)) {
                throw new BeditaBadRequestException('Invalid object type');
            }
            $objectModel = $this->loadModelByType($objectTypeConf['model']);
            $isNew = (empty($this->data['id'])) ? true : false;
            $this->Transaction->begin();
            $this->saveObject($objectModel);
            $savedObjectId = $objectModel->id;
            $this->Transaction->commit();
            $this->objects($savedObjectId);
            if ($isNew) {
                $this->ResponseHandler->sendStatus(201);
                $this->ResponseHandler->sendHeader('Location', $this->baseUrl(false) . 'objects/' . $savedObjectId);
            }
        } else {

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
        $user = $this->BeAuthJwt->identify();
        if (!$user) {
            throw new BeditaUnauthorizedException();
        }
        $options += array(
            'handleTagList' => false,
            'emptyPermission' => false,
            'saveTree' => false
        );

        if (empty($this->data['object_type'])) {
            throw new BeditaBadRequestException('Missing object type');
        }
        if (!in_array($this->data['object_type'], $this->writableObjects)) {
            throw new BeditaBadRequestException('Save forbidden for object type ' . $this->data['object_type']);
        }

        $objectTypeConf = Configure::read('objectTypes.' . $this->data['object_type']);
        $this->data['object_type_id'] = Configure::read('objectTypes.' . $this->data['object_type'] . '.id');
        $this->data['status'] = 'on';
        if (empty($this->data['id'])) {
            $this->data['user_created'] = $user['id'];
        }
        $this->data['user_modified'] = $user['id'];

        // validate and format data for save
        $this->ApiValidator->checkObject($this->data);
        $this->data = $this->ApiFormatter->formatObjectForSave($this->data);
        parent::saveObject($beModel, $options);

        if (!empty($this->data['parents'])) {
            ClassRegistry::init('Tree')->updateTree($beModel->id, $this->data['parents']);
        }
    }

    protected function saveRelations($objectId, array $relations = array()) {
        $objectRelation = ClassRegistry::init('ObjectRelation');
        foreach ($relations as $name => $relData) {
            // remove
            if (empty($relData)) {
                $objectRelation->deleteObjectRelation($objectId, $relData['related_id']);
            }
        }
    }

    protected function saveParents($objectId, $parents) {
        ClassRegistry::init('Tree')->updateTree($objectId, $parents);
    }

    /**
     * Get children of $parentId object, prepare and set response data
     * The response is automatically paginated using self::paginationOptions
     *
     * @see FrontendController::loadSectionObjects()
     * @param int $parentId the parent id
     * @param array $options an array of options for filter results
     * @return void
     */
    protected function responseChildren($parentId, array $options = array()) {
        $defaultOptions = array('explodeRelations' => false);
        $options = array_merge($defaultOptions, $this->paginationOptions, $options);
        // assure to have result in 'children' key
        $options['itemsTogether'] = true;
        // add conditions on not accessible objects (frontend_access_with_block)
        // @todo move to FrontendController::loadSectionObjects()?
        $user = $this->BeAuthJwt->getUser();
        $permissionJoin = array(
            'table' => 'permissions',
            'alias' => 'Permission',
            'type' => 'inner',
            'conditions' => array(
                'Permission.object_id = Tree.id',
                'Permission.flag' => Configure::read('objectPermissions.frontend_access_with_block'),
                'Permission.switch' => 'group',
            )
        );
        $fields = array('Tree.id');
        $conditions = array('Tree.parent_id' => $parentId);
        $group = 'Tree.id';

        $tree = ClassRegistry::init('Tree');
        $objectsForbidden = $tree->find('list', array(
            'fields' => $fields,
            'joins' => array($permissionJoin),
            'conditions' => $conditions,
            'group' => $group
        ));

        // allowed to user
        if (!empty($user)) {
            $permissionJoin['conditions']['Permission.ugid'] = $user['groupsIds'];
            $objectsAllowed = $tree->find('list', array(
                'fields' => $fields,
                'joins' => array($permissionJoin),
                'conditions' => $conditions,
                'group' => $group
            ));
            if (!empty($objectsAllowed)) {
                $objectsForbidden = array_diff($objectsForbidden, $objectsAllowed);
            }
        }

        if (!empty($objectsForbidden)) {
            $options['filter']['NOT']['BEObject.id'] = array_values($objectsForbidden);
        }

        $result = $this->loadSectionObjects($parentId, $options);
        if (empty($result['children'])) {
            $this->setData();
        } else {
            $objects = $this->ApiFormatter->formatObjects(
                $result['children'],
                array('countRelations' => true, 'countChildren' => true)
            );
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
    protected function loadChildren($id) {
        if (func_num_args() > 1) {
            throw new BeditaBadRequestException();
        }
        $this->responseChildren($id);
    }

    /**
     * Load sections children of object $id setting data for response
     *
     * @param int $id
     * @return void
     */
    protected function loadSections($id) {
        if (func_num_args() > 1) {
            throw new BeditaBadRequestException();
        }
        $sectionObjectTypeId = Configure::read('objectTypes.section.id');
        $result = $this->responseChildren($id, array(
            'filter' => array(
                'object_type_id' => array($sectionObjectTypeId)
            )
        ));
    }

    /**
     * Load contents children of object $id setting data for response
     *
     * @param int $id
     * @return void
     */
    protected function loadContents($id) {
        if (func_num_args() > 1) {
            throw new BeditaBadRequestException();
        }
        $sectionObjectTypeId = Configure::read('objectTypes.section.id');
        $result = $this->responseChildren($id, array(
            'filter' => array(
                'NOT' => array(
                    'object_type_id' => array($sectionObjectTypeId)
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
    protected function loadDescendants($id) {
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
    protected function loadSiblings($id) {
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
     * @return void
     */
    protected function loadRelations($id, $relation = '') {
        if (func_num_args() > 2) {
            throw new BeditaBadRequestException();
        }
        // count relations of object $id
        if (empty($relation)) {
            $relCount = $this->ApiFormatter->formatRelationsCount(array('id' => $id));
            $this->setData($relCount);
        // detail of related objects
        } else {
            $defaultOptions = array('explodeRelations' => false);
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

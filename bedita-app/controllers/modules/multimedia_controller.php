<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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
 * Module Multimedia: management of Application, File, Image, Audio, Video objects
 */
class MultimediaController extends ModulesController {
    /**
     * Module label
     * 
     * @var string
     */
    var $name = 'Multimedia';

    /**
     * Module helpers
     * 
     * @var array
     */
    var $helpers = array('BeTree', 'BeToolbar', 'ImageInfo');

    /**
     * Module components
     * 
     * @var array
     */
    var $components = array('BeFileHandler', 'BeUploadToObj', 'BeSecurity');

    /**
     * Modules models. This controller does not use a single model
     * 
     * @var array
     */
    var $uses = array('BEObject', 'Application', 'Stream', 'Image', 'Audio', 'Video', 'Tree', 'User', 'Group', 'Category', 'BEFile');

    /**
     * The object types for module multimedia. Can be customized in configure 'multimedia.types'
     * 
     * @var array
     */
    protected $objectTypes = array('application', 'audio', 'b_e_file', 'image', 'video');

    /**
     * The allowed models for module. They depends on 'multimedia.types', if set
     *
     * @var array
     */
    protected $allowed = array();

    /**
     * Module name
     * 
     * @var string
     */
    protected $moduleName = 'multimedia';

    /**
     * Set types for multimedia module.
     * Remove from 'validate_resource.mime' entries for types not allowed.
     * 
     * @return void
     */
    protected function beditaBeforeFilter()
    {
        parent::beditaBeforeFilter();
        $objectTypes = Configure::read('multimedia.types');
        if ($objectTypes) {
            $this->objectTypes = $objectTypes;
        }
        foreach ($this->objectTypes as $type) {
            $this->allowed[] = Inflector::camelize($type);
        }
        $mime = Configure::read('validate_resource.mime');
        foreach ($mime as $model => $data) {
            if (!in_array($model, $this->allowed)) {
                unset($mime[$model]);
            }
        }
        Configure::write('validate_resource.mime', $mime);
    }

    /**
     * Index for multimedia module
     *
     * @param string|int $id The multimedia ID
     * @param string|null $order The field for the order by
     * @param integer|null $dir The directory depth
     * @param integer|null $page The page number for pagination
     * @param integer|null $dim The dimension of results, for pagination
     * @return void
     */
    public function index($id = null, $order = 'id', $dir = 0, $page = 1, $dim = 50)
    {
        // setup arguments for get children
        $this->setup_args(
            array('id', 'integer', &$id),
            array('page', 'integer', &$page),
            array('dim', 'integer', &$dim),
            array('order', 'string', &$order),
            array('dir', 'boolean', &$dir)
        );

        // prepare filter for BeTree->getChildren
        $filter = $this->prepareFilter($order);

        // get multimedia items
        $multimedia = $this->BeTree->getChildren($id, null, $filter, $order, $dir, $page, $dim)  ;
        $this->params['toolbar'] = &$multimedia['toolbar'];

        // get properties
        $properties = ClassRegistry::init('Property')->find('all', array(
            'conditions' => array('object_type_id' => $filter['object_type_id']),
            'contain' => array(),
        ));

        // get publications
        $user = $this->BeAuth->getUserSession();
        $expandBranch = array();
        if (!empty($filter['parent_id'])) {
            $expandBranch[] = $filter['parent_id'];
        } elseif (!empty($id)) {
            $expandBranch[] = $id;
        }
        $treeModel = ClassRegistry::init('Tree');
        $tree = $treeModel->getAllRoots($user['userid'], null, array('count_permission' => true), $expandBranch);

        // get available relations
        $availableRelations = array();
        $objectRelation = ClassRegistry::init('ObjectRelation');
        $conf = Configure::getInstance();
        foreach ($conf->objectTypes['multimedia']['id'] as $mediaId) {
            $r = $objectRelation->availableRelations($mediaId);
            $availableRelations = array_merge($availableRelations, $r);
        }

        // exclude some kind of relations from view
        $relationsToExclude = array('attach' => 'attach', 'download' => 'download', 'seealso' => 'seealso');
        $availableRelations = array_diff_key($availableRelations, $relationsToExclude);

        // template data
        $this->set('tree', $tree);
        $this->set('objects', $multimedia['items']);
        $this->set('properties', $properties);
        $this->set('availableRelations', $availableRelations);
        $this->setSessionForObjectDetail($multimedia['items']);
    }

    /**
     * View for module multimedia
     *
     * @param string|int|null $id The multimedia ID
     * @return void
     */
    public function view($id = null)
    {
        $conf  = Configure::getInstance();
        $this->setup_args(array('id', 'integer', &$id));
        // Get object by $id
        $obj = null ;
        $parents_id = array();
        $name = '';
        if ($id) {
            // check if object is forbidden for user
            $user = $this->Session->read('BEAuthUser');
            $permission = ClassRegistry::init('Permission');
            if ($permission->isForbidden($id, $user)) {
                throw new BeditaException(__('Access forbidden to object', true) . ' ' . $id);
            }
            $objEditor = ClassRegistry::init('ObjectEditor');
            $objEditor->cleanup($id);
            $model = ClassRegistry::init($this->BEObject->getType($id));
            $name = Inflector::underscore($model->name);
            if (!in_array('multimedia', $model->objectTypesGroups)) {
                throw new BeditaException(__('Error loading object', true));
            }
            $model->containLevel('detailed');
            if(!($obj = $model->findById($id))) {
                 throw new BeditaException(sprintf(__('Error loading object: %d', true), $id));
            }
            if (!in_array($model->name, $this->allowed)) {
                $module = $obj['ObjectType']['module_name'];
                if ($module === 'multimedia') {
                    throw new BeditaException(__('Object of a type not allowed for module', true));
                }
                $this->redirect(sprintf('/%s/view/%s', $module, $id));
                return;
            }
            if (isset($obj['Category'])) {
                $objCat = array();
                foreach ($obj['Category'] as $oc) {
                    $objCat = $oc['name'];
                }
                $obj['Category'] = $objCat;
            }

            if (!empty($obj['RelatedObject'])) {
                $obj['relations'] = $this->objectRelationArray($obj['RelatedObject']);
            }
            if (!empty($obj['Annotation'])) {
                $this->setupAnnotations($obj);
            }
            unset($obj['Annotation']);

            $imagePath  = $this->BeFileHandler->path($id);
            $imageURL   = $this->BeFileHandler->url($id);

            $treeModel = ClassRegistry::init('Tree');
            $parents_id = $treeModel->getParents($id);

            $previews = $this->previewsForObject($parents_id, $id, $obj['status']);

            $this->historyItem['object_id'] = $id;

            //check if hash is present elsewhere
            if (!empty($obj['hash_file'])) {
                if (empty($obj['uri'])) {
                    //#630 add usr/event msg
                    $this->userErrorMessage(__('Media file expected, hash value is present', true) . ' -  ' . $obj['hash_file']);
                    $this->eventError('multimedia file expected for hash: ' . $obj['hash_file']);
                }
                $results = $this->Image->query("SELECT * FROM streams INNER JOIN objects ON objects.id = streams.id WHERE hash_file='".$obj['hash_file']."'  AND streams.id != ".$obj['id']."");
                $this->set('elsewhere_hash', $results);
            }

            // #536 check local file existence
            if (!empty($obj['uri']) && ($obj['uri'][0] === '/' || $obj['uri'][0] === DS)) {
                $path = Configure::read('mediaRoot') . $obj['uri'];
                if (!file_exists($path)) {
                    $url = Configure::read('mediaUrl') . $obj['uri'];
                    $this->userErrorMessage(__('Media file is missing', true) . ' -  ' . $url);
                    $this->eventError('multimedia missing file: ' . $url);
                }
            }

            // #707 check exceeding file size
            if (!empty($obj['file_size']) && $name === 'image' && 
                    ($obj['file_size'] > Configure::read('imgFilesizeLimit'))) {
                $this->userWarnMessage(__('Image file is too big, unable to create thumbnails', true));
            }

            $this->set('objectProperty', $this->BeCustomProperty->setupForView($obj, Configure::read('objectTypes.' . $model->name . '.id')));
        } else {
            Configure::write('defaultStatus', 'on'); // set default ON for new objects
        }

        $availableRelations = ClassRegistry::init('ObjectRelation')->availableRelations($name);

        // data for template
        $this->set('object',    @$obj);
        $this->set('imagePath', @$imagePath);
        $this->set('imageUrl',  @$imageURL);

        // exclude some kind of relations from view
        $relationsToExclude = array(
            'attach' => 'attach',
            'download' => 'download',
            'seealso' => 'seealso',
        );
        $availableRelations = array_diff_key($availableRelations, $relationsToExclude);

        $this->set('availabeRelations', $availableRelations);

        if(!empty($obj['relations'])) {
            $this->set('relObjects', $obj['relations']);
        }

        // get publications
        $user = $this->BeAuth->getUserSession();
        $treeModel = ClassRegistry::init('Tree');
        $tree = $treeModel->getAllRoots($user['userid'], null, array('count_permission' => true), $parents_id);

        $this->set('tree', $tree);
        $this->set('parents', $parents_id);
        $this->setSessionForObjectDetail();
    }

    /**
     * Save data via ajax
     *
     * @return void
     */
    public function saveAjax()
    {
        $this->layout = 'ajax';
        try {
            if (!empty($this->params['form']['upload_choice'])) {
                $streamData = $this->Stream->find('first', array(
                    'conditions' => array('id' => $this->params['form']['upload_other_obj_id'])
                ));

                $this->data['uri'] = $streamData['Stream']['uri'];
                $this->data['name'] = $streamData['Stream']['name'];
                $this->data['original_name'] = $streamData['Stream']['original_name'];
                $this->data['mime_type'] = $streamData['Stream']['mime_type'];

                if ($this->params['form']['upload_choice'] == 'new_file_new_obj') {
                    // if it's not a new object then clone original object
                    if (!empty($this->data['id'])) {
                        $this->cloneObject();
                    // else it it's new, save object cloning media attached
                    } else {
                        $this->save(true);
                    }
                } else { // new_file_old_obj
                    $this->BeUploadToObj->cloneMediaObject($this->data, true);
                    $this->save();
                }
            } else {
                $this->set('newObject', empty($this->data['id']));
                $this->save();
            }

            $this->set('redirUrl', '/multimedia/view/'.$this->Stream->id);

        } catch (BEditaFileExistException $ex) {
            $errTrace = get_class($ex) . ' - ' . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();
            $this->setResult(self::ERROR);
            $this->set('errorFileExist', true);
            $this->set('errorMsg', $ex->getMessage());
            $this->set('objectId', $ex->getObjectId());
            $this->set('objectTitle', $this->BEObject->field('title', array('id' => $ex->getObjectId())));
        } catch(BeditaException $ex) {
            // force header text/plain to haven't javascript error (jQuery undefined) when a file was uploaded
            throw new BeditaAjaxException(
                $ex->getMessage(),
                array(
                    'output' => 'beditaMsg',
                    'headers' => array('Content-Type: text/plain', 'HTTP/1.1 500 Internal Server Error')
                )
            );
        }
    }

    /**
     * Save data
     *
     * @param boolean $cloneMedia The clone media flag
     * @return void
     */
    public function save($cloneMedia = false)
    {
        $this->checkWriteModulePermission();
        if(empty($this->data)) {
            throw new BeditaException( __('No data', true));
        }

        $new = (empty($this->data['id'])) ? true : false ;

        if (!$new) {
            $this->checkObjectWritePermission($this->data['id']);
        }

        $this->prepareRelationsToSave();

        // Format custom properties
        $this->BeCustomProperty->setupForSave();

        $this->Transaction->begin();
        // save data
        if (!empty($this->params['form']['tags'])) {
            $this->data['Category'] = $this->Category->saveTagList($this->params['form']['tags']);
        }

        if (!empty($this->params['form']['Filedata']['name'])) {
            if(!empty($this->data['url'])) {
                unset($this->data['url']);
            }
            if ($cloneMedia) {
                $this->params['form']['forceupload'] = true;
            }
            $this->Stream->id = $this->BeUploadToObj->upload($this->data);
        } elseif (!empty($this->data['url'])) {
            $this->Stream->id = $this->BeUploadToObj->uploadFromURL($this->data, $cloneMedia);
        } elseif ($cloneMedia) {
            $this->Stream->id = $this->BeUploadToObj->cloneMediaObject($this->data);
        } else {
            if(!empty($this->data['url'])) {
                unset($this->data['url']);
            }
            $model = (!empty($this->data['id']))? $this->BEObject->getType($this->data['id']) : 'BEFile';

            if ($model == 'Video') {
                $this->data['thumbnail'] = $this->BeUploadToObj->getThumbnail($this->data);
            }
            if (!empty($this->params['form']['mediatype'])) {
                $object_type_id = Configure::read('objectTypes.' . Inflector::underscore($model) . '.id');
                if (empty($this->data['Category'])) {
                    $this->data['Category'] = array();
                }
                $this->data['Category'] = array_merge($this->data['Category'], $this->Category->checkMediaType($object_type_id, $this->params['form']['mediatype']));
            }

            if (!isset($this->data['Permission'])) {
                $this->data['Permission'] = array();
            }

            if (!$this->{$model}->save($this->data)) {
                throw new BeditaException(__('Error saving multimedia', true), $this->{$model}->validationErrors);
            }
            $this->Stream->id = $this->{$model}->id;
        }

        $this->data['id'] = $this->Stream->id;

        if (isset($this->data['destination'])) {
            if (!$new) {
                $this->BeTree->setupForSave($this->Stream->id, $this->data['destination']);
            }
            ClassRegistry::init('Tree')->updateTree($this->Stream->id, $this->data['destination']);
        }
        $this->Transaction->commit();
        $this->userInfoMessage(__('Multimedia object saved', true).' - '.$this->data['title']);
        $this->eventInfo('multimedia object ['. $this->data['title'].'] saved');
    }

    /**
     * Clone data
     *
     * @return void
     */
    public function cloneObject()
    {
        unset($this->data['id']);
        unset($this->data['nickname']);
        $this->data['status'] = 'draft';
        $this->data['fixed'] = 0;
        $this->save(true);
    }

    /**
     * Delete data
     *
     * @return void
     */
    public function delete()
    {
        $this->checkWriteModulePermission();
        $objectsListDeleted = $this->deleteObjects('Stream');
        $this->userInfoMessage(__('Multimedia deleted', true) . ' -  ' . $objectsListDeleted);
        $this->eventInfo(sprintf('multimedia %s deleted', $objectsListDeleted));
    }

    /**
     * Delete selected multimedia
     *
     * @return void
     */
    public function deleteSelected()
    {
        $this->checkWriteModulePermission();
        $objectsListDeleted = $this->deleteObjects('Stream');
        $this->userInfoMessage(__('Multimedia deleted', true) . ' -  ' . $objectsListDeleted);
        $this->eventInfo(sprintf('multimedia %s deleted', $objectsListDeleted));
    }

    /**
     * Load multiupload in modal
     * called via ajax
     * 
     * @return void
     */
    public function multipleUpload()
    {
        $this->layout = 'ajax';
    }

    /**
     * Forward action
     *
     * @param string $action The action
     * @param string $result The result
     * @return void
     */
    protected function forward($action, $result)
    {
        $moduleRedirect = array(
            'saveAjax' => array(
                'OK' => self::VIEW_FWD.'upload_ajax_response',
                'ERROR' => self::VIEW_FWD.'upload_ajax_response',
            ),
        );
        return $this->moduleForward($action, $result, $moduleRedirect);
    }

    /**
     * Prepare filter for get children call
     *
     * @param string|null $order The order field, if any
     * @return array The filter
     */
    private function prepareFilter($order)
    {
        $filter = array(
            'object_type_id' => array(),
            'count_annotation' => array('Comment', 'EditorNote'),
            'count_permission' => true,
            'afterFilter' => array(
                array(
                    'className' => 'ObjectProperty',
                    'methodName' => 'objectsCustomProperties'
                ),
                array(
                    'className' => 'Stream',
                    'methodName' => 'appendStreamFields'
                ),
                array(
                    'className' => 'ObjectRelation',
                    'methodName' => 'countRelations',
                    'options' => array(
                        'relations' => array('attach', 'seealso', 'download')
                    ),
                ),
                array(
                    'className' => 'Tree',
                    'methodName' => 'countUbiquity',
                ),
            ),
        );
        $conf = Configure::getInstance();
        foreach ($this->objectTypes as $type) {
            $filter['object_type_id'][] = $conf->objectTypes[$type]['id'];
        }
        if ($order === 'mediatype') {
            $filter['mediatype'] = 1;
        } else {
            $filter['afterFilter'][] = array(
                'className' => 'Category',
                'methodName' => 'appendMediatype'
            );
        }
        $sessionFilter = $this->SessionFilter->setFromUrl();
        $filter = array_merge($filter, $sessionFilter);

        return $filter;
    }
}

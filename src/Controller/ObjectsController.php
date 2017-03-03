<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Controller;

use BEdita\Core\Model\Action\ListRelatedObjects;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ConflictException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\NotImplementedException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;

/**
 * Controller for `/objects` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable $Objects
 */
class ObjectsController extends ResourcesController
{

    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Objects';

    /**
     * The referred object type entity filled when `object_type` request param is set and valid
     *
     * @var \BEdita\Core\Model\Entity\ObjectType
     */
    protected $objectType = null;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $type = $this->request->getParam('object_type', 'objects');
        if ($type != 'objects') {
            try {
                $this->objectType = TableRegistry::get('ObjectTypes')->get($type);
                $this->modelClass = $this->objectType->alias;
                $this->Objects = TableRegistry::get($this->modelClass);
            } catch (RecordNotFoundException $e) {
                $this->log('Type endpoint does not exist ' . $type, 'error');

                throw new NotFoundException('Endpoint does not exist');
            }
        }

        $this->set('_type', 'objects');
    }

    /**
     * Paginated objects list.
     *
     * @return void
     */
    public function index()
    {
        $query = $this->Objects->find('all')
            ->where(['deleted' => 0])
            ->contain(['ObjectTypes']);

        if ($this->objectType) {
            $query->andWhere(['object_type_id' => $this->objectType->id]);
        }

        $objects = $this->paginate($query);

        $this->set(compact('objects'));
        $this->set('_serialize', ['objects']);
    }

    /**
     * {@inheritDoc}
     */
    public function related()
    {
        $relationship = $this->request->getParam('relationship');
        $relatedId = $this->request->getParam('related_id');

        $Association = $this->findAssociation($relationship);

        $action = new ListRelatedObjects($Association);
        $query = $action($relatedId);

        $query = $query->select($Association, true);

        $objects = $this->paginate($query);

        $this->set(compact('objects'));
        $this->set([
            '_serialize' => ['objects'],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function findAssociation($relationship)
    {
        $behaviorRegistry = $this->Objects->behaviors();
        if ($behaviorRegistry->hasMethod('getRelations') && array_key_exists($relationship, $behaviorRegistry->call('getRelations'))) {
            return $this->Objects->association(Inflector::camelize(Inflector::underscore($relationship)));
        }

        return parent::findAssociation($relationship);
    }

    /**
     * Get single object data.
     *
     * @param int $id Object ID.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException Throws an exception if specified object could not be found.
     */
    public function view($id)
    {
        $conditions = ['deleted' => 0];
        if ($this->objectType) {
            $conditions['object_type_id'] = $this->objectType->id;
        }
        $object = $this->Objects->get($id, [
            'contain' => ['ObjectTypes'],
            'conditions' => $conditions
        ]);

        $this->set(compact('object'));
        $this->set('_serialize', ['object']);
    }

    /**
     * Add a new object.
     *
     * @return void
     * @throws \Cake\Network\Exception\BadRequestException Throws an exception if submitted data is invalid.
     */
    public function add()
    {
        $this->request->allowMethod('post');

        $object = $this->Objects->newEntity($this->request->getData());
        $object->type = $this->request->getData('type');
        if ($this->objectType && $object->type !== $this->objectType->name) {
            $this->log('Bad type on object creation ' . $object->type, 'error');
            throw new BadRequestException('Invalid type');
        }
        if (!$this->Objects->save($object)) {
            $this->log('Object creation failed  - ' . $object->type . ' - ' . json_encode($object->errors()), 'error');
            throw new BadRequestException(['title' => 'Invalid data', 'detail' => [$object->errors()]]);
        }

        $this->response = $this->response
            ->withStatus(201)
            ->withHeader(
                'Location',
                Router::url(
                    [
                        'object_type' => $object->type,
                        '_name' => 'api:objects:view',
                        $object->id,
                    ],
                    true
                )
            );

        $this->set(compact('object'));
        $this->set('_serialize', ['object']);
    }

    /**
     * Edit an existing object.
     *
     * @param int $id Object ID.
     * @return void
     * @throws \Cake\Network\Exception\ConflictException Throws an exception if object ID in the payload doesn't match
     *      the URL object ID.
     * @throws \Cake\Network\Exception\NotFoundException Throws an exception if specified object could not be found.
     * @throws \Cake\Network\Exception\BadRequestException Throws an exception if submitted data is invalid.
     */
    public function edit($id)
    {
        $this->request->allowMethod('patch');

        if ($this->request->getData('id') != $id) {
            throw new ConflictException('IDs don\' match');
        }

        $object = $this->Objects->get($id, [
            'conditions' => ['deleted' => 0]
        ]);

        if ($this->objectType && $object->type !== $this->objectType->name) {
            $this->log('Bad type on object edit ' . $object->type, 'error');
            throw new NotFoundException('Invalid type');
        }

        $object = $this->Objects->patchEntity($object, $this->request->getData());
        if (!$this->Objects->save($object)) {
            $this->log('Object edit failed ' . json_encode($object->errors()), 'error');
            throw new BadRequestException(['title' => 'Invalid data', 'detail' => [$object->errors()]]);
        }

        $this->set(compact('object'));
        $this->set('_serialize', ['object']);
    }

    /**
     * Delete an existing object.
     *
     * @param int $id object ID.
     * @return \Cake\Network\Response
     * @throws \Cake\Network\Exception\InternalErrorException Throws an exception if an error occurs during deletion.
     * @throws \Cake\Network\Exception\NotFoundException Throws an exception if specified object could not be found.
     */
    public function delete($id)
    {
        $this->request->allowMethod('delete');

        $object = $this->Objects->get($id, [
            'conditions' => ['deleted' => 0]
        ]);

        if ($this->objectType && $object->type !== $this->objectType->name) {
            $this->log('Bad type on object delete ' . $object->type, 'error');
            throw new NotFoundException('Invalid type');
        }

        $object->deleted = true;
        if (!$this->Objects->save($object)) {
            throw new InternalErrorException('Could not delete object');
        }

        return $this->response
            ->withHeader('Content-Type', $this->request->contentType())
            ->withStatus(204);
    }

    /**
     * View and manage relationships.
     *
     * @return \Cake\Network\Response|null
     */
    public function relationships()
    {
        $this->request->allowMethod(['get', 'post', 'patch', 'delete']);

        $id = $this->request->getParam('id');
        $relationship = $this->request->getParam('relationship');

        $Association = $this->findAssociation($relationship);

        switch ($this->request->getMethod()) {
            case 'PATCH':
            case 'POST':
            case 'DELETE':
                throw new NotImplementedException(__d('bedita', 'Not yet implemented'));

            case 'GET':
            default:
                $action = new ListRelatedObjects($Association);
                $data = $action($id);

                if ($data instanceof Query) {
                    $data = $this->paginate($data);
                }

                $this->set(compact('data'));
                $this->set([
                    '_serialize' => ['data'],
                ]);

                return null;
        }
    }
}

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

use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ConflictException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Routing\Router;

/**
 * Controller for `/object_types` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\ObjectTypesTable $ObjectTypes
 */
class ObjectTypesController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'ObjectTypes';

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->set('_type', 'object_types');
        if (isset($this->JsonApi)) {
            $this->JsonApi->config('resourceTypes', ['object_types']);
        }
    }

    /**
     * Paginated object_types list.
     *
     * @return void
     */
    public function index()
    {
        $query = $this->ObjectTypes->find('all');

        $object_types = $this->paginate($query);

        $this->set(compact('object_types'));
        $this->set('_serialize', ['object_types']);
    }

    /**
     * Get object_type's data.
     *
     * @param int $id ObjectType ID.
     * @return void
     */
    public function view($id)
    {

        $object_type = $this->ObjectTypes->get($id);

        $this->set(compact('object_type'));
        $this->set('_serialize', ['object_type']);
    }

    /**
     * Add a new object_type.
     *
     * @return void
     * @throws \Cake\Network\Exception\BadRequestException Throws an exception if submitted data is invalid.
     */
    public function add()
    {
        $this->request->allowMethod('post');

        $object_type = $this->ObjectTypes->newEntity($this->request->data);
        if (!$this->ObjectTypes->save($object_type)) {
            throw new BadRequestException('Invalid data');
        }

        $this->response->statusCode(201);
        $this->response->header('Location', Router::url(['_name' => 'api:object_types:view', $object_type->id], true));

        $this->set(compact('object_type'));
        $this->set('_serialize', ['object_type']);
    }

    /**
     * Edit an existing object_type.
     *
     * @param int $id ObjectType ID.
     * @return void
     * @throws \Cake\Network\Exception\ConflictException Throws an exception if object_type ID in the payload doesn't match
     *      the object_type ID in the URL.
     * @throws \Cake\Network\Exception\NotFoundException Throws an exception if specified object_type could not be found.
     * @throws \Cake\Network\Exception\BadRequestException Throws an exception if submitted data is invalid.
     */
    public function edit($id)
    {
        $this->request->allowMethod('patch');

        if ($this->request->data('id') != $id) {
            throw new ConflictException('IDs don\' match');
        }

        $object_type = $this->ObjectTypes->get($id);
        $object_type = $this->ObjectTypes->patchEntity($object_type, $this->request->data);
        if (!$this->ObjectTypes->save($object_type)) {
            throw new BadRequestException('Invalid data');
        }

        $this->set(compact('object_type'));
        $this->set('_serialize', ['object_type']);
    }

    /**
     * Delete an existing object_type.
     *
     * @param int $id ObjectType ID.
     * @return void
     * @throws \Cake\Network\Exception\InternalErrorException Throws an exception if an error occurs during deletion.
     */
    public function delete($id)
    {
        $this->request->allowMethod('delete');

        $object_type = $this->ObjectTypes->get($id);
        if (!$this->ObjectTypes->delete($object_type)) {
            throw new InternalErrorException('Could not delete object_type');
        }

        $this->noContentResponse();
    }
}

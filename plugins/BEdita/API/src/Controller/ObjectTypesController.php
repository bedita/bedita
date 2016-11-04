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

        $objectTypes = $this->paginate($query);

        $this->set(compact('objectTypes'));
        $this->set('_serialize', ['objectTypes']);
    }

    /**
     * Get object_type's data.
     *
     * @param int $id ObjectType ID.
     * @return void
     */
    public function view($id)
    {
        $objectTypes = $this->ObjectTypes->get($id);

        $this->set(compact('objectTypes'));
        $this->set('_serialize', ['objectTypes']);
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

        $objectTypes = $this->ObjectTypes->newEntity($this->request->data);
        if (!$this->ObjectTypes->save($objectTypes)) {
            throw new BadRequestException('Invalid data');
        }

        $this->response->statusCode(201);
        $this->response->header('Location', Router::url(['_name' => 'api:object_types:view', $objectTypes->id], true));

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

        $objectType = $this->ObjectTypes->get($id);
        $objectType = $this->ObjectTypes->patchEntity($objectType, $this->request->data);
        if (!$this->ObjectTypes->save($objectType)) {
            throw new BadRequestException('Invalid data');
        }

        $this->set(compact('objectType'));
        $this->set('_serialize', ['objectType']);
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

        $objectType = $this->ObjectTypes->get($id);
        if (!$this->ObjectTypes->delete($objectType)) {
            throw new InternalErrorException('Could not delete object_type');
        }

        $this->noContentResponse();
    }
}

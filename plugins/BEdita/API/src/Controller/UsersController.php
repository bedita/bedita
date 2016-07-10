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
use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\Query;

/**
 * Controller for `/users` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Users';

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->set('_type', 'users');
        if (isset($this->JsonApi)) {
            $this->JsonApi->config('resourceTypes', ['users']);
        }
    }

    /**
     * Paginated users list.
     *
     * @return void
     */
    public function index()
    {
        $query = $this->Users->find('all');

        if ($roleId = $this->request->param('role_id')) {
            $query = $query->innerJoinWith('Roles', function (Query $query) use ($roleId) {
                return $query->where(['Roles.id' => $roleId]);
            });
        }

        $users = $this->paginate($query);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * Get user's data.
     *
     * @param int $id User ID.
     * @return void
     */
    public function view($id)
    {
        $user = $this->Users->get($id);

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Add a new user.
     *
     * @return void
     */
    public function add()
    {
        $this->request->allowMethod('post');

        $user = $this->Users->newEntity($this->request->data);
        if (!$this->Users->save($user)) {
            throw new BadRequestException('Invalid data');
        }

        $this->response->statusCode(201);

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete an existing user.
     *
     * @param int $id User ID.
     * @return \Cake\Network\Response
     * @throws \Cake\Network\Exception\InternalErrorException Throws an exception if an error occurs during deletion.
     */
    public function delete($id)
    {
        $this->request->allowMethod('delete');

        $user = $this->Users->get($id);
        if (!$this->Users->delete($user)) {
            throw new InternalErrorException('Could not delete user');
        }

        return $this->response;
    }
}

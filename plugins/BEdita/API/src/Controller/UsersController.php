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
use Cake\ORM\Query;
use Cake\Routing\Router;

/**
 * Controller for `/users` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\UsersTable $Users
 */
class UsersController extends ResourcesController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Users';

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'roles' => ['roles'],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        if (isset($this->JsonApi) && $this->request->getParam('action') != 'relationships') {
            $this->JsonApi->setConfig('resourceTypes', ['users']);
        }
    }

    /**
     * Paginated users list.
     *
     * @return void
     */
    public function index()
    {
        $query = $this->Users->find('all')->where(['deleted' => 0]);

        $relatedId = $this->request->getParam('related_id');
        if ($relatedId !== false) {
            $relationship = $this->request->getParam('relationship');
            $Association = $this->findAssociation($relationship);
            $query = $query->innerJoinWith(
                $Association->getName(),
                function (Query $query) use ($Association, $relatedId) {
                    return $query->where([$Association->aliasField('id') => $relatedId]);
                }
            );
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
        $user = $this->Users->get($id, [
            'conditions' => ['deleted' => 0]
        ]);

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Add a new user.
     *
     * @return void
     * @throws \Cake\Network\Exception\BadRequestException Throws an exception if submitted data is invalid.
     */
    public function add()
    {
        $this->request->allowMethod('post');

        $user = $this->Users->newEntity($this->request->getData());
        $user->type = $this->request->getData('type');
        if (!$this->Users->save($user)) {
            $this->log('User creation failed ' . json_encode($user->errors()), 'error');
            throw new BadRequestException(['title' => 'Invalid data', 'detail' => [$user->errors()]]);
        }

        $this->response = $this->response
            ->withStatus(201)
            ->withHeader(
                'Location',
                Router::url(
                    [
                        '_name' => 'api:users:view',
                        $user->id,
                    ],
                    true
                )
            );

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit an existing user.
     *
     * @param int $id User ID.
     * @return void
     * @throws \Cake\Network\Exception\ConflictException Throws an exception if user ID in the payload doesn't match
     *      the user ID in the URL.
     * @throws \Cake\Network\Exception\NotFoundException Throws an exception if specified user could not be found.
     * @throws \Cake\Network\Exception\BadRequestException Throws an exception if submitted data is invalid.
     */
    public function edit($id)
    {
        $this->request->allowMethod('patch');

        if ($this->request->getData('id') != $id) {
            throw new ConflictException('IDs don\' match');
        }

        $user = $this->Users->get($id, [
            'conditions' => ['deleted' => 0]
        ]);
        $user = $this->Users->patchEntity($user, $this->request->getData());
        if (!$this->Users->save($user)) {
            $this->log('User edit failed ' . json_encode($user->errors()), 'error');
            throw new BadRequestException(['title' => 'Invalid data', 'detail' => [$user->errors()]]);
        }

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

        $user = $this->Users->get($id, [
            'conditions' => ['deleted' => 0]
        ]);
        $user->deleted = true;
        if (!$this->Users->save($user)) {
            throw new InternalErrorException('Could not delete user');
        }

        return $this->response
            ->withHeader('Content-Type', $this->request->contentType())
            ->withStatus(204);
    }
}

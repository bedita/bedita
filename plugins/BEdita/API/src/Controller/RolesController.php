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
 * Controller for `/roles` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\RolesTable $Roles
 */
class RolesController extends ResourcesController
{

    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Roles';

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'users' => ['users'],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        if (isset($this->JsonApi) && $this->request->getParam('action') != 'relationships') {
            $this->JsonApi->setConfig('resourceTypes', ['roles']);
        }
    }

    /**
     * Paginated roles list.
     *
     * @return void
     */
    public function index()
    {
        $query = $this->Roles->find('all');

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

        $roles = $this->paginate($query);

        $this->set(compact('roles'));
        $this->set('_serialize', ['roles']);
    }

    /**
     * Get role's data.
     *
     * @param int $id Role ID.
     * @return void
     */
    public function view($id)
    {
        $role = $this->Roles->get($id);

        $this->set(compact('role'));
        $this->set('_serialize', ['role']);
    }

    /**
     * Add a new role.
     *
     * @return void
     * @throws \Cake\Network\Exception\BadRequestException Throws an exception if submitted data is invalid.
     */
    public function add()
    {
        $this->request->allowMethod('post');

        $role = $this->Roles->newEntity($this->request->getData());
        if (!$this->Roles->save($role)) {
            $this->log('Role add failed ' . json_encode($role->errors()), 'error');
            throw new BadRequestException(['title' => 'Invalid data', 'detail' => [$role->errors()]]);
        }

        $this->response = $this->response
            ->withStatus(201)
            ->withHeader(
                'Location',
                Router::url(
                    [
                        '_name' => 'api:roles:view',
                        $role->id,
                    ],
                    true
                )
            );

        $this->set(compact('role'));
        $this->set('_serialize', ['role']);
    }

    /**
     * Edit an existing role.
     *
     * @param int $id Role ID.
     * @return void
     * @throws \Cake\Network\Exception\ConflictException Throws an exception if role ID in the payload doesn't match
     *      the role ID in the URL.
     * @throws \Cake\Network\Exception\NotFoundException Throws an exception if specified role could not be found.
     * @throws \Cake\Network\Exception\BadRequestException Throws an exception if submitted data is invalid.
     */
    public function edit($id)
    {
        $this->request->allowMethod('patch');

        if ($this->request->getData('id') != $id) {
            throw new ConflictException('IDs don\'t match');
        }

        $role = $this->Roles->get($id);
        $role = $this->Roles->patchEntity($role, $this->request->getData());
        if (!$this->Roles->save($role)) {
            $this->log('Role edit failed ' . json_encode($role->errors()), 'error');
            throw new BadRequestException(['title' => 'Invalid data', 'detail' => [$role->errors()]]);
        }

        $this->set(compact('role'));
        $this->set('_serialize', ['role']);
    }

    /**
     * Delete an existing role.
     *
     * @param int $id Role ID.
     * @return \Cake\Network\Response
     * @throws \Cake\Network\Exception\InternalErrorException Throws an exception if an error occurs during deletion.
     */
    public function delete($id)
    {
        $this->request->allowMethod('delete');

        $role = $this->Roles->get($id);
        if (!$this->Roles->delete($role)) {
            throw new InternalErrorException('Could not delete role');
        }

        return $this->response
            ->withHeader('Content-Type', $this->request->contentType())
            ->withStatus(204);
    }
}

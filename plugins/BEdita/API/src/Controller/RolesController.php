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

/**
 * Controller for `/roles` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\RolesTable $Roles
 */
class RolesController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Roles';

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->set('_type', 'roles');
    }

    /**
     * Paginated roles list.
     *
     * @return void
     */
    public function index()
    {
        $query = $this->Roles->find('all');

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
}

<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
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
 * Controller for `/applications` endpoint.
 *
 * @since 4.6.0
 *
 * @property \BEdita\Core\Model\Table\ApplicationsTable $Applications
 */
class ApplicationsController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Applications';

    /**
     * Display available applications.
     *
     * @return void
     */
    public function index()
    {
        $query = $this->Applications->find()
            ->select(['id', 'name', 'description']);
        $data = $this->paginate($query);

        $this->set(compact('data'));
        $this->set('_serialize', ['data']);
    }
}

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
 * Controller for `/async_jobs` endpoint.
 *
 * @property \BEdita\Core\Model\Table\AsyncJobsTable $AsyncJobs
 */
class AsyncJobsController extends AppController
{
    /**
     * @inheritDoc
     */
    public $defaultTable = 'AsyncJobs';

    /**
     * Handle async_jobs endpoint.
     *
     * @return void
     */
    public function index(): void
    {
        $this->getRequest()->allowMethod(['GET', 'POST']);
        if ($this->getRequest()->is('GET')) {
            $query = $this->AsyncJobs->find();
            $data = $this->paginate($query);
            $this->set(compact('data'));
            $this->setSerialize(['data']);

            return;
        }
        $data = $this->getRequest()->getData();
        $asyncJob = $this->AsyncJobs->newEntity($data);
        $this->AsyncJobs->save($asyncJob);
        $this->set(compact('asyncJob'));
        $this->setSerialize(['asyncJob']);
    }
}

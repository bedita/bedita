<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller;

use Cake\Http\Exception\MethodNotAllowedException;

/**
 * Controller for `/async_jobs` endpoint.
 *
 * @property \BEdita\Core\Model\Table\AsyncJobsTable $AsyncJobs
 */
class AsyncJobsController extends ResourcesController
{
    /**
     * @inheritDoc
     */
    public $defaultTable = 'AsyncJobs';

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        if (!in_array($this->request->getMethod(), ['GET', 'POST'])) {
            throw new MethodNotAllowedException();
        }
    }
}

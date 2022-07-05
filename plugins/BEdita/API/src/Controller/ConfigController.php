<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller;

use BEdita\Core\State\CurrentApplication;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\ForbiddenException;

/**
 * Controller for `/config` endpoint.
 *
 * @since 4.0.0
 * @property \BEdita\Core\Model\Table\ConfigTable $Config
 */
class ConfigController extends ResourcesController
{
    /**
     * @inheritDoc
     */
    public $modelClass = 'Config';

    /**
     * Display available configurations.
     *
     * @return void
     */
    public function index(): void
    {
        if ($this->request->is('post')) {
            $this->request = $this->request->withData('context', 'app')
                ->withData('application_id', CurrentApplication::getApplicationId());
        } else {
            $query = $this->request->getQueryParams();
            $query['filter']['application_id'] = CurrentApplication::getApplicationId();
            $query['filter']['context'] = 'app';
            $this->request = $this->request->withQueryParams($query);
        }
        parent::index();
    }

    /**
     * Check entity validity in `PATCH`/`DELETE` calls in controller subclasses
     *
     * @param EntityInterface $entity
     * @return void
     */
    protected function checkEntity(EntityInterface $entity): void
    {
        if (
            $entity->get('application_id') != CurrentApplication::getApplicationId() ||
            $entity->get('context') != 'app'
        ) {
            throw new ForbiddenException();
        }
    }
}

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

namespace BEdita\API\Controller\Model;

use BEdita\API\Controller\AppController;
use BEdita\Core\Utility\ProjectModel;
use Cake\Event\Event;
use Cake\Http\Exception\NotAcceptableException;

/**
 * Controller for `/model/project` endpoint.
 *
 * @since 4.5.0
 *
 */
class ProjectController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();
        if ($this->components()->has('JsonApi')) {
            $this->components()->unload('JsonApi');
        }
        $this->RequestHandler->setConfig('viewClassMap.json', 'Json');
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event): void
    {
        if (!$this->request->is(['json'])) {
            throw new NotAcceptableException(
                __d('bedita', 'Bad request content type "{0}"', $this->request->getHeaderLine('Accept'))
            );
        }
    }

    /**
     * Get project schema.
     *
     * @return void
     */
    public function index(): void
    {
        $this->request->allowMethod(['get']);

        $model = ProjectModel::generate();

        $this->set($model);
        $this->set('_serialize', true);
    }
}

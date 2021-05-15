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

/**
 * Controller for `/model/project` endpoint.
 *
 * @since 4.5.0
 *
 */
class ProjectController extends AppController
{
    /**
     * JSON content type.
     *
     * @var string
     */
    const CONTENT_TYPE = 'application/json';

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        if ($this->components()->has('JsonApi')) {
            $this->components()->unload('JsonApi');
        }
        $this->viewBuilder()->setClassName('Json');
    }

    /**
     * {@inheritDoc}
     *
     * Intentionally left blank to override parent method.
     * Avoid content-type negotiation checks based on `Accept` header.
     *
     * @codeCoverageIgnore
     */
    public function beforeFilter(Event $event)
    {
    }

    /**
     * Get project schema.
     *
     * @return \Cake\Http\Response
     */
    public function index()
    {
        $this->request->allowMethod(['get']);

        $model = ProjectModel::generate();

        $this->set($model);
        $this->set('_serialize', true);

        return $this->render()
            ->withType(static::CONTENT_TYPE);
    }
}

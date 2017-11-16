<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
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
use BEdita\Core\Model\Schema\JsonSchema;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Routing\Router;

/**
 * Controller for `/model/schema/{type}` endpoint.
 *
 * @since 4.0.0
 *
 */
class SchemaController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->components()->unload('JsonApi');
        $this->viewBuilder()->className('Json');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function beforeFilter(Event $event)
    {
    }

    /**
     * Get JSON-SCHEMA of a type.
     *
     * @param string $typeName Name of an object type or of a resource type.
     * @return void
     */
    public function jsonSchema($typeName)
    {
        $this->request->allowMethod(['get']);

        $url = Router::fullBaseUrl() . $this->request->here();
        $this->set(JsonSchema::generate($typeName, $url));
        $this->set('_serialize', true);
    }
}

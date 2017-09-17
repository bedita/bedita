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
namespace BEdita\API\Controller;

use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\Router;
use Cake\Utility\Inflector;

/**
 * Controller for `/admin` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\RolesTable $Roles
 */
class AdminController extends ResourcesController
{

    /**
     * {@inheritDoc}
     */
    public $modelClass = null;

    /**
     * Resource name, one of `$this->config('allowedResources')`
     */
    public $resourceName = null;

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedResources' => [
            'applications',
            'async_jobs',
            'config',
            'endpoints',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        $this->resourceName = $this->request->getParam('item');
        if (!in_array($this->resourceName, $this->getConfig('allowedResources'))) {
            throw new MissingRouteException(['url' => $this->request->getRequestTarget()]);
        }

        $this->modelClass = Inflector::camelize($this->resourceName);

        parent::initialize();

        if (isset($this->JsonApi)) {
            $this->JsonApi->setConfig('resourceTypes', [$this->resourceName]);
        }

        $this->Auth->getAuthorize('BEdita/API.Endpoint')->setConfig('administratorOnly', true);
    }

    /**
     * {@inheritDoc}
     */
    protected function resourceUrl($id)
    {
        return Router::url(
            [
                '_name' => 'api:admin:resource',
                'item' => $this->resourceName,
                'id' => $id,
                'controller' => $this->name,
            ],
            true
        );
    }
}

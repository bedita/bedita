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

/**
 * Controller for `/config` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\ConfigTable $Table
 */
class ConfigController extends ResourcesController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Config';

    /**
     * {@inheritDoc}
     */
    public function index()
    {
        // Add `mine` filter to load common configuration and
        // application related configuration
        // Load only `core` and `app` contexts for now
        $queryParams = $this->request->getQueryParams();
        $this->request = $this->request->withQueryParams([
            'filter' => [
                'mine' => true,
                'context' => ['core', 'app']
            ]
        ]);

        parent::index();
        // restore original query params for the view
        $this->request = $this->request->withQueryParams($queryParams);
    }
}

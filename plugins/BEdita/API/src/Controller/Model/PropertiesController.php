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

/**
 * Controller for `/model/properties` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\PropertiesTable $Properties
 */
class PropertiesController extends ModelController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Properties';

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'object_types' => ['object_types'],
        ],
    ];
}

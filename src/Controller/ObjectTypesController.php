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

/**
 * Controller for `/model/object_types` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\ObjectTypesTable $ObjectTypes
 */
class ObjectTypesController extends ResourcesController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'ObjectTypes';

    /**
     * {@inheritDoc}
     */
    protected $_routeNamePrefix = 'api:model';

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'properties' => ['properties'],
            'left_relations' => ['relations'],
            'right_relations' => ['relations'],
        ],
    ];
}

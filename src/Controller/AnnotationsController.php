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

use BEdita\Core\Model\Action\ListRelatedObjectsAction;
use Cake\ORM\Association;
use Cake\Routing\Router;

/**
 * Controller for `/annotations` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\AnnotationsTable $Annotations
 */
class AnnotationsController extends ResourcesController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Annotations';

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'object' => [],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    protected function getAvailableUrl($relationship)
    {
        return Router::url(
            [
                '_name' => 'api:objects:index',
                'object_type' => 'objects'
            ],
            true
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return \BEdita\Core\Model\Action\ListRelatedObjectsAction
     */
    protected function getAssociatedAction(Association $association)
    {
        return new ListRelatedObjectsAction(compact('association'));
    }
}

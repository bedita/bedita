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

use BEdita\Core\Model\Action\ListRelatedFoldersAction;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Association;

/**
 * Controller for `/folders` endpoint.
 *
 * The main aim is to bridge `parent` API relationship to `parents` BTM association.
 *
 * @since 4.0.0
 */
class FoldersController extends ObjectsController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Folders';

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'parent' => ['folders'],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * `parent` relationship is valid and will return `parents` association.
     * `parents` relationship is not allowed.
     */
    protected function findAssociation($relationship)
    {
        if ($relationship === 'parents') {
            throw new NotFoundException(__d('bedita', 'Relationship "{0}" does not exist', $relationship));
        }

        if ($relationship === 'parent') {
            return $this->Table->association('Parents');
        }

        return parent::findAssociation($relationship);
    }

    /**
     * {@inheritDoc}
     */
    protected function getAvailableUrl($relationship)
    {
        if ($relationship === 'parent') {
            $relationship = 'parents';
        }

        return parent::getAvailableUrl($relationship);
    }

    /**
     * {@inheritDoc}
     *
     * @return \BEdita\Core\Model\Action\ListRelatedFoldersAction
     */
    protected function getAssociatedAction(Association $association)
    {
        return new ListRelatedFoldersAction(compact('association'));
    }

    /**
     * {@inheritDoc}
     *
     * Folder with Parents association allows GET and PATCH
     */
    protected function setRelationshipsAllowedMethods(Association $association)
    {
        parent::setRelationshipsAllowedMethods($association);

        if ($association->getName() === 'Parents') {
            $allowedMethods = ['get', 'patch'];
            $this->request->allowMethod($allowedMethods);
        }
    }
}

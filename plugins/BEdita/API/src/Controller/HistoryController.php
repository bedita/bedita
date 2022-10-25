<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Association;
use Cake\ORM\Table;
use Cake\Utility\Inflector;

/**
 * Controller for `/history` endpoint.
 *
 * @since 4.1.0
 * @property \BEdita\Core\Model\Table\HistoryTable $Table
 */
class HistoryController extends ResourcesController
{
    /**
     * @inheritDoc
     */
    public $modelClass = 'History';

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'users' => ['users'],
        ],
    ];

    /**
     * @inheritDoc
     */
    protected function findAssociation(string $relationship, ?Table $table = null): Association
    {
        $relationship = Inflector::underscore($relationship);
        $association = $this->Table->associations()->getByProperty($relationship);
        if (empty($association)) {
            throw new NotFoundException(__d('bedita', 'Relationship "{0}" does not exist', $relationship));
        }

        return $association;
    }

    /**
     * @inheritDoc
     */
    protected function setRelationshipsAllowedMethods(Association $association)
    {
        $this->request->allowMethod(['get']);
    }
}

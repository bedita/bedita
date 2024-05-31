<?php
declare(strict_types=1);

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

use Cake\Core\Configure;
use Cake\ORM\Association;

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
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'user' => ['users'],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $this->defaultTable = (string)Configure::read('History.table', 'History');

        parent::initialize();
    }

    /**
     * @inheritDoc
     */
    protected function setRelationshipsAllowedMethods(Association $association)
    {
        $this->request->allowMethod(['get']);
    }
}

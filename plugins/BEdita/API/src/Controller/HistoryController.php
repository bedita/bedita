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

use BEdita\Core\Model\Action\ListEntitiesAction;
use BEdita\Core\Utility\JsonApiSerializable;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * Controller for `/history` endpoint.
 *
 * @since 4.1.0
 *
 */
class HistoryController extends AppController
{
    /**
     * History table
     *
     * @var \Cake\ORM\Table
     */
    protected $HistoryTable;

    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();

        $historyTable = (string)Configure::read('History.table', 'History');
        $this->HistoryTable = TableRegistry::getTableLocator()->get($historyTable);
    }

    /**
     * History index.
     *
     * @return void
     */
    public function index()
    {
        $this->request->allowMethod('get');

        $filter = (array)$this->request->getQuery('filter');
        $action = new ListEntitiesAction(['table' => $this->HistoryTable]);
        $query = $action(compact('filter'));
        $this->set('_fields', $this->request->getQuery('fields', []));
        $data = $this->paginate($query);

        $this->set(compact('data'));
        $this->set([
            '_serialize' => ['data'],
            '_jsonApiOptions' => JsonApiSerializable::JSONAPIOPT_EXCLUDE_RELATIONSHIPS |
                JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS
        ]);
    }
}

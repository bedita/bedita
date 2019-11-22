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

use BEdita\Core\History\HistoryTableRegistry;
use BEdita\Core\Utility\JsonApiSerializable;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
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
    public function initialize()
    {
        parent::initialize();

        $historyTable = (string)Configure::read('History.table', 'History');
        $this->HistoryTable = HistoryTableRegistry::get($historyTable);
    }

    /**
     * View object history.
     *
     * @param int|string $id Object ID.
     * @return void
     */
    public function view($id)
    {
        $this->checkExistence($id);
        $query = $this->HistoryTable->find('history', [$id]);
        $data = $this->paginate($query);

        $this->set(compact('data'));
        $this->set([
            '_serialize' => ['data'],
            '_jsonApiOptions' => JsonApiSerializable::JSONAPIOPT_EXCLUDE_RELATIONSHIPS |
                JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS
        ]);
    }

    /**
     * View user activity history.
     *
     * @param int|string $id User ID.
     * @return void
     */
    public function user($id)
    {
        $this->checkExistence($id, 'Users');
        $query = $this->HistoryTable->find('activity', [$id]);
        $data = $this->paginate($query);

        $this->set(compact('data'));
        $this->set([
            '_serialize' => ['data'],
            '_jsonApiOptions' => JsonApiSerializable::JSONAPIOPT_EXCLUDE_RELATIONSHIPS |
                JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS
        ]);
    }

    /**
     * Check for object/user existence
     *
     * @param string|int $id Object or user id
     * @param string $type Type to search, 'Objects' or 'Users'
     * @return void
     * @throws NotFoundException
     */
    protected function checkExistence($id, string $type = 'Objects')
    {
        $Table = TableRegistry::getTableLocator()->get($type);
        if (!$Table->exists(['id' => $id])) {
            throw new NotFoundException(__d('bedita', 'Unable to find "{0}" with ID "{1}"', $type, $id));
        }
    }
}

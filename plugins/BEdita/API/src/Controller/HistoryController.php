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

use BEdita\Core\History\HistoryInterface;
use BEdita\Core\Utility\JsonApiSerializable;
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
     * Get object history.
     *
     * @param int $id Object ID.
     * @return void
     */
    public function view($id)
    {
        $Objects = TableRegistry::getTableLocator()->get('Objects');
        if (!$Objects->exists(['id' => $id])) {
            throw new NotFoundException(__d('bedita', 'Object "{0}" not found', $id));
        }
        /** @var HistoryInterface $historyModel */
        $historyModel = $Objects->behaviors()
                ->get('History')
                ->historyModel;
        $data = $historyModel->readEvents($id);

        $this->set(compact('data'));
        $this->set([
            '_serialize' => ['data'],
            '_jsonApiOptions' => JsonApiSerializable::JSONAPIOPT_EXCLUDE_RELATIONSHIPS | JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS
        ]);
    }
}

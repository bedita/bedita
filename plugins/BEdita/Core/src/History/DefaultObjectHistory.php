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

namespace BEdita\Core\History;

use Cake\ORM\TableRegistry;

/**
 * Default Object Hisotry model.
 *
 * Default implementation of HisttoryInterface using `objects_history` table
 */
class DefaultObjectHistory implements HistoryInterface
{
    /**
     * Object History table
     *
     * @var \BEdita\Core\Model\Table\ObjectHistoryTable
     */
    protected $HistoryTable;

    /**
     * Object constructor
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->HistoryTable = TableRegistry::getTableLocator()->get('ObjectHistory');
    }

    /**
     * {@inheritDoc}
     */
    public function addEvent(array $data): void
    {
        $entity = $this->HistoryTable->newEntity($data);
        $this->HistoryTable->saveOrFail($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function readEvents($objectId, array $options = []): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function readUserEvents($userId, array $options = []): array
    {
        return [];
    }
}

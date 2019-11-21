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

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use LogicException;

/**
 * Registry/factory class for history tables.
 *
 * @since 4.1.0
 */
class HistoryTableRegistry
{
    /**
     * Provide history table and check for finder presence.
     *
     * @param string $alias Table name or alias.
     * @return Table
     * @throws \LogicException When no suitable history table is found.
     */
    public static function get(string $alias): Table
    {
        $table = TableRegistry::getTableLocator()->get($alias);
        if (!$table->hasFinder('history') || !$table->hasFinder('activity')) {
            throw new LogicException(__d('bedita', 'History table must implement "history" and "activity" finders'));
        }

        return $table;
    }
}

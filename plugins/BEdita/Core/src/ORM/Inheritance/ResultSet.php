<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\ORM\Inheritance;

use Cake\ORM\ResultSet as CakeResultSet;

/**
 * As `Cake\ORM\ResultSet` but takes care of the presence of all tables belonging to the inheritance
 * and if needed add them to `self::$_map` so that all data are correctly passed
 * to `\BEdita\Core\ORM\Inheritance\ExtensionOf::transformRow()` method
 *
 * @since 4.0.0
 */
class ResultSet extends CakeResultSet
{

    /**
     * Map of table alias to add to `self::$_map` grouping results
     *
     * @var array
     */
    protected $inheritedMap = [];

    /**
     * {@inheritDoc}
     */
    protected function _calculateColumnMap($query)
    {
        parent::_calculateColumnMap($query);

        $repository = $query->repository();
        $inheritedTables = $repository->inheritedTables(true);
        if (empty($inheritedTables)) {
            return;
        }

        if (empty($this->_map[$repository->alias()])) {
            $this->inheritedMap[$repository->alias()] = [$repository->alias() => $repository->alias()];
        }

        // add to map all missing inherited tables
        foreach ($inheritedTables as $inheritedTable) {
            if (!empty($this->_map[$inheritedTable->alias()])) {
                continue;
            }

            $this->inheritedMap[$inheritedTable->alias()] = [$inheritedTable->alias() => $inheritedTable->alias()];
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function _groupResult($row)
    {
        if (!empty($this->inheritedMap)) {
            $row += array_fill_keys(array_keys($this->inheritedMap), true);
            $this->_map += $this->inheritedMap;
        }

        return parent::_groupResult($row);
    }
}

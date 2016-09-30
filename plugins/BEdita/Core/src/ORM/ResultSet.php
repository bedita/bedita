<?php

namespace BEdita\Core\ORM;

use Cake\ORM\ResultSet as CakeResultSet;

class ResultSet extends CakeResultSet
{

    protected $inheritedMap = [];

    /**
     * {@inheritDoc}
     */
    protected function _calculateColumnMap($query)
    {
        parent::_calculateColumnMap($query);

        $repository = $query->repository();
        $extensionOf = $repository->associations()->type('ExtensionOf');
        if (empty($extensionOf)) {
            return;
        }

        if (empty($this->_map[$repository->alias()])) {
            $this->inheritedMap[$repository->alias()] = [$repository->alias() => $repository->alias()];
        }

        // add to map all inherited table
        foreach ($repository->inheritedTables(true) as $inheritedTable) {
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

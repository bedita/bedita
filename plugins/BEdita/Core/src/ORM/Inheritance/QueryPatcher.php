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

use Cake\ORM\Query;
use Cake\ORM\Table;

/**
 * QueryPatcher class.
 *
 * Patch the original Query instance using the Table inheritance
 *
 * @since 4.0.0
 */
class QueryPatcher
{
    /**
     * Table instance.
     *
     * @var \Cake\ORM\Table
     */
    protected $table = null;

    /**
     * Query instance.
     *
     * @var \Cake\ORM\Query
     */
    protected $query = null;

    /**
     * It keeps trace of inheritance to avoid repeated operations
     *
     * @var array
     */
    protected $inheritanceMap = [];

    /**
     * Constructor.
     *
     * @param \Cake\ORM\Table $table The Table instance
     */
    public function __construct(Table $table)
    {
        if (!$table->hasBehavior('ClassTableInheritance')) {
            throw new \InvalidArgumentException(sprintf(
                'Table %s must use ClassTableInheritance behavior',
                $table->alias()
            ));
        }
        $this->table = $table;
    }

    /**
     * Return the complete table inheritance of `$this->table`.
     * Once obtained it returns its value without recalculate it.
     *
     * @return array
     */
    protected function inheritedTables()
    {
        if (array_key_exists('tables', $this->inheritanceMap)) {
            return $this->inheritanceMap['tables'];
        }

        $this->inheritanceMap['tables'] = $this->table->inheritedTables(true);

        return $this->inheritanceMap['tables'];
    }

    /**
     * Prepare the Query to patch
     *
     * @param \Cake\ORM\Query $query The Query to patch
     * @return $this
     */
    public function patch(Query $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Execute all the patches and return the query patched
     *
     * @return \Cake\ORM\Query
     */
    public function all()
    {
        $this->contain();

        return $this->query;
    }

    /**
     * Arrange contain mapping tables to the right association
     *
     * * add inherited tables
     * * arrange tables in \Cake\ORM\Query::contain() to the right inherited table
     * * override contain data with that calculated
     *
     * @return $this
     */
    public function contain()
    {
        $inheritedTables = array_map(function (Table $table) {
            return $table->alias();
        }, $this->inheritedTables());

        $contain = $this->query->contain();
        $contain += array_fill_keys($inheritedTables, []);
        $result = [];

        foreach ($contain as $tableName => $tableContain) {
            $containString = $this->buildContainString($tableName);
            if ($containString === false) {
                $containString = $tableName;
            }
            $result[$containString] = $tableContain;
        }

        $this->query->contain($result, true);

        return $this;
    }

    /**
     * Given a table name return the right contain string
     *
     * If `$tableName` is a direct association to current table return it
     * else search if `$tableName` is associated to a inherited table
     *
     * Return false if `$tableName` is not found as association to any table
     *
     * @param string $tableName The starting table name
     * @return string|false
     */
    public function buildContainString($tableName)
    {
        if ($this->table->association($tableName)) {
            return $tableName;
        }

        foreach ($this->inheritedTables() as $inherited) {
            $containString = empty($containString) ? $inherited->alias() : $containString . '.' . $inherited->alias();
            if (!$inherited->association($tableName)) {
                continue;
            }

            return $containString . '.' . $tableName;
        }

        return false;
    }
}

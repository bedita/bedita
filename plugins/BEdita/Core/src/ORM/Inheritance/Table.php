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

use BadMethodCallException;
use Cake\ORM\Query as CakeQuery;
use Cake\ORM\Table as CakeTable;
use Cake\ORM\TableRegistry;

/**
 * Base Table class used by tables that needs class table inheritance (CTI)
 *
 * It exposes an `extensionOf()` method to add a `\BEdita\Core\ORM\Association\ExtensionOf` association
 * with a target table from which inherit fields.
 *
 * Once added that association the current table will find and save fields
 * of all inherited tables as own fields in a transparent way.
 *
 * Every Table can inherit just one table and eventually inherits other tables by nested inheritance
 *
 * @since 4.0.0
 */
class Table extends CakeTable
{

    /**
     * Table that is being inherited by this one.
     *
     * @var \Cake\ORM\Table|string|null
     */
    protected $inheritedTable = null;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        // Attach event handler for inheritance.
        $this->eventManager()->on(new InheritanceEventHandler());
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        return new Query($this->getConnection(), $this);
    }

    /**
     * Configure this table to inherit from another table.
     *
     * @param string|\Cake\ORM\Table $associated The extended table. It can be either a registry alias or an instance.
     * @return $this
     */
    public function extensionOf($associated)
    {
        // If it is an alias, and it already exists in the registry, immediately load the instance.
        if (is_string($associated) && TableRegistry::exists($associated)) {
            $associated = TableRegistry::get($associated);
        }
        $this->inheritedTable = $associated;

        if ($this->inheritedTable instanceof CakeTable) {
            // Ensure that inherited tables have their association collections fixed first.
            $this->inheritedTables();

            // Inherit associations from inherited table.
            $this->_associations = new AssociationCollection($this, $this->inheritedTable->_associations);
            // TODO: Same for behaviors?
        }

        return $this;
    }

    /**
     * Say if table `$tableName` is inherited by current Table.
     *
     * @param string $tableName The table name to check
     * @param bool $nested If it must check nested inheritance
     * @return bool
     */
    public function isTableInherited($tableName, $nested = false)
    {
        if ($nested) {
            $inheritedTables = $this->inheritedTables();
            $found = array_filter($inheritedTables, function (CakeTable $table) use ($tableName) {
                return $table->getAlias() === $tableName;
            });

            return count($found) > 0;
        }

        $inheritedTable = $this->inheritedTable();

        return $inheritedTable !== null && $inheritedTable->getAlias() === $tableName;
    }

    /**
     * Return the inherited table from current table.
     *
     * @return \Cake\ORM\Table|null
     */
    public function inheritedTable()
    {
        if (is_string($this->inheritedTable)) {
            $this->extensionOf(TableRegistry::get($this->inheritedTable));
        }

        return $this->inheritedTable;
    }

    /**
     * Return the inherited tables from current Table.
     *
     * @return \Cake\ORM\Table[]
     */
    public function inheritedTables()
    {
        $inheritedTable = $this->inheritedTable();
        if ($inheritedTable === null) {
            return [];
        }

        if (!($inheritedTable instanceof self)) {
            return [$inheritedTable];
        }

        return array_merge([$inheritedTable], $inheritedTable->inheritedTables());
    }

    /**
     * Find all common tables in inheritance chain.
     *
     * @param \Cake\ORM\Table $table Table to compare current table to.
     * @return \Cake\ORM\Table[]
     */
    public function commonInheritance(CakeTable $table)
    {
        if (!($table instanceof self)) {
            return in_array($table, $this->inheritedTables(), true) ? [$table] : [];
        }

        $inherited = array_merge(
            array_reverse($this->inheritedTables()),
            [$this]
        );
        $table = array_merge(
            array_reverse($table->inheritedTables()),
            [$table]
        );

        $common = [];
        $i = 0;
        while (isset($inherited[$i]) && isset($table[$i]) && $inherited[$i] === $table[$i]) {
            array_unshift($common, $inherited[$i]);
            $i++;
        }

        return $common;
    }

    /**
     * {@inheritDoc}
     */
    public function hasFinder($type)
    {
        if (parent::hasFinder($type) === true) {
            return true;
        }

        $inheritedTable = $this->inheritedTable();
        if ($inheritedTable !== null) {
            return $inheritedTable->hasFinder($type);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function callFinder($type, CakeQuery $query, array $options = [])
    {
        if (parent::hasFinder($type)) {
            return parent::callFinder($type, $query, $options);
        }

        $inheritedTable = $this->inheritedTable();
        if ($inheritedTable !== null) {
            return $inheritedTable->callFinder($type, $query, $options);
        }

        throw new BadMethodCallException(
            sprintf('Unknown finder method "%s"', $type)
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param bool $inheritedFields Should fields from inherited tables be considered?
     */
    public function hasField($field, $inheritedFields = true)
    {
        $result = parent::hasField($field);
        $inheritedTable = $this->inheritedTable();

        if ($result || !$inheritedFields || $inheritedTable === null) {
            return $result;
        }

        return $inheritedTable->hasField($field);
    }

    /**
     * Perform operations when cloning table.
     *
     * @return void
     */
    public function __clone()
    {
        $this->_associations = clone $this->_associations;
        $this->_behaviors = clone $this->_behaviors;
        $this->_eventManager = clone $this->_eventManager;
    }
}

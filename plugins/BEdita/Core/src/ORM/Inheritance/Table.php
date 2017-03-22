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
use BEdita\Core\ORM\Association\ExtensionOf;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Association;
use Cake\ORM\Query as CakeQuery;
use Cake\ORM\Table as CakeTable;

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
 *
 * @method \BEdita\Core\ORM\Inheritance\Query find($type = 'all', $options = [])
 */
class Table extends CakeTable
{
    /**
     * {@inheritDoc}
     */
    public function query()
    {
        return new Query($this->getConnection(), $this);
    }

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        $events = parent::implementedEvents();
        $eventMap = [
            'Model.beforeFind' => 'inheritanceBeforeFind',
            'Model.beforeSave' => 'inheritanceBeforeSave',
        ];

        foreach ($eventMap as $name => $listener) {
            if (array_key_exists($name, $events)) {
                $events[$name] = [
                    ['callable' => $events[$name]],
                    ['callable' => $listener]
                ];
            } else {
                $events[$name] = $listener;
            }
        }

        return $events;
    }

    /**
     * Arrange \Cake\ORM\Query before execute the query.
     * In details:
     *
     * * build contain of inherited tables
     * * prepare format result method
     *
     * @param \Cake\Event\Event $event The event dispatched
     * @param \BEdita\Core\ORM\Inheritance\Query $query The query object
     * @param \ArrayObject $options Options
     * @param bool $primary Indicates whether or not this is the root query or an associated query
     * @return void
     */
    public function inheritanceBeforeFind(Event $event, Query $query, \ArrayObject $options, $primary)
    {
        if (!$primary || $this->inheritedTable() === null) {
            return;
        }

        $query->fixAll();
    }

    /**
     * Dirty the Entity property corresponding to the inherited table to trigger `ExtensionOf::saveAssociated()`
     *
     * @see \BEdita\Core\ORM\Association\ExtensionOf::saveAssociated()
     * @param \Cake\Event\Event $event The event dispatched
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @param \ArrayObject $options The save options
     * @return void
     */
    public function inheritanceBeforeSave(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        $inheritedTable = $this->inheritedTable();
        if ($inheritedTable === null) {
            return;
        }

        $property = $this->association($inheritedTable->getAlias())->getProperty();

        $entity->dirty($property, true);
    }

    /**
     * Creates a new ExtensionOf association between `$source` table and a target
     * table. An "extension of" association is a 1-1 relationship.
     *
     * A Table can have only one ExtensionOf relation.
     * Trying to add more ExtensionOf association to the same table will throw an exception
     *
     * Target table can be inferred by its name, which is provided in the
     * second argument, or you can either pass the class name to be instantiated or
     * an instance of it directly.
     *
     * The options array accept the following keys:
     *
     * - className: The class name of the target table object
     * - targetTable: An instance of a table object to be used as the target table
     * - conditions: array with a list of conditions to filter the join with
     * - strategy: The loading strategy to use. 'join' and 'select' are supported.
     * - finder: The finder method to use when loading records from this association.
     *   Defaults to 'all'. When the strategy is 'join', only the fields, containments,
     *   and where conditions will be used from the finder.
     *
     * This method will return the association object that was built.
     *
     * Other options as `joinType` and `dependent` if present are removed
     * to use defaults defined in ExtensionOf association
     *
     * @param string $associated the alias for the target table. This is used to
     * uniquely identify the association
     * @param array $options list of options to configure the association definition
     * @return \BEdita\Core\ORM\Association\ExtensionOf
     * @throws \RuntimeException When an ExtensionOf association is already present
     */
    public function extensionOf($associated, array $options = [])
    {
        $inheritedTable = $this->inheritedTable();
        if ($inheritedTable !== null) {
            throw new \RuntimeException(sprintf(
                '"%s" has already an ExtensionOf association with %s',
                $this->getAlias(),
                $inheritedTable->getAlias()
            ));
        }

        $options = array_merge($options, [
            'sourceTable' => $this,
            'foreignKey' => $this->getPrimaryKey(),
        ]);
        $options = array_diff_key($options, array_flip(['joinType', 'dependent']));
        $association = new ExtensionOf($associated, $options);

        return $this->_associations->add($association->getName(), $association);
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
        $inheritedTables = $this->inheritedTables($nested);
        $found = array_filter($inheritedTables, function (Table $table) use ($tableName) {
            return $table->getAlias() === $tableName;
        });

        return count($found) > 0;
    }

    /**
     * Return the inherited table from current table.
     *
     * @return \Cake\ORM\Table|null
     */
    public function inheritedTable()
    {
        $association = $this->associations()->type('ExtensionOf');
        $association = current($association);
        if (!($association instanceof Association)) {
            return null;
        }

        return $association->getTarget();
    }

    /**
     * Return the inherited tables from current Table.
     *
     * By default return the direct inherited table (no nested).
     * To get the the all nested inherited tables pass `$nested = true`.
     *
     * @param bool $nested If it must return all the inherited tables or just direct inherited table
     * @return \Cake\ORM\Table[]
     */
    public function inheritedTables($nested = false)
    {
        $inheritedTable = $this->inheritedTable();
        if ($inheritedTable === null) {
            return [];
        }

        if (!$nested || !($inheritedTable instanceof self)) {
            return [$inheritedTable];
        }

        return array_merge([$inheritedTable], $inheritedTable->inheritedTables(true));
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
    public function hasField($field, $inheritedFields = false)
    {
        $result = parent::hasField($field);
        $inheritedTable = $this->inheritedTable();

        if ($result || !$inheritedFields || $inheritedTable === null) {
            return $result;
        }

        return $inheritedTable->hasField($field, true);
    }
}

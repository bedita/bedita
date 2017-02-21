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

use BEdita\Core\ORM\Association\ExtensionOf;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
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
        if (!$primary || empty($this->inheritedTables())) {
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
        $inheritedTable = current($this->inheritedTables());
        if (empty($inheritedTable)) {
            return;
        }

        $property = $this->association($inheritedTable->alias())->getProperty();

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
        $alreadyExists = $this->associations()->type('ExtensionOf');
        if (!empty($alreadyExists)) {
            throw new \RuntimeException(sprintf(
                '"%s" has already an ExtensionOf association with %s',
                $this->getAlias(),
                $alreadyExists[0]->getTarget()->getAlias()
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
     * Return the inherited tables from current Table.
     *
     * By default return the direct inherited table (no nested).
     * To get the the all nested inherited tables pass `$nested = true`.
     *
     * @param bool $nested If it must return all the inherited tables or just direct inherited table
     * @return array
     */
    public function inheritedTables($nested = false)
    {
        $associations = $this->_associations->type('ExtensionOf');
        if (empty($associations)) {
            return [];
        }

        $association = array_shift($associations);
        if (!$nested || !($association->target() instanceof Table)) {
            return [$association->target()];
        }

        return array_merge([$association->target()], $association->target()->inheritedTables(true));
    }
}

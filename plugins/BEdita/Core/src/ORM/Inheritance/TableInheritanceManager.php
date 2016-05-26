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
use Cake\ORM\Table;

/**
 * TableInheritanceManager class
 *
 * Handle the table inheritance executing task as:
 *
 * - add table to inheritance associating source table with target table via ExtensionOf asssociation
 * - remove table to inheritance
 * - return an array of inherited tables
 *
 * @since 4.0.0
 */
class TableInheritanceManager
{
    /**
     * Add to `$source` table an inherit table
     *
     * `$targetConf` is used for customize the inheritance:
     *
     * - `tableName` the target table name to inherit
     * - `className` the target table class name
     *
     * Create a new `ExtensionOf` association with `$source` table (if not already exists)
     *
     * @see self::createAssociation() To know `$targetConf` possible attributes
     * @throws \RuntimeException When you try to add a table but the inherit already exists
     *                           or when trying to add table already inherited
     *                           or when self::checkAssociation() fails
     * @param \Cake\ORM\Table $source The source table instance
     * @param array $targetConf The configuration of inherited table
     * @return $this
     */
    public function addTable(Table $source, $targetConf = [])
    {
        $targetConf += ['className' => null];

        if (empty($targetConf['tableName'])) {
            throw new \InvalidArgumentException(sprintf(
                '"tableName" is required. %s can not add table to the inheritance',
                $source->alias()
            ));
        }

        $inheritedTable = current($this->inheritedTables($source));
        if (!empty($inheritedTable) && $inheritedTable->alias() != $targetConf['tableName']) {
            throw new \RuntimeException(sprintf(
                '%s already inherits table %s',
                $source->alias(),
                $inheritedTable->alias()
            ));
        }

        if ($source->association($targetConf['tableName'])) {
            $this->checkAssociation($source, $targetConf);
            return $this;
        }

        $this->createAssociation($source, $targetConf['tableName'], $targetConf);

        return $this;
    }

    /**
     * Creates a new ExtensionOf association between `$source` table and a target
     * table. An "extension of" association is a 1-1 relationship.
     *
     * Target table can be inferred by its name, which is provided in the
     * second argument, or you can either pass the class name to be instantiated or
     * an instance of it directly.
     *
     * The options array accept the following keys:
     *
     * - className: The class name of the target table object
     * - targetTable: An instance of a table object to be used as the target table
     * - cascadeCallbacks: Set to true if you want CakePHP to fire callbacks on
     *   cascaded deletes. If false the ORM will use deleteAll() to remove data.
     *   When true records will be loaded and then deleted.
     * - conditions: array with a list of conditions to filter the join with
     * - strategy: The loading strategy to use. 'join' and 'select' are supported.
     * - finder: The finder method to use when loading records from this association.
     *   Defaults to 'all'. When the strategy is 'join', only the fields, containments,
     *   and where conditions will be used from the finder.
     *
     * This method will return the association object that was built.
     *
     * Other options as `joinType` and `dependent`are removed
     * to use defaults defined in ExtensionOf association
     *
     * @param \Cake\ORM\Table $source The source table instance
     * @param string $associated the alias for the target table. This is used to
     * uniquely identify the association
     * @param array $options list of options to configure the association definition
     * @return \BEdita\Core\ORM\Association\ExtensionOf
     */
    protected function createAssociation(Table $source, $associated, array $options = [])
    {
        $options = array_merge($options, [
            'sourceTable' => $source,
            'foreignKey' => $source->primaryKey()
        ]);
        $options = array_diff_key($options, array_flip(['joinType', 'dependent']));

        $association = new ExtensionOf($associated, $options);
        return $source->associations()->add($association->name(), $association);
    }

    /**
     * Remove a table from the inheritance of `$source` table.
     *
     * Only direct inheritance table can be removed.
     * Removing the inheritance will also remove the `ExtensionOf` association.
     *
     * @throws \RuntimeException When trying to remove a wrong table
     * @param \Cake\ORM\Table $source The source table instance
     * @param string $tableName The table name to remove
     * @return $this
     */
    public function removeTable(Table $source, $tableName)
    {
        if (!$this->isTableInherited($source, $tableName)) {
            throw new \RuntimeException(sprintf(
                '"%s" is not inherited or is a nested inheritance. You can remove only direct inheritance',
                $tableName
            ));
        }

        $source->associations()->remove($tableName);
        return $this;
    }

    /**
     * Check if the `$source` table has a valid association against `$targetConf`.
     *
     * To be valid:
     *
     * * it must be `ExtensionOf` association
     * * it must have "INNER" join type
     * * the foreign key on target table must to be equal to source primary key
     * * the class name used must be equal to `$targetConf['className']` (if any is set)
     *
     * @throws \RuntimeException When the association is not valid
     * @param \Cake\ORM\Table $source The source table instance
     * @param array $targetConf an array of inherited conf to check
     * @return void
     */
    protected function checkAssociation(Table $source, array $targetConf = [])
    {
        $association = $source->association($targetConf['tableName']);
        if (!$association) {
            throw new \RuntimeException(sprintf(
                '"%s" is not associated to %s',
                $source->alias(),
                $targetConf['tableName']
            ));
        }

        if (!($association instanceof ExtensionOf)) {
            throw new \RuntimeException(sprintf(
                '"%s" is already associated with not compatible association',
                $targetConf['tableName']
            ));
        }

        if (strtolower($association->joinType()) != 'inner') {
            throw new \RuntimeException(sprintf(
                '"%s" is joined with "%s" type. It must be "INNER"',
                $targetConf['tableName'],
                $association->joinType()
            ));
        }

        if ($association->foreignKey() != $source->primaryKey()) {
            throw new \RuntimeException(sprintf(
                'foreignKey "%s" must be identical to primary key "%s".',
                $association->foreignKey(),
                $source->primaryKey()
            ));
        }

        if (isset($targetConf['className']) && $targetConf['className'] != $association->className()) {
            throw new \RuntimeException(sprintf(
                'className "%s" mismatch the previous configuration "%s".',
                $targetConf['className'],
                $association->className()
            ));
        }
    }

    /**
     * Say if table `$tableName` is inherited by `$source` table.
     *
     * @param \Cake\ORM\Table $source The source table instance
     * @param string $tableName The table name to check
     * @param bool $nested If it must check nested inheritance
     * @return bool
     */
    public function isTableInherited(Table $source, $tableName, $nested = false)
    {
        $inheritedTables = $this->inheritedTables($source, $nested);
        $found = array_filter($inheritedTables, function ($table) use ($tableName) {
            return $table->alias() === $tableName;
        });

        return count($found) > 0;
    }

    /**
     * Return the inherited tables from $source Table.
     *
     * By default return the direct inherited table (no nested).
     * To get the the all nested inherited tables pass `$nested = true`.
     *
     * @param \Cake\ORM\Table $source The source table instance
     * @param bool $nested If it must return all the inherited tables or just direct inherited table
     * @return array
     */
    public function inheritedTables(Table $source, $nested = false)
    {
        $associations = $source->associations()->type('ExtensionOf');
        if (empty($associations)) {
            return [];
        }

        $association = array_shift($associations);
        if (!$nested) {
            return [$association->target()];
        }

        return array_merge([$association->target()], $this->inheritedTables($association->target(), true));
    }
}

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

namespace BEdita\Core\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use RuntimeException;

/**
 * ClassTableInheritance behavior
 *
 * It is responsabile to handle Class Table Inheritance.
 *
 * Adding tables to the inheritance implies that
 * the current table will show fields of all inherited tables as own fields in a transparent way.
 *
 * Every Table can inherit just one table and eventually inherits other tables by
 * nested inheritance i.e. to inherit tables inherited from others
 *
 * Example of usage:
 *
 * ```
 * // in MainTable table class
 * public function initialize(array $config)
 * {
 *     $this->addBehavior('BEdita/Core.ClassTableInheritance', [
 *         'table' => 'InheritedOne',
 *         'className' => 'Class\Namespace\InheritedOneTable'
 *     ]);
 * }
 *
 * // in InheritedOne table class
 * public function initialize(array $config)
 * {
 *     $this->addBehavior('BEdita/Core.ClassTableInheritance', [
 *         'table' => 'InheritedTwo',
 *         'className' => 'Class\Namespace\InheritedTwoTable'
 *     ]);
 * }
 * ```
 *
 * in this case `MainTable` inherits `InheritedOne` that inherits `InheritedTwo`.
 * So `MainTable` inherits `InheritedTwo` too.
 *
 * @since 4.0.0
 */
class ClassTableInheritanceBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'implementedMethods' => [
            'isTableInherited' => 'isTableInherited',
            'inheritedTables' => 'inheritedTables',
            'arrangeContain' => 'arrangeContain',
            'buildContainString' => 'buildContainString'
        ]
    ];

    /**
     * Initialize the Behavior configuring table association
     *
     * Configuration options are:
     *
     * * `table` the table to inherit (as Table alias)
     * * `className` the table class name
     *
     * After the initialization the Behavior config will be
     * something like
     *
     * ```
     * 'table' => [
     *      'alias' => 'Objects',
     *      'className' => 'BEdita\Core\Model\Table\ObjectsTable,
     *      'associationCreated' => true
     * ]
     * ```
     *
     * @param array $config configuration options
     * @return void
     */
    public function initialize(array $config)
    {
        $this->config(['table' => null, 'className' => null]);
        $this->addTable($config['table'], $config);
    }

    /**
     * Arrange \Cake\ORM\Query before execute the query.
     * In details:
     *
     * * build contain of inherited tables
     * * prepare format result method
     *
     * @param \Cake\Event\Event $event The event dispatched
     * @param \Cake\ORM\Query $query The query object
     * @param \ArrayObject $options Options
     * @param bool $primary Indicates whether or not this is the root query or an associated query
     * @return void
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary)
    {
        if ($primary) {
            $this->arrangeContain($query);
        }

        $query->formatResults(function ($results) {
            return $results->map(function ($row) {
                return $this->flatten($row);
            });
        });
    }

    /**
     * Add table to the inheritance.
     *
     * `$conf` is used for customize the inheritance:
     *
     * * `className` the associated table class name
     *
     * Adding table create a new "belongsTo" association with current table (if not already exists)
     * and write the behavior configuration.
     * When association is created the key `associationCreated` in conf is set to true
     *
     * @throws \RuntimeException When you try to add a table but the inherit already exists
     *                           or when trying to add table already inherited
     *                           or when self::checkAssociation() fails
     * @param string $tableName The table name
     * @param array $conf The configuration options
     * @return $this
     */
    public function addTable($tableName, array $conf = [])
    {
        $conf = ['alias' => $tableName] + $conf + ['className' => null];
        if (!empty($this->config('table'))) {
            throw new RuntimeException('You can inherit just one table');
        }

        if ($this->isTableInherited($tableName, true)) {
            throw new RuntimeException(sprintf(
                'You cannot add "%s", it is already inherited.',
                $tableName
            ));
        }

        if ($this->_table->association($tableName)) {
            $this->checkAssociation($tableName, $conf);
            $conf['associationCreated'] = false;
            return $this->config('table', $conf);
        }

        $this->_table->belongsTo($tableName, [
            'foreignKey' => $this->_table->primaryKey(),
            'className' => $conf['className'],
            'joinType' => 'INNER',
        ]);
        $conf['associationCreated'] = true;

        return $this->config('table', $conf);
    }

    /**
     * Remove a table from the inheritance.
     *
     * Only direct inheritance table can be removed.
     * If the inheritance had created a new association with current table
     * then it will be removed.
     *
     * @throws \RuntimeException When trying to remove wrong table
     * @param string $tableName The table name to remove
     * @return $this
     */
    public function removeTable($tableName)
    {
        if (!$this->isTableInherited($tableName)) {
            throw new RuntimeException(sprintf(
                '"%s" is not inherited or is a nested inheritance. You can remove only direct inheritance',
                $tableName
            ));
        }
        $tableConf = $this->config('table');
        if ($tableConf['associationCreated']) {
            $this->_table
                ->associations()
                ->remove($tableName);
        }
        return $this->config('table', null);
    }

    /**
     * Check if the current table has an association with `$tableName`
     * and if it is valid using `$conf`.
     *
     * To be valid:
     *
     * * it must be "belongsTo" association
     * * it must have "INNER" join type
     * * the foreign key on current table must be equal to its primary key
     * * the class name used must be equal to `$conf['className']` (if any is set)
     *
     * @param string $tableName The table name to check
     * @param array $conf an optional array of conf to check
     * @return void
     */
    protected function checkAssociation($tableName, array $conf = [])
    {
        $association = $this->_table->association($tableName);
        if (!$association) {
            throw new RuntimeException(sprintf(
                '"%s" is not associated to %s',
                $this->_table->alias(),
                $tableName
            ));
        }

        if ($association->type() !== $association::MANY_TO_ONE) {
            throw new RuntimeException(sprintf(
                '"%s" must be associated with belongsTo',
                $tableName
            ));
        }

        if (strtolower($association->joinType()) != 'inner') {
            throw new RuntimeException(sprintf(
                '"%s" is joined with "%s" type. It must be "INNER"',
                $tableName,
                $association->joinType()
            ));
        }

        if ($association->foreignKey() != $this->_table->primaryKey()) {
            throw new RuntimeException(sprintf(
                'foreignKey "%s" must be identical to primary key "%s".',
                $association->foreignKey(),
                $this->_table->primaryKey()
            ));
        }

        if (isset($conf['className']) && $conf['className'] != $association->className()) {
            throw new RuntimeException(sprintf(
                'className "%s" mismatch the previous configuration "%s".',
                $conf['className'],
                $association->className()
            ));
        }
    }

    /**
     * Say if table is inherited.
     *
     * @param string $tableName The table name to check
     * @param bool $nested If it must check nested inheritance
     * @return bool
     */
    public function isTableInherited($tableName, $nested = false)
    {
        $found = array_search(
            $tableName,
            array_column($this->inheritedTables($nested), 'alias')
        );
        return $found !== false;
    }

    /**
     * Helper method to get Table instance from the registry.
     *
     * @param string $tableName The table name
     * @param string|null $className The class name used for table
     * @return \Cake\ORM\Table
     */
    protected function getTable($tableName, $className = null)
    {
        return TableRegistry::get(
            $tableName,
            TableRegistry::exists($tableName) ? [] : ['className' => $className]
        );
    }

    /**
     * Return the inherited tables configuration.
     *
     * By default return all direct inherited tables (no nested).
     * To get the the all nested levels of inherited tables
     * pass `$nested = true`.
     *
     * @param bool $nested If it must return the complete inherited tables
     * @return array
     */
    public function inheritedTables($nested = false)
    {
        $inheritedTables = $this->config('table') ?: [];
        if (!$nested || empty($inheritedTables)) {
            return [$inheritedTables];
        }

        $table = $this->getTable($inheritedTables['alias'], $inheritedTables['className']);
        if (!$table->hasBehavior('ClassTableInheritance')) {
            return [$inheritedTables];
        }

        return array_merge([$inheritedTables], $table->inheritedTables(true));
    }

    /**
     * Arrange contain mapping tables to the right association
     *
     * * add inherited tables
     * * arrange tables in \Cake\ORM\Query::contain() to the right inherited table
     * * override contain data with that calculated
     *
     * @param \Cake\ORM\Query $query The query object
     * @return \Cake\ORM\Query
     */
    public function arrangeContain(Query $query)
    {
        $inheritedTables = $this->inheritedTables(true);
        $contain = $query->contain();
        $contain += array_fill_keys(array_column($inheritedTables, 'alias'), []);
        $result = [];

        foreach ($contain as $tableName => $tableContain) {
            $containString = $this->buildContainString($tableName);
            if (!$containString) {
                $containString = $tableName;
            }
            $result[$containString] = $tableContain;
        }

        $query->contain($result, true);
        return $query;
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
     * @return bool|string
     */
    public function buildContainString($tableName)
    {
        if ($this->_table->association($tableName)) {
            return $tableName;
        }

        foreach ($this->inheritedTables(true) as $conf) {
            $table = $this->getTable($conf['alias'], $conf['className']);

            if ($table->association($tableName)) {
                return $conf['alias'] . '.' . $tableName;
            }

            if ($table->hasBehavior('ClassTableInheritance')) {
                $containString = $table->buildContainString($tableName);
                return ($containString === false) ? false : $conf['alias'] . '.' . $containString;
            }
        }

        return false;
    }

    /**
     * Flatten an Cake\ORM\Entity or array using inherited tables
     *
     * All associations defined by inherited tables are collapsed on current entity
     *
     * @param \Cake\ORM\Entity|array $row The entity or array to flatten
     * @return \Cake\ORM\Entity|array
     */
    public function flatten($row)
    {
        $tableName = $this->config('table.alias');
        $association = $this->_table
            ->association($tableName)
            ->property();
        if (empty($row[$association])) {
            continue;
        }

        $flattenMethod = is_object($row[$association]) ? 'flattenEntityProperty' : 'flattenArrayProperty';
        $this->{$flattenMethod}($row, $association);

        return $row;
    }

    /**
     * Given an `$entity` and a `$property` flatten
     * the `$entity->$property` as current `$entity` properties
     *
     * @param \Cake\ORM\Entity $entity The entity object
     * @param string $association The entity property that represents an association
     * @return void
     */
    protected function flattenEntityProperty(Entity $entity, $association)
    {
        $entityToFlat = $entity->$association;
        foreach ($entityToFlat->visibleProperties() as $prop) {
            $entity->set($prop, $entityToFlat->$prop);
            $entity->dirty($prop, false);
        }
        $entity->unsetProperty($association);
    }

    /**
     * Given an array and a property flatten the array[property] data
     *
     * @param array $row The array
     * @param string $association The property that represents an association
     * @return void
     */
    protected function flattenArrayProperty(array &$row, $association)
    {
        $row = array_merge($row, $row[$association]);
        unset($row[$association]);
    }
}

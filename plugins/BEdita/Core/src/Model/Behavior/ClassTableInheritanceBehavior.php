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
 * the main table will show fields of all inherited tables as own fields in a transparent way.
 *
 * You can use it for multiple inheritance i.e. to inherit more tables at the same level
 * and nested inheritance i.e. to inherit tables inherited from others
 *
 * Example of usage:
 *
 * 1. Multiple inheritance (inherit directly two or more tables).
 *
 * ```
 * // in MainTable table class
 * public function initialize(array $config)
 * {
 *     $this->addBehavior('BEdita/Core.ClassTableInheritance', [
 *         'tables' => [
 *             'InheritedOne' => [
 *                 'foreignKey' => 'inherited_one_id',
 *                 'className' => 'Class\Namespace\InheritedOneTable'
 *             ],
 *             'InheritedTwo' => [
 *                 'foreignKey' => 'inherited_two_id',
 *                 'className' => 'Class\Namespace\InheritedTwoTable'
 *             ]
 *         ]
 *     ]);
 * }
 * ```
 *
 * in this case `InheritedOne` and `InheritedTwo` will be inherited as first level of inheritance
 *
 * 2. Nested inheritance
 *
 * ```
 * // in MainTable table class
 * public function initialize(array $config)
 * {
 *     $this->addBehavior('BEdita/Core.ClassTableInheritance', [
 *         'tables' => [
 *             'InheritedOne' => [
 *                 'foreignKey' => 'inherited_one_id',
 *                 'className' => 'Class\Namespace\InheritedOneTable'
 *             ]
 *         ]
 *     ]);
 * }
 *
 * // in InheritedOne table class
 * public function initialize(array $config)
 * {
 *     $this->addBehavior('BEdita/Core.ClassTableInheritance', [
 *         'tables' => [
 *             'InheritedTwo' => [
 *                 'foreignKey' => 'inherited_two_id',
 *                 'className' => 'Class\Namespace\InheritedTwoTable'
 *             ]
 *         ]
 *     ]);
 * }
 * ```
 *
 * in this case `MainTable` inherits `InheritedOne` that inherits `InheritedTwo`.
 * So `MainTable` inherits `InheritedTwo` too.
 *
 *
 * You can mix 1. and 2. too
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
     * Initialize the Behavior configuring table associations
     *
     * Configuration options are:
     *
     * * 'tables' an array of inherited tables
     *
     * @param array $config configuration options
     * @return void
     */
    public function initialize(array $config)
    {
        $this->config('tables', [], false);
        foreach ($config['tables'] as $tableName => &$conf) {
            $this->addTable($tableName, $conf);
        }
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
     * * `foreignKey` the name of the foreign key found in the current table
     * * `className` the associated table class name
     *
     * Adding table create a new "belongsTo" association with main table (if not already exists)
     * and write the behavior configuration.
     * When association is created the key `associationCreated` in conf is set to true
     *
     * @throws \RuntimeException When you try to add table already inherited
     *                           or when self::checkAssociation() fails
     * @param string $tableName The table name
     * @param array $conf The configuration options
     * @return $this
     */
    public function addTable($tableName, array $conf = [])
    {
        $conf += ['foreignKey' => null, 'className' => null];
        if ($this->isTableInherited($tableName, true)) {
            throw new RuntimeException(sprintf(
                'You cannot add "%s", it is already inherited.',
                $tableName
            ));
        }

        if ($this->_table->association($tableName)) {
            $this->checkAssociation($tableName, $conf);
            $conf['associationCreated'] = false;
            return $this->config('tables.' . $tableName, $conf);
        }

        $this->_table->belongsTo($tableName, [
            'foreignKey' => $conf['foreignKey'],
            'className' => $conf['className'],
            'joinType' => 'INNER',
        ]);
        $conf['associationCreated'] = true;

        return $this->config('tables.' . $tableName, $conf);
    }

    /**
     * Remove a table from the inheritance.
     *
     * Only first level inheritance table can be removed.
     * If the inheritance had created a new association with main table
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
        $tableConf = $this->config('tables.' . $tableName);
        if ($tableName['associationCreated']) {
            $this->_table
                ->associations()
                ->remove($tableName);
        }
        return $this->config('tables.' . $tableName, null);
    }

    /**
     * Check if the main table has an association with `$tableName`
     * and if it is valid using `$conf`.
     *
     * To be valid:
     *
     * * it must be "belongsTo" association
     * * it must have "INNER" join type
     * * the foreign key must be equal to `$conf['foreignKey']` (if any is set)
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
                '"%s" must have a belongsTo association',
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

        if (isset($conf['foreignKey']) && $conf['foreignKey'] != $association->foreignKey()) {
            throw new RuntimeException(sprintf(
                'foreignKey "%s" mismatch the previous configuration "%s".',
                $conf['foreignKey'],
                $association->foreignKey()
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
        return array_key_exists($tableName, $this->inheritedTables($nested));
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
     *
     * @param bool $nested If it must return the complete inherited tables
     * @return array
     */
    public function inheritedTables($nested = false)
    {
        $inheritedTables = $this->config('tables');
        if (!$nested) {
            return $inheritedTables;
        }

        foreach ($inheritedTables as $tableName => $conf) {
            $table = $this->getTable($tableName, $conf['className']);
            if (!$table->hasBehavior('ClassTableInheritance')) {
                continue;
            }

            $inheritedTables = array_merge($inheritedTables, $table->inheritedTables(true));
        }

        return $inheritedTables;
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
        $contain += array_fill_keys(array_keys($inheritedTables), []);
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
     * If `$tableName` is a direct association to main table return it
     * else search if $tableName is associated to a inherited table
     *
     * Return false if $tableName is not found as association to any table
     *
     * @param string $tableName The starting table name
     * @return bool|string
     */
    public function buildContainString($tableName)
    {
        if ($this->_table->association($tableName)) {
            return $tableName;
        }

        foreach ($this->inheritedTables(true) as $inheritedTableName => $conf) {
            $table = $this->getTable($inheritedTableName, $conf['className']);

            if ($table->association($tableName)) {
                return $inheritedTableName . '.' . $tableName;
            }

            if ($table->hasBehavior('ClassTableInheritance')) {
                $containString = $table->buildContainString($tableName);
                return ($containString === false) ? false : $inheritedTableName . '.' . $containString;
            }
        }

        return false;
    }

    /**
     * Flatten an Cake\ORM\Entity or array using inherited tables
     *
     * All associations defined by inherited tables are collapsed on main entity
     *
     * @param \Cake\ORM\Entity|array $row The entity or array to flatten
     * @return \Cake\ORM\Entity|array
     */
    public function flatten($row)
    {
        foreach (array_keys($this->inheritedTables()) as $tableName) {
            $association = $this->_table
                ->association($tableName)
                ->property();
            if (empty($row[$association])) {
                continue;
            }

            $flattenMethod = is_object($row[$association]) ? 'flattenEntityProperty' : 'flattenArrayProperty';
            $this->{$flattenMethod}($row, $association);
        }

        return $row;
    }

    /**
     * Given an `$entity` and a `$property` flatten
     * the `$entity->$property` as main `$entity` properties
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

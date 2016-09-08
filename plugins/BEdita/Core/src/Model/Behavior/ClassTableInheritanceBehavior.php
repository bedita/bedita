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

use BEdita\Core\ORM\Inheritance\QueryPatcher;
use BEdita\Core\ORM\Inheritance\TableInheritanceManager;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * ClassTableInheritance behavior
 *
 * It is responsible for handling Class Table Inheritance.
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
 *         'table' => [
 *              'tableName' => 'InheritedOne',
 *              'className' => 'Class\Namespace\InheritedOneTable'
 *         ]
 *     ]);
 * }
 *
 * // in InheritedOne table class
 * public function initialize(array $config)
 * {
 *     $this->addBehavior('BEdita/Core.ClassTableInheritance', [
 *         'table' => [
 *              'tableName' => 'InheritedTwo',
 *              'className' => 'Class\Namespace\InheritedTwoTable'
 *         ]
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
            'patchContain' => 'patchContain'
        ]
    ];

    /**
     * Initialize the Behavior configuring table association.
     *
     * Configuration options are:
     *
     * - `table` the table to inherit.
     *   Must contain `tableName` key. Other keys can be `className`
     *
     * @see BEdita\Core\ORM\TableInheritanceManager::addTable()
     * @param array $config configuration options
     * @return void
     */
    public function initialize(array $config)
    {
        if (empty($config['table']['tableName'])) {
            throw new \InvalidArgumentException('Behavior missing configuration. At least [table => [tableName => \'MyTableName\']] expected');
        }
        TableInheritanceManager::addTable($this->_table, $config['table']);
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
    public function beforeFind(Event $event, Query $query, \ArrayObject $options, $primary)
    {
        if (!$primary) {
            return;
        }
        $this->queryPatcher()
            ->patch($query)
            ->all();
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
    public function beforeSave(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        $inheritedTable = current($this->inheritedTables());
        if (empty($inheritedTable)) {
            return;
        }

        $property = $this->_table
            ->association($inheritedTable->alias())
            ->property();

        $entity->dirty($property, true);
    }

    /**
     * Wrap BEdita\Core\ORM\TableInheritanceManager::isTableInherited()
     * to expose it to Table class
     *
     * @see BEdita\Core\ORM\TableInheritanceManager::isTableInherited()
     * @param string $tableName The table name to check
     * @param bool $nested If it must check nested inheritance
     * @return bool
     */
    public function isTableInherited($tableName, $nested = false)
    {
        return TableInheritanceManager::isTableInherited($this->_table, $tableName, $nested);
    }

    /**
     * Wrap BEdita\Core\ORM\TableInheritanceManager::inheritedTables()
     * to exposes it to Table class
     *
     * @see BEdita\Core\ORM\TableInheritanceManager::inheritedTables()
     * @param bool $nested If it must return the complete inherited tables (default false)
     * @return array
     */
    public function inheritedTables($nested = false)
    {
        return TableInheritanceManager::inheritedTables($this->_table, $nested);
    }

    /**
     * Arrange contain mapping tables to the right association
     *
     * @see \BEdita\Core\ORM\Inheritance\QueryPatcher::contain()
     * @param \Cake\ORM\Query $query The query object
     * @return \Cake\ORM\Query
     */
    public function patchContain(Query $query)
    {
        $this->queryPatcher()
            ->patch($query)
            ->contain();

        return $query;
    }

    /**
     * Return a new instance of QueryPatcher
     *
     * @return \BEdita\Core\ORM\Inheritance\QueryPatcher
     */
    public function queryPatcher()
    {
        return new QueryPatcher($this->_table);
    }
}

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

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query as CakeQuery;
use Cake\ORM\Table as CakeTable;

/**
 * Extends `\Cake\ORM\Query` to use custom `ResultSet` to better handle query results in CTI.
 * Also add useful methods to patch the query clauses in CTI context.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\ORM\Inheritance\Table _repository
 */
class Query extends CakeQuery
{

    /**
     * {@inheritDoc}
     */
    public function addDefaultTypes(CakeTable $table)
    {
        parent::addDefaultTypes($table);

        if ($table instanceof Table) {
            // Add types of fields from inherited tables, so that they are cast to the correct type.
            $alias = $table->getAlias();
            foreach ($table->inheritedTables() as $table) {
                $map = $table->getSchema()->typeMap();
                $fields = [];
                foreach ($map as $f => $type) {
                    $fields[$f] = $fields[$alias . '.' . $f] = $fields[$alias . '__' . $f] = $type;
                }
                $this->getTypeMap()->addDefaults($fields);
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function _addDefaultFields()
    {
        $select = $this->clause('select');
        $this->_hasFields = true;

        if (!count($select) || $this->_autoFields === true) {
            // If no fields have explicitly been selected, and autoFields is enabled, select all fields from inheritance chain.
            $this->_hasFields = false;
            $columns = $this->_repository->getSchema()->columns();
            foreach ($this->_repository->inheritedTables() as $inheritedTable) {
                $columns = array_merge($columns, $inheritedTable->getSchema()->columns());
            }

            $this->select($columns);
            $select = $this->clause('select');
        }

        $aliased = $this->aliasFields($select, $this->_repository->getAlias());
        $this->select($aliased, true);
    }

    /**
     * {@inheritDoc}
     */
    protected function _transformQuery()
    {
        if ($this->_dirty && $this->_type === 'select' && empty($this->_parts['from']) && $this->_repository->inheritedTable() !== null) {
            // If no "from" was explicitly set, use CTI sub-query.
            $this->from([$this->_repository->getAlias() => $this->getInheritanceSubQuery()], true);
        }

        parent::_transformQuery();
    }

    /**
     * Get sub-query that returns inheritance chain as a single expression.
     *
     * @return \Cake\ORM\Query
     */
    protected function getInheritanceSubQuery()
    {
        $subQuery = new parent($this->getConnection(), $this->_repository);

        // Current table.
        $subQuery
            ->select(
                // Select fields from current table.
                $this->subQueryAliasFields(
                    $this->_repository->getSchema()->columns(),
                    $this->_repository
                )
            )
            ->from(
                // Set "from" of the sub-query.
                [$this->_repository->getAlias() => $this->_repository->getTable()]
            );

        // Inherited tables.
        foreach ($this->_repository->inheritedTables() as $table) {
            $subQuery
                ->select(
                    // Add fields from inherited table to "select" clause.
                    $this->subQueryAliasFields(
                        array_diff($table->getSchema()->columns(), (array)$table->getPrimaryKey()), // Be careful to avoid duplicate columns.
                        $table
                    )
                )
                ->innerJoin(
                    // Add joins with inherited tables.
                    [$table->getAlias() => $table->getTable()],
                    function (QueryExpression $exp) use ($table) {
                        return $exp->equalFields(
                            $table->aliasField((string)$table->getPrimaryKey()),
                            $this->_repository->aliasField((string)$this->_repository->getPrimaryKey())
                        );
                    }
                );
        }

        return $subQuery;
    }

    /**
     * Alias fields for use in `from` sub-query.
     *
     * Fields **MUST NOT** have CakePHP's default aliases, but should rather have their "cleaned" name version.
     *
     * For instance, a field named `foo` in the table `bars` would be aliased by Cake as `Bars__foo`, but we
     * want it to be _exactly_ `foo` so that the main query can use the correct name.
     *
     * @param string[] $fields Fields to be aliased.
     * @param \Cake\ORM\Table $table Table instance.
     * @return array
     */
    protected function subQueryAliasFields(array $fields, CakeTable $table)
    {
        $result = [];
        foreach ($fields as $field) {
            $result[$field] = $table->aliasField($field);
        }

        return $result;
    }
}

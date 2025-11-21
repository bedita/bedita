<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\ORM\Inheritance\Query;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query\SelectQuery as CakeSelectQuery;
use Cake\ORM\Table;

/**
 * Select Query class for tables that use class table inheritance (CTI).
 *
 * @since 5.24.0
 */
class SelectQuery extends CakeSelectQuery
{
    use InheritanceQueryTrait;

    /**
     * @inheritDoc
     */
    protected function _addDefaultFields(): void
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
     * @inheritDoc
     */
    protected function _transformQuery(): void
    {
        if ($this->_dirty && empty($this->_parts['from']) && $this->_repository->inheritedTable() !== null) {
            // If no "from" was explicitly set, use CTI sub-query.
            $this->from([$this->_repository->getAlias() => $this->getInheritanceSubQuery()], true);
        }

        parent::_transformQuery();
    }

    /**
     * Get sub-query that returns inheritance chain as a single expression.
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function getInheritanceSubQuery(): CakeSelectQuery
    {
        // @codingStandardsIgnoreStart
        $subQuery = new parent($this->_repository);
        // @codingStandardsIgnoreEnd

        // Current table.
        $subQuery
            ->select(
                // Select fields from current table.
                $this->subQueryAliasFields(
                    $this->_repository->getSchema()->columns(),
                    $this->_repository,
                ),
            )
            ->from(
                // Set "from" of the sub-query.
                [$this->_repository->getTable() => $this->_repository->getTable()],
            );

        // Inherited tables.
        foreach ($this->_repository->inheritedTables() as $table) {
            $subQuery
                ->select(
                    // Add fields from inherited table to "select" clause.
                    $this->subQueryAliasFields(
                        array_diff($table->getSchema()->columns(), (array)$table->getPrimaryKey()), // Be careful to avoid duplicate columns.
                        $table,
                    ),
                )
                ->innerJoin(
                    // Add joins with inherited tables.
                    [$table->getTable() => $table->getTable()],
                    function (QueryExpression $exp) use ($table) {
                        return $exp->equalFields(
                            sprintf('%s.%s', $table->getTable(), (string)$table->getPrimaryKey()),
                            sprintf('%s.%s', $this->_repository->getTable(), (string)$this->_repository->getPrimaryKey()),
                        );
                    },
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
     * @param array<string> $fields Fields to be aliased.
     * @param \Cake\ORM\Table $table Table instance.
     * @return array
     */
    protected function subQueryAliasFields(array $fields, Table $table): array
    {
        $result = [];
        foreach ($fields as $field) {
            $result[$field] = sprintf('%s.%s', $table->getTable(), $field);
        }

        return $result;
    }
}

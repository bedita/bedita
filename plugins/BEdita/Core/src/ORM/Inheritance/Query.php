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

use BEdita\Core\ORM\Inheritance\ResultSet;
use Cake\Database\ExpressionInterface;
use Cake\Database\Expression\FieldInterface;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\RepositoryInterface;
use Cake\ORM\Query as CakeQuery;
use Cake\ORM\Table as CakeTable;

/**
 * Extends `\Cake\ORM\Query` to use custom `ResultSet` to better handle query results in CTI.
 * Also add useful methods to patch the query clauses in CTI context.
 *
 * @since 4.0.0
 */
class Query extends CakeQuery
{
    /**
     * It keeps trace of inheritance to avoid repeated operations
     *
     * @var array
     */
    protected $inheritanceMap = [];

    /**
     * The alias checker used to extract fields name aliased with `self::_repository` alias.
     * It needs to boost performance using `substr()` in case of repeated replaces.
     *
     * Contains the two keys:
     * - `string` the string to check i.e. `TableAlias.`
     * - `length` the length of the string to check
     *
     * @var array
     * @see self::aliasChecker()
     * @see self::extractField()
     */
    protected $aliasChecker = [];

    /**
     * It replaces `\Cake\Datasource\QueryTrait::repository()` calling it
     * and setup the `self::aliasChecker` if needed
     *
     * {@inheritDoc}
     */
    protected function aliasChecker($table)
    {
        $checker = $table->alias() . '.';

        return $this->aliasChecker = ['string' => $checker, 'length' => strlen($checker)];
    }

    /**
     * {@inheritDoc}
     */
    protected function _execute()
    {
        $this->triggerBeforeFind();
        if ($this->_results) {
            $decorator = $this->_decoratorClass();

            return new $decorator($this->_results);
        }

        $statement = $this->eagerLoader()->loadExternal($this, $this->execute());

        return new ResultSet($this, $statement);
    }

    /**
     * Return the complete table inheritance of `$this->_repository`.
     * Once obtained it returns its value without recalculate it.
     *
     * @return array
     */
    protected function inheritedTables()
    {
        if (array_key_exists('tables', $this->inheritanceMap)) {
            return $this->inheritanceMap['tables'];
        }

        $this->inheritanceMap['tables'] = $this->_repository->inheritedTables(true);

        return $this->inheritanceMap['tables'];
    }

    /**
     * Execute all the fixes to work right in CTI context
     * and return the query patched
     *
     * @return $this
     */
    public function fixAll()
    {
        $this->fixContain();

        return $this
            ->traverseExpressions([$this, 'fixExpression'])
            ->traverse(
                [$this, 'fixClause'],
                ['select', 'group', 'distinct']
            );
    }

    /**
     * Arrange contain mapping tables to the right association
     *
     * * add inherited tables
     * * arrange tables in `self::contain()` to the right inherited table
     * * override contain data with that calculated
     *
     * @return $this
     */
    public function fixContain()
    {
        $inheritedTables = array_map(function (CakeTable $table) {
            return $table->alias();
        }, $this->inheritedTables());

        $contain = $this->contain();
        $contain += array_fill_keys($inheritedTables, []);
        $result = [];

        foreach ($contain as $tableName => $tableContain) {
            $containString = $this->buildContainString($tableName);
            if ($containString === false) {
                $containString = $tableName;
            }
            $result[$containString] = $tableContain;
        }

        $this->contain($result, true);

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
        if ($this->_repository->association($tableName)) {
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
    /**
     * Fix sql clause mapping inherited fields.
     *
     * Pay attention that this method just fixes clauses in the format of string or array of string.
     * Moreover at the end `\Cake\ORM\Query::$clause()` is called with overwrite `true` as second param
     * so assure that the clause method of `\Cake\ORM\Query` has the right signature.
     *
     * If you have to fix query expression you should use `self::fixExpression()` instead.
     *
     * @param array|\Cake\Database\ExpressionInterface|bool|string $clauseData The clause data
     * @param string $clause The sql clause
     * @return $this
     */
    public function fixClause($clauseData, $clause)
    {
        $clauseData = $clauseData ?: $this->clause($clause);
        if (empty($clauseData) || is_bool($clauseData) || $clauseData instanceof ExpressionInterface) {
            return $this;
        }

        if (!is_array($clauseData)) {
            $clauseData = [$clauseData];
        }

        foreach ($clauseData as $key => $data) {
            if (!is_string($data)) {
                continue;
            }

            $clauseData[$key] = $this->fixAliasField($data);
        }

        $this->{$clause}($clauseData, true);

        return $this;
    }

    /**
     * Fix query expressions mapping inherited fields
     *
     * @param \Cake\Database\ExpressionInterface $expression The expression to manipulate
     * @return $this
     */
    public function fixExpression(ExpressionInterface $expression)
    {
        if ($expression instanceof IdentifierExpression) {
            $identifier = $expression->getIdentifier();
            $expression->setIdentifier($this->fixAliasField($identifier));

            return $this;
        }

        if ($expression instanceof FieldInterface) {
            $field = $expression->getField();
            if (is_string($field)) {
                $expression->setField($this->fixAliasField($field));
            }

            return $this;
        }

        if ($expression instanceof QueryExpression) {
            $expression->iterateParts(function ($value, &$key) {
                if (!is_numeric($key)) {
                    $key = $this->fixAliasField($key);
                }

                if (is_string($value)) {
                    return $this->fixAliasField($value);
                }

                return $value;
            });
        }

        return $this;
    }

    /**
     * Given a `$field` return itself aliased as `TableAlias.column_name`
     * eventually using the right inherited table
     *
     * If `$field` doesn't correspond to any inherited table columns
     * then return it without any change.
     *
     * @param string $field The field string
     * @return string
     */
    public function fixAliasField($field)
    {
        $field = $this->extractField($field);

        if (strpos($field, '.') !== false) {
            return $field;
        }

        if ($this->_repository->hasField($field)) {
            return $this->_repository->alias() . '.' . $field;
        }

        foreach ($this->inheritedTables() as $inherited) {
            if (!$inherited->hasField($field)) {
                continue;
            }

            return $inherited->alias() . '.' . $field;
        }

        return $field;
    }

    /**
     * Given a `$field` returns it without the `self::$table` alias
     *
     * @param string $field The field string
     * @return string
     */
    protected function extractField($field)
    {
        if (empty($this->aliasChecker)) {
            $this->aliasChecker($this->_repository);
        }

        $aliasedWith = substr($field, 0, $this->aliasChecker['length']);
        if ($aliasedWith == $this->aliasChecker['string']) {
            $field = substr($field, $this->aliasChecker['length']);
        }

        return $field;
    }
}

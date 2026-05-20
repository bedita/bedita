<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2026 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

declare(strict_types=1);

namespace BEdita\Core\Model\Behavior;

use ArrayAccess;
use Cake\Database\Expression\CommonTableExpression;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Expression\TupleComparison;
use Cake\Database\Expression\UnaryExpression;
use Cake\Database\ExpressionInterface;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\Association;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Behavior;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Behavior to help represent trees using adjacency lists.
 *
 * This behavior uses recursive common table expressions to find descendants, ancestors, level and ancestry path without
 * incurring in an N+1 problem. Common table expressions are available in SQLite, Postgres, and MySQL as of version 8.0.
 */
class AdjacencyListBehavior extends Behavior
{
    /**
     * Name of field containing descendant level relative to the ancestor in the built CTE.
     */
    public const CTE_FIELD_LEVEL = 'level';

    /**
     * Name of field containing the flag to detect cyclic references in the built CTE.
     */
    public const CTE_FIELD_CYCLIC = 'cyclic';

    /**
     * Prefix for ancestor fields in the built CTE.
     */
    public const CTE_PREFIX_ANCESTOR = 'ancestor_';

    /**
     * Prefix for descendant fields in the built CTE.
     */
    public const CTE_PREFIX_DESCENDANT = 'descendant_';

    /**
     * Logical name of CTE.
     *
     * @var string
     */
    protected string $cteName;

    /**
     * Association to link rows to their parent row.
     *
     * @var \Cake\ORM\Association\BelongsTo
     */
    protected BelongsTo $parentAssociation;

    /**
     * Built CTE.
     *
     * @var \Cake\Database\Expression\CommonTableExpression
     */
    protected CommonTableExpression $cte;

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'cteName' => null,
        'parentAssociation' => 'Parents',
        'ancestorsAssociation' => 'Ancestors',
        'descendantsAssociation' => 'Descendants',
    ];

    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $table = $this->table();

        // Set CTE logical name.
        $cteName = $this->getConfig('cteName', sprintf('%s_matrix', $table->getTable()));
        if (!is_string($cteName) || empty($cteName)) {
            throw new InvalidArgumentException('CTE name should be a non empty string, or be omitted to use default');
        }
        $this->cteName = $cteName;

        // Get parent association.
        $parentAssociation = $this->getConfigOrFail('parentAssociation');
        if (is_string($parentAssociation)) {
            $parentAssociation = $table->getAssociation($parentAssociation);
        }
        if (!$parentAssociation instanceof BelongsTo) {
            throw new UnexpectedValueException(sprintf(
                'Configuration `%s` should be a string or an instance of %s, got %s',
                'parentAssociation',
                BelongsTo::class,
                get_debug_type($parentAssociation),
            ));
        }
        $this->parentAssociation = $parentAssociation;
    }

    /**
     * Get schema of CTE.
     *
     * @return \Cake\Database\Schema\TableSchema
     */
    protected function getCteSchema(): TableSchema
    {
        $bindingKey = (array)$this->parentAssociation->getBindingKey();
        $originalSchema = $this->table()->getSchema();

        $schema = new TableSchema($this->cteName);
        $schema = array_reduce(
            $bindingKey,
            function (TableSchema $schema, string $field) use ($originalSchema): TableSchema {
                $attrs = $originalSchema->getColumn($field) ?? 'string';

                return $schema
                    ->addColumn(static::CTE_PREFIX_ANCESTOR . $field, $attrs)
                    ->addColumn(static::CTE_PREFIX_DESCENDANT . $field, $attrs);
            },
            $schema,
        );

        return $schema
            ->addColumn(static::CTE_FIELD_LEVEL, 'integer')
            ->addColumn(static::CTE_FIELD_CYCLIC, 'boolean');
    }

    /**
     * Get or build association for ancestors or descendants.
     *
     * @param bool $descendants `true` for descendants, `false` for ancestors.
     * @return \Cake\ORM\Association\BelongsToMany
     */
    protected function getInheritanceAssociation(bool $descendants): BelongsToMany
    {
        [$config, $foreignKeyPrefix, $targetForeignKeyPrefix] = ['ancestorsAssociation', static::CTE_PREFIX_DESCENDANT, static::CTE_PREFIX_ANCESTOR];
        if ($descendants) {
            [$config, $foreignKeyPrefix, $targetForeignKeyPrefix] = ['descendantsAssociation', static::CTE_PREFIX_ANCESTOR, static::CTE_PREFIX_DESCENDANT];
        }

        $name = $this->getConfigOrFail($config);
        if (!is_string($name) || empty($name)) {
            throw new InvalidArgumentException(sprintf('Configuration `%s` must be a non-empty string', $config));
        }

        $table = $this->table();
        if (!$table->hasAssociation($name)) {
            $targetTable = static::getCleanCopy($table)->setAlias($name);
            $joinTable = $this->cteName;
            $through = new Table(['table' => $joinTable, 'schema' => $this->getCteSchema(), 'alias' => $name . 'Through']);

            $bindingKey = (array)$this->parentAssociation->getBindingKey();
            $foreignKey = static::prefix($bindingKey, $foreignKeyPrefix);
            $targetForeignKey = static::prefix($bindingKey, $targetForeignKeyPrefix);

            return $table->belongsToMany($name, compact('targetTable', 'joinTable', 'through', 'foreignKey', 'targetForeignKey'));
        }

        $association = $table->getAssociation($name);
        if (!$association instanceof BelongsToMany) {
            throw new UnexpectedValueException(sprintf('Unexpected association type `%s`', get_class($association)));
        }

        return $association;
    }

    /**
     * Get clean copy of a table object, that can be used for belongsToMany associations where the target table
     * is the same as the source.
     *
     * @param \Cake\ORM\Table $table Table to clone.
     * @return \Cake\ORM\Table
     */
    protected static function getCleanCopy(Table $table): Table
    {
        $className = get_class($table);
        $copy = new $className([
            'table' => $table->getTable(),
            'connection' => $table->getConnection(),
            'schema' => $table->getSchema(),
            'entityClass' => $table->getEntityClass(),
        ]);

        $copy->associations()->removeAll();
        collection($table->associations())
            ->each(fn(Association $association) => $copy->associations()->add(
                $association->getName(),
                (clone $association)->setSource($copy),
            ));

        return $copy;
    }

    /**
     * Alias a field from CTE.
     *
     * @param string $field Field to be aliased.
     * @return string
     */
    protected function aliasCteField(string $field): string
    {
        if (str_contains($field, '.')) {
            return $field;
        }

        return sprintf('%s.%s', $this->cteName, $field);
    }

    /**
     * Prefix a list of fields with the passed prefix.
     *
     * @param string[] $fields Fields to be prefixed.
     * @param string $prefix Prefix.
     * @return string[]
     */
    protected static function prefix(array $fields, string $prefix): array
    {
        return array_map(fn(string $field): string => $prefix . $field, $fields);
    }

    /**
     * Remap a list of fields to a list of aliased identifiers.
     *
     * @param string[] $fields Fields.
     * @param callable $aliasFn Aliasing function.
     * @return \Cake\Database\Expression\IdentifierExpression[]
     */
    protected static function toIdentifiers(array $fields, callable $aliasFn): array
    {
        return array_map(fn(string $field): IdentifierExpression => new IdentifierExpression($aliasFn($field)), $fields);
    }

    /**
     * Build recursive Common Table Expression (CTE) for finding all pairs of ancestor and descendant nodes,
     * with level and a flag to stop infinite recursion in case of cyclic references.
     *
     * @return \Cake\Database\Expression\CommonTableExpression
     */
    protected function cteBuilder(): CommonTableExpression
    {
        $table = $this->table();

        // Prepare fields:
        $bindingKey = (array)$this->parentAssociation->getBindingKey();
        $ancestorFields = static::prefix($bindingKey, static::CTE_PREFIX_ANCESTOR);
        $descendantFields = static::prefix($bindingKey, static::CTE_PREFIX_DESCENDANT);
        $fields = array_merge($ancestorFields, $descendantFields, [static::CTE_FIELD_LEVEL, static::CTE_FIELD_CYCLIC]);

        // Prepare identifier expressions:
        $bindingKey = static::toIdentifiers($bindingKey, [$table, 'aliasField']);
        $foreignKey = static::toIdentifiers((array)($this->parentAssociation->getForeignKey() ?: null), [$table, 'aliasField']);
        $ancestorFields = static::toIdentifiers($ancestorFields, [$this, 'aliasCteField']);
        $descendantFields = static::toIdentifiers($descendantFields, [$this, 'aliasCteField']);

        // Recursion base:
        $base = $table->find()
            ->select(array_merge(
                $bindingKey, // ancestor
                $bindingKey, // descendant
                [
                    0, // level
                    0, // cyclic flag
                ],
            ));
        // Recursive part:
        $recursive = $table->find()
            ->select(array_merge(
                $ancestorFields, // ancestor
                $bindingKey, // descendant
                [
                    new UnaryExpression('+ 1', $this->aliasCteField(static::CTE_FIELD_LEVEL), UnaryExpression::POSTFIX), // level (increase by 1)
                    new TupleComparison($ancestorFields, $bindingKey), // cyclic flag (true if we re-encounter the same node)
                ],
            ))
            ->innerJoin(
                $this->cteName,
                (new QueryExpression())
                    ->add(new TupleComparison($descendantFields, $foreignKey))
                    ->not($this->aliasCteField(static::CTE_FIELD_CYCLIC)), // Avoid infinite recursion even with cyclic references.
            );

        return (new CommonTableExpression())
            ->recursive()
            ->name($this->cteName)
            ->field($fields)
            ->query($base->unionAll($recursive));
    }

    /**
     * Extract fields for tuple matching.
     *
     * @param mixed $from Entity(-ies) from which extract fields.
     * @param string[] $fields List of fields to extract.
     * @return \Cake\Database\ExpressionInterface|array
     */
    protected static function extractFields(mixed $from, array $fields): ExpressionInterface|array
    {
        if ($from instanceof ExpressionInterface) {
            return $from;
        }
        if (is_array($from) && array_is_list($from)) {
            return array_map(
                fn(mixed $from): ExpressionInterface|array => static::extractFields($from, $fields),
                $from,
            );
        }

        if (is_scalar($from) && count($fields) === 1) {
            return [$from];
        }

        if (!is_array($from) && !$from instanceof ArrayAccess) {
            throw new InvalidArgumentException('Cannot extract fields.');
        }

        return array_map(
            fn(string $field): mixed => $from[$field] ?? null,
            $fields,
        );
    }

    /**
     * Append CTE to query `WITH` clause.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findInheritanceMatrix(SelectQuery $query): SelectQuery
    {
        $this->cte ??= $this->cteBuilder();

        // Ensure CTE has been added to query:
        if (!in_array($this->cte, (array)$query->clause('with'), true)) {
            $query = $query->with($this->cte);
        }

        return $query;
    }

    /**
     * Attach join to Query object.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object.
     * @param \Cake\ORM\Association\BelongsToMany $association Association to join with.
     * @param bool $includeSelf Whether to include the self association (where `level` is 0).
     * @param \Cake\Database\ExpressionInterface $conditions Additional conditions.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function makeJoin(SelectQuery $query, BelongsToMany $association, bool $includeSelf, ExpressionInterface $conditions): SelectQuery
    {
        return $query
            ->find('inheritanceMatrix')
            ->innerJoinWith(
                $association->getName(),
                fn(SelectQuery $query): SelectQuery => $query->where(
                    function (QueryExpression $exp) use ($association, $conditions, $includeSelf): QueryExpression {
                        if (!$includeSelf) {
                            $exp = $exp->gt($association->junction()->aliasField(static::CTE_FIELD_LEVEL), 0);
                        }

                        return $exp->add($conditions);
                    },
                ),
            );
    }

    /**
     * Find all ancestors for a node.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object.
     * @param array{for: mixed, includeSelf?: bool} $options Options.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findAncestors(SelectQuery $query, array $options): SelectQuery
    {
        $for = $options['for'] ?? null;
        $includeSelf = $options['includeSelf'] ?? false;
        if (empty($for)) {
            throw new InvalidArgumentException(sprintf('Missing required `%s` option', 'for'));
        }

        $association = $this->getInheritanceAssociation(true);
        $bindingKey = (array)$association->getBindingKey();

        return $this->makeJoin(
            $query,
            $association,
            $includeSelf,
            new TupleComparison(array_map([$association->getTarget(), 'aliasField'], $bindingKey), static::extractFields($for, $bindingKey)),
        );
    }

    /**
     * Find all descendants for a node.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object.
     * @param array{for: mixed, includeSelf?: bool} $options Options.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findDescendants(SelectQuery $query, array $options): SelectQuery
    {
        $for = $options['for'] ?? null;
        $includeSelf = $options['includeSelf'] ?? false;
        if (empty($for)) {
            throw new InvalidArgumentException(sprintf('Missing required `%s` option', 'for'));
        }

        $association = $this->getInheritanceAssociation(false);
        $bindingKey = (array)$association->getBindingKey();

        return $this->makeJoin(
            $query,
            $association,
            $includeSelf,
            new TupleComparison(array_map([$association->getTarget(), 'aliasField'], $bindingKey), static::extractFields($for, $bindingKey)),
        );
    }
}

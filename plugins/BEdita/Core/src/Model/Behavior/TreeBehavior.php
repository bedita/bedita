<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Behavior;

use Cake\Database\Expression\Comparison;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Query;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior\TreeBehavior as CakeTreeBehavior;
use Cake\ORM\Table;

/**
 * This behavior adds absolute positioning of nodes on top of CakePHP {@see \Cake\ORM\Behavior\TreeBehavior}.
 *
 * {@inheritDoc}
 */
class TreeBehavior extends CakeTreeBehavior
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct(Table $table, array $config = [])
    {
        $this->_defaultConfig['implementedMethods'] += [
            'getCurrentPosition' => 'getCurrentPosition',
            'moveAt' => 'moveAt',
            'checkIntegrity' => 'checkIntegrity',
        ];

        parent::__construct($table, $config);
    }

    /**
     * @inheritDoc
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity)
    {
        // ensure to use actual left and right fields
        unset($entity[$this->getConfig('left')], $entity[$this->getConfig('right')]);
        $this->_ensureFields($entity);

        parent::beforeDelete($event, $entity);
    }

    /**
     * Get current position of a node within its parent.
     *
     * @param \Cake\Datasource\EntityInterface $node Node to get position for.
     * @return int
     */
    public function getCurrentPosition(EntityInterface $node)
    {
        return $this->_scope($this->table()->find())
            ->where(function (QueryExpression $exp) use ($node) {
                $parentField = $this->getConfig('parent');
                $leftField = $this->getConfig('left');

                if (!$node->has($parentField)) {
                    $exp = $exp->isNull($this->table()->aliasField($parentField));
                } else {
                    $exp = $exp->eq($this->table()->aliasField($parentField), $node->get($parentField));
                }

                return $exp
                    ->lte($this->table()->aliasField($leftField), $node->get($leftField));
            })
            ->count();
    }

    /**
     * Move a node at a specific position without changing the parent.
     *
     * @param \Cake\Datasource\EntityInterface $node Node to be moved.
     * @param int|string $position New position. Can be either an integer, or a string (`'first'` or `'last'`).
     *      Negative integers are interpreted as number of positions from the end of the list. 0 (zero) is not allowed.
     * @return \Cake\Datasource\EntityInterface|false
     */
    public function moveAt(EntityInterface $node, $position)
    {
        return $this->table()->getConnection()->transactional(function () use ($node, $position) {
            $position = static::validatePosition($position);
            if ($position === false) {
                return false;
            }

            // ensure to use actual left and right fields
            unset($node[$this->getConfig('left')]);
            unset($node[$this->getConfig('right')]);
            $this->_ensureFields($node);

            $currentPosition = $this->getCurrentPosition($node);
            if ($position === $currentPosition) {
                // Do not perform extra queries. Position must still be normalized, so we'll need to re-check later.
                return $node;
            }

            $childrenCount = $this->_scope($this->table()->find())
                ->where(function (QueryExpression $exp) use ($node) {
                    $parentField = $this->getConfig('parent');

                    if (!$node->has($parentField)) {
                        return $exp->isNull($this->table()->aliasField($parentField));
                    }

                    return $exp->eq($this->table()->aliasField($parentField), $node->get($parentField));
                })
                ->count();

            // Normalize position. Transform negative indexes, and apply bounds.
            if ($position < 0) {
                $position = $childrenCount + $position + 1;
            }
            $position = max(1, min($position, $childrenCount));

            if ($position === $currentPosition) {
                // Already OK.
                return $node;
            }

            if ($position > $currentPosition) {
                return $this->moveDown($node, $position - $currentPosition);
            }

            return $this->moveUp($node, $currentPosition - $position);
        });
    }

    /**
     * Validate a position.
     *
     * @param int|string $position Position to be validated.
     * @return int|false
     */
    protected static function validatePosition($position)
    {
        if ($position === 'first') {
            return 1;
        }
        if ($position === 'last') {
            return -1;
        }

        $position = filter_var($position, FILTER_VALIDATE_INT);
        if ($position === false || $position === 0) {
            return false;
        }

        return $position;
    }

    /**
     * Calls $this->_recoverTree() without transactional(...)
     * Warning: you should set up a transactional flow manually if you use this method!
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function nonAtomicRecover(): void
    {
        $this->_recoverTree();
    }

    /**
     * Run queries to check tree integrity.
     *
     * @return string[]
     */
    public function checkIntegrity(): array
    {
        $table = $this->table();
        $pk = $table->aliasField($table->getPrimaryKey());
        $left = $table->aliasField($this->getConfigOrFail('left'));
        $right = $table->aliasField($this->getConfigOrFail('right'));
        $parent = $table->aliasField($this->getConfigOrFail('parent'));
        $childAlias = sprintf('Child%s', $table->getAlias());
        $siblingAlias = sprintf('Sibling%s', $table->getAlias());

        $exists = function (Query $query): bool {
            return $query->select(['existing' => 1])->limit(1)->execute()->count() > 0;
        };

        $errors = [];

        // Check that for every record `lft < rght`.
        $query = $table->find()
            ->where(function (QueryExpression $exp) use ($left, $right): QueryExpression {
                return $exp->gte($left, new IdentifierExpression($right));
            });
        if ($exists($query)) {
            $errors[] = sprintf('Found record where %s >= %s', $this->getConfigOrFail('left'), $this->getConfigOrFail('right'));
        }

        // Check that for every parent, `parent.lft + 1 = MIN(children.lft)`
        $query = $table->find()
            ->innerJoin(
                [$childAlias => $table->getTable()],
                function (QueryExpression $exp) use ($pk, $childAlias): QueryExpression {
                    return $exp
                        ->equalFields($pk, sprintf('%s.%s', $childAlias, $this->getConfigOrFail('parent')));
                }
            )
            ->group([$pk, $left])
            ->having(function (QueryExpression $exp, Query $query) use ($childAlias, $left): QueryExpression {
                return $exp->notEq(
                    new Comparison($left, 1, null, '+'),
                    $query->func()->min(sprintf('%s.%s', $childAlias, $this->getConfigOrFail('left')))
                );
            });
        if ($exists($query)) {
            $errors[] = sprintf('Found record where parent.%s + 1 != MIN(children.%1$s)', $this->getConfigOrFail('left'));
        }

        // Check that for every parent, `parent.rght - 1 = MAX(children.rght)`
        $query = $table->find()
            ->innerJoin(
                [$childAlias => $table->getTable()],
                function (QueryExpression $exp) use ($pk, $childAlias): QueryExpression {
                    return $exp
                        ->equalFields($pk, sprintf('%s.%s', $childAlias, $this->getConfigOrFail('parent')));
                }
            )
            ->group([$pk, $right])
            ->having(function (QueryExpression $exp, Query $query) use ($childAlias, $right): QueryExpression {
                return $exp->notEq(
                    new Comparison($right, 1, null, '-'),
                    $query->func()->max(sprintf('%s.%s', $childAlias, $this->getConfigOrFail('right')))
                );
            });
        if ($exists($query)) {
            $errors[] = sprintf('Found record where parent.%s - 1 != MAX(children.%1$s)', $this->getConfigOrFail('right'));
        }

        // Check that for every node, `node.lft - 1 = MAX(sibling.rght)` where `sibling.lft <= node.lft`.
        $query = $table->find()
            ->innerJoin(
                [$siblingAlias => $table->getTable()],
                function (QueryExpression $exp) use ($table, $left, $parent, $pk, $siblingAlias): QueryExpression {
                    return $exp
                        ->add($exp->or(function (QueryExpression $exp) use ($parent, $siblingAlias): QueryExpression {
                            $siblingParent = sprintf('%s.%s', $siblingAlias, $this->getConfigOrFail('parent'));

                            return $exp
                                ->equalFields($parent, $siblingParent)
                                ->add($exp->and(function (QueryExpression $exp) use ($parent, $siblingParent): QueryExpression {
                                    return $exp->isNull($parent)->isNull($siblingParent);
                                }));
                        }))
                        ->gte($left, new IdentifierExpression(sprintf('%s.%s', $siblingAlias, $this->getConfigOrFail('left'))))
                        ->notEq($pk, new IdentifierExpression(sprintf('%s.%s', $siblingAlias, $table->getPrimaryKey())));
                }
            )
            ->group([$pk, $left])
            ->having(function (QueryExpression $exp, Query $query) use ($siblingAlias, $left): QueryExpression {
                return $exp->notEq(
                    new Comparison($left, 1, null, '-'),
                    $query->func()->max(sprintf('%s.%s', $siblingAlias, $this->getConfigOrFail('right')))
                );
            });
        if ($exists($query)) {
            $errors[] = sprintf('Found record where %s - 1 != MAX(previousSiblings.%s)', $this->getConfigOrFail('left'), $this->getConfigOrFail('right'));
        }

        return $errors;
    }
}

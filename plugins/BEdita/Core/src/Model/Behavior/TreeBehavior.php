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

use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
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
        ];

        parent::__construct($table, $config);
    }

    /**
     * Get current position of a node within its parent.
     *
     * @param \Cake\Datasource\EntityInterface $node Node to get position for.
     * @return int
     */
    public function getCurrentPosition(EntityInterface $node)
    {
        return $this->_scope($this->getTable()->find())
            ->where(function (QueryExpression $exp) use ($node) {
                $parentField = $this->getConfig('parent');
                $leftField = $this->getConfig('left');

                if (!$node->has($parentField)) {
                    $exp = $exp->isNull($this->getTable()->aliasField($parentField));
                } else {
                    $exp = $exp->eq($this->getTable()->aliasField($parentField), $node->get($parentField));
                }

                return $exp
                    ->lte($this->getTable()->aliasField($leftField), $node->get($leftField));
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
        return $this->getTable()->getConnection()->transactional(function () use ($node, $position) {
            $position = static::validatePosition($position);
            if ($position === false) {
                return false;
            }

            $currentPosition = $this->getCurrentPosition($node);
            if ($position === $currentPosition) {
                // Do not perform extra queries. Position must still be normalized, so we'll need to re-check later.
                return $node;
            }

            $childrenCount = $this->_scope($this->getTable()->find())
                ->where(function (QueryExpression $exp) use ($node) {
                    $parentField = $this->getConfig('parent');

                    if (!$node->has($parentField)) {
                        return $exp->isNull($this->getTable()->aliasField($parentField));
                    }

                    return $exp->eq($this->getTable()->aliasField($parentField), $node->get($parentField));
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
}

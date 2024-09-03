<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\ORM\Inheritance;

use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Association;
use Cake\ORM\AssociationCollection as CakeAssociationCollection;
use Traversable;

/**
 * Class to proxy all association operations to inherited tables.
 *
 * This allows associations to be inherited in a way that, if a new
 * association is added to the parent table, it becomes immediately
 * available for descendants.
 *
 * @since 4.0.0
 */
class AssociationCollection extends CakeAssociationCollection
{
    /**
     * Inner association collection.
     *
     * This represents the association collection of the inherited table.
     *
     * @var \Cake\ORM\AssociationCollection
     */
    protected $innerCollection;

    /**
     * Table instance.
     *
     * @var \BEdita\Core\ORM\Inheritance\Table
     */
    protected $table;

    /**
     * Class constructor.
     *
     * @param \BEdita\Core\ORM\Inheritance\Table $table Original table instance.
     */
    public function __construct(Table $table)
    {
        $this->table = $table;

        // Copy existing associations to the new collection. This is the new collection. Associations are copied here.
        $this->_items = $table->associations()->_items;

        $this->innerCollection = new CakeAssociationCollection();
        if ($table->inheritedTable() !== null) {
            $this->innerCollection = $table->inheritedTable()->associations();
        }
    }

    /**
     * Get the inherited associations collection.
     *
     * The clone of `self::innerCollection` is cleaned by `RelatedTo` associations
     * that involve concrete objects.
     *
     * @return \Cake\ORM\AssociationCollection
     */
    protected function inheritedAssociations()
    {
        $innerCollection = clone $this->innerCollection;
        foreach ($innerCollection as $association) {
            if (!($association instanceof RelatedTo) || $association->isSourceAbstract()) {
                continue;
            }

            $innerCollection->remove($association->getName());
        }

        return $innerCollection;
    }

    /**
     * Helper to adjust configuration for an association that's being inherited.
     *
     * When inheriting an association it is essential to clone the association class,
     * change the source table and ensure binding key and foreign key are "frozen" to avoid
     * conventions being applied against the new table.
     *
     * @param \Cake\ORM\Association|null $association Association being inherited.
     * @return \Cake\ORM\Association
     */
    protected function inheritAssociation(?Association $association = null)
    {
        if ($association === null) {
            return $association;
        }

        $inheritedAssociation = clone $association;
        $inheritedAssociation
            ->setSource($this->table)
            ->setBindingKey($association->getBindingKey())
            ->setForeignKey($association->getForeignKey());

        return $inheritedAssociation;
    }

    /**
     * @inheritDoc
     */
    public function get($alias): ?Association
    {
        $association = parent::get($alias);
        if ($association === null) {
            $association = $this->inheritAssociation($this->inheritedAssociations()->get($alias));
        }

        return $association;
    }

    /**
     * @inheritDoc
     */
    public function getByProperty($prop): ?Association
    {
        $association = parent::getByProperty($prop);
        if ($association === null) {
            $association = $this->inheritAssociation($this->inheritedAssociations()->getByProperty($prop));
        }

        return $association;
    }

    /**
     * @inheritDoc
     */
    public function has($alias): bool
    {
        return parent::has($alias) || $this->inheritedAssociations()->has($alias);
    }

    /**
     * @inheritDoc
     */
    public function keys(): array
    {
        return array_merge(parent::keys(), $this->inheritedAssociations()->keys());
    }

    /**
     * @inheritDoc
     */
    public function getByType($class): array
    {
        return array_merge(parent::getByType($class), $this->inheritedAssociations()->getByType($class));
    }

    /**
     * {@inheritDoc}
     *
     * @param bool $alias Should removal be cascaded to parent table's associations?
     */
    public function remove($alias, $cascade = true): void
    {
        parent::remove($alias);
        if ($cascade) {
            $this->innerCollection->remove($alias);
        }
    }

    /**
     * @inheritDoc
     */
    public function removeAll(): void
    {
        parent::removeAll();
        $this->innerCollection->removeAll();
    }

    /**
     * @inheritDoc
     */
    public function cascadeDelete(EntityInterface $entity, array $options): bool
    {
        if (!parent::cascadeDelete($entity, $options)) {
            return false;
        }

        return $this->inheritedAssociations()->cascadeDelete($entity, $options);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        $iterator = new \AppendIterator();
        $iterator->append(new \ArrayIterator($this->_items));
        $iterator->append($this->inheritedAssociations()->getIterator());

        return $iterator;
    }

    /**
     * Object clone hook.
     *
     * Clone the inner association collection.
     *
     * @return void
     */
    public function __clone()
    {
        $this->innerCollection = clone $this->innerCollection;
    }
}

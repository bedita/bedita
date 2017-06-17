<?php
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

use Cake\ORM\Association;
use Cake\ORM\AssociationCollection as CakeAssociationCollection;

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
     * @param \Cake\ORM\AssociationCollection $inner Inner association collection.
     */
    public function __construct(Table $table, CakeAssociationCollection $inner)
    {
        $this->table = $table;
        $this->innerCollection = $inner;

        // Copy existing associations to the new collection. This is the new collection. Associations are copied here.
        $this->_items = $table->associations()->_items;
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
    protected function inheritAssociation(Association $association = null)
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
     * {@inheritDoc}
     */
    public function get($alias)
    {
        $association = parent::get($alias);
        if ($association === null) {
            $association = $this->inheritAssociation($this->innerCollection->get($alias));
        }

        return $association;
    }

    /**
     * {@inheritDoc}
     */
    public function getByProperty($prop)
    {
        $association = parent::getByProperty($prop);
        if ($association === null) {
            $association = $this->inheritAssociation($this->innerCollection->getByProperty($prop));
        }

        return $association;
    }

    /**
     * {@inheritDoc}
     */
    public function has($alias)
    {
        return parent::has($alias) || $this->innerCollection->has($alias);
    }

    /**
     * {@inheritDoc}
     */
    public function keys()
    {
        return array_merge(parent::keys(), $this->innerCollection->keys());
    }

    /**
     * {@inheritDoc}
     */
    public function type($class)
    {
        return array_merge(parent::type($class), $this->innerCollection->type($class));
    }

    /**
     * {@inheritDoc}
     *
     * @param bool $cascade Should removal be cascaded to parent table's associations?
     */
    public function remove($alias, $cascade = true)
    {
        parent::remove($alias);
        if ($cascade) {
            $this->innerCollection->remove($alias);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function removeAll()
    {
        parent::removeAll();
        $this->innerCollection->removeAll();
    }

    /**
     * {@inheritDoc}
     */
    protected function _getNoCascadeItems($entity, $options)
    {
        return array_merge(
            parent::_getNoCascadeItems($entity, $options),
            $this->innerCollection->_getNoCascadeItems($entity, $options)
        );
    }
}

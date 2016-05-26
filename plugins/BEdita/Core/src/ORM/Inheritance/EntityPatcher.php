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

use Cake\ORM\Entity;
use Cake\ORM\Table;

/**
 * EntityPatcher class.
 *
 * Used to patch Entity or array through the inheritance.
 *
 * @since 4.0.0
 */
class EntityPatcher
{
    /**
     * Table instance.
     *
     * @var \Cake\ORM\Table
     */
    protected $table = null;

    /**
     * Constructor. Check if Table is set up to use class table inheritance
     *
     * @param Cake\ORM\Table $table The Table instance
     */
    public function __construct(Table $table)
    {
        if (!$table->hasBehavior('ClassTableInheritance')) {
            throw new \InvalidArgumentException(sprintf(
                'Table %s must use ClassTableInheritance behavior',
                $table->alias()
            ));
        }
        $this->table = $table;
    }

    /**
     * Flatten an Entity or array using inherited tables
     *
     * All associations defined by inherited tables are collapsed on current entity
     *
     * @param \Cake\ORM\Entity|array $row The entity or array to flatten
     * @return \Cake\ORM\Entity|array
     */
    public function flatten($row)
    {
        $inheritedTables = $this->table->inheritedTables(true);

        foreach ($inheritedTables as $key => $inherited) {
            $source = ($key > 0) ? $inheritedTables[$key - 1] : $this->table;
            $property = $source
                ->association($inherited->alias())
                ->property();

            if (empty($row[$property])) {
                return $row;
            }

            $flattenMethod = ($row[$property] instanceof Entity) ? 'flattenEntityProperty' : 'flattenArrayProperty';
            $this->{$flattenMethod}($row, $property);
        }

        return $row;
    }

    /**
     * Given an `$entity` and a `$property`
     * flatten the `$entity->$property` as current `$entity` properties
     *
     * @param \Cake\ORM\Entity $entity The entity object
     * @param string $property The entity property that represents an association
     * @return void
     */
    protected function flattenEntityProperty(Entity $entity, $property)
    {
        $entityToFlat = $entity->$property;
        foreach ($entityToFlat->visibleProperties() as $prop) {
            $entity->set($prop, $entityToFlat->$prop);
            $entity->dirty($prop, false);
        }
        $entity->unsetProperty($property);
    }

    /**
     * Given an array and a property flatten the array[property] data
     *
     * @param array $row The array
     * @param string $property The property that represents an association
     * @return void
     */
    protected function flattenArrayProperty(array &$row, $property)
    {
        $row = array_merge($row, $row[$property]);
        unset($row[$property]);
    }
}

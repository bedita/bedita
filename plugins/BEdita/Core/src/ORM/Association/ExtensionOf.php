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

namespace BEdita\Core\ORM\Association;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Entity;

/**
 * Represents an 1 - 1 relationship where the source side of the relation is
 * related to only one record in the target table and vice versa.
 * Note that the target fields will be merged with source fields as it represents
 * an association in CTI scenario.
 *
 * The association extends BelongsTo for saving the target side before the source side
 * but it is defined as Association::ONE_TO_ONE instead of Association::MANY_TO_ONE.
 *
 * Unlike BelongsTo associations ExtensionOf are cleared in a cascading delete scenario.
 *
 * An example of a ExtensionOf association would be Mammals is an extension of Animals.
 * In this scenario:
 *
 * - saving Mammals will save first the associated Animals
 * - deleting Mammals will also delete the associated Animals
 *
 * @since 4.0.0
 */
class ExtensionOf extends BelongsTo
{

    /**
     * {@inheritDoc}
     */
    protected $_joinType = 'INNER';

    /**
     * {@inheritDoc}
     *
     * Add `Model.afterDelete` listener to work in a cascading delete scenario.
     * The `cascadeDelete()` used by CakePHP in fact would fail for constraint violation error
     * deleting first target table when the foreign key is in source table
     * @param string $alias
     */
    public function __construct($alias, array $options = [])
    {
        parent::__construct($alias, $options);

        $this->getSource()
            ->eventManager()
            ->on(
                'Model.afterDelete',
                function (Event $event, Entity $entity, \ArrayObject $options) {
                    $bindingKey = (array)$this->getBindingKey();
                    $entity = $this->getTarget()->get($entity->extract($bindingKey));

                    return $this->getTarget()->delete($entity, ['_primary' => false] + $options->getArrayCopy());
                }
            );
    }

    /**
     * {@inheritDoc}
     */
    public function type()
    {
        return self::ONE_TO_ONE;
    }

    /**
     * {@inheritDoc}
     */
    public function transformRow($row, $nestKey, $joined, $targetProperty = null)
    {
        $sourceAlias = $this->getSource()->getAlias();
        $nestKey = $nestKey ?: $this->_name;
        if (!isset($row[$sourceAlias])) {
            return $row;
        }

        // remove key corresponding to source alias
        $row[$sourceAlias] = array_diff_key($row[$sourceAlias], array_flip([$sourceAlias]));

        $properties = $row[$nestKey];
        if ($properties instanceof Entity) {
            $properties = $properties->getOriginalValues();
        }

        // get properties except key corresponding to $nestKey
        if (is_array($properties)) {
            $row[$sourceAlias] += array_diff_key($properties, array_flip([$nestKey]));
        }
        unset($row[$nestKey]);

        return $row;
    }

    /**
     * {@inheritDoc}
     */
    public function saveAssociated(EntityInterface $entity, array $options = [])
    {
        $targetData = $this->targetPropertiesValues($entity);
        $defaultValues = array_map(
            function ($val) {
                if (is_string($val) && substr($val, 0, 6) === 'NULL::') {
                    return null;
                }

                return $val;
            },
            $this->getTarget()->getSchema()->defaultValues()
        );
        $propertiesToRemove = array_keys($defaultValues);

        $targetEntity = $this->getTarget()->newEntity($defaultValues, [
            'accessibleFields' => ['*' => true],
        ]);
        $targetEntity->isNew($entity->isNew());
        $targetEntity = $this->getTarget()->patchEntity($targetEntity, $targetData, [
            'accessibleFields' => ['*' => true],
        ]);
        if (!$entity->isNew()) {
            $targetEntity->dirty($this->getBindingKey(), true);
        }

        if (empty($targetEntity) || !($targetEntity instanceof EntityInterface)) {
            return $entity;
        }

        $targetEntity = $this->getTarget()->save($targetEntity, $options);
        if (!$targetEntity) {
            return false;
        }

        $properties = array_combine(
            (array)$this->getForeignKey(),
            $targetEntity->extract((array)$this->getBindingKey())
        );
        $properties += $targetEntity->extract($targetEntity->visibleProperties() + $targetEntity->hiddenProperties());
        if (isset($propertiesToRemove)) {
            $properties = array_diff_key($properties, array_flip($propertiesToRemove));
        }

        $entity->set($properties, ['guard' => false]);

        return $entity;
    }

    /**
     * Return all properties values that not belong to table source `$entity.
     * It check all `$entity` visible properties plus hidden properties
     * plus Table source associations' properties.
     *
     * @param \Cake\Datasource\EntityInterface $entity an entity from the source table
     * @return array
     */
    protected function targetPropertiesValues(EntityInterface $entity)
    {
        $properties = array_merge($entity->visibleProperties(), $entity->hiddenProperties());
        $propertyValues = [];
        foreach ($properties as $prop) {
            $value = $entity->get($prop);
            if ($value === null && !$entity->dirty($prop)) {
                continue;
            }

            $propertyValues[$prop] = $value;
        }

        $source = $this->getSource();
        $sourceProperties = array_diff($source->getSchema()->columns(), [$source->getPrimaryKey()]);
        foreach ($source->associations()->keys() as $key) {
            $sourceProperties[] = $source->association($key)->getProperty();
        }

        return array_diff_key($propertyValues, array_flip($sourceProperties));
    }
}

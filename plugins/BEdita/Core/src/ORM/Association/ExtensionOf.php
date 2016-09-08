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
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\DependentDeleteTrait;
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
    use DependentDeleteTrait;

    /**
     * {@inheritDoc}
     */
    protected $_dependent = true;

    /**
     * {@inheritDoc}
     */
    protected $_cascadeCallbacks = true;

    /**
     * {@inheritDoc}
     */
    protected $_joinType = 'INNER';

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
        $sourceAlias = $this->source()->alias();
        $nestKey = $nestKey ?: $this->_name;
        if (!isset($row[$sourceAlias])) {
            return $row;
        }

        $properties = ($row[$nestKey] instanceof Entity) ? $row[$nestKey]->getOriginalValues() : $row[$nestKey];
        $row[$sourceAlias] += $properties;
        unset($row[$nestKey]);

        return $row;
    }

    /**
     * {@inheritDoc}
     */
    public function saveAssociated(EntityInterface $entity, array $options = [])
    {
        $targetData = $this->targetPropertiesValues($entity);
        if (empty($targetData)) {
            $targetData = $this->target()->schema()->defaultValues();
            $propertiesToRemove = array_keys($targetData);
        }

        $targetEntity = $this->target()->newEntity($targetData, [
            'accessibleFields' => ['*' => true]
        ]);

        if (empty($targetEntity) || !($targetEntity instanceof EntityInterface)) {
            return $entity;
        }

        $targetEntity = $this->target()->save($targetEntity, $options);
        if (!$targetEntity) {
            return false;
        }

        $properties = array_combine(
            (array)$this->foreignKey(),
            $targetEntity->extract((array)$this->bindingKey())
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
        $properties = $entity->visibleProperties() + $entity->hiddenProperties();
        $propertyValues = $entity->extract($properties);

        $source = $this->source();
        $sourceProperties = array_diff($source->schema()->columns(), [$source->primaryKey()]);
        foreach ($source->associations()->keys() as $key) {
            $sourceProperties[] = $source->association($key)->property();
        }

        return array_diff_key($propertyValues, array_flip($sourceProperties));
    }

    /**
     * Override `\Cake\ORM\Association::_appendFields()`
     * to append target fields set with `\Cake\ORM\Query::select()`
     * without target table alias.
     *
     * The following steps are executed for each field in `$query` select clause:
     *
     * - if field doesn't contain "." and it belongs to target table then build `TargetTableAlias.field`
     * - if field contains "." but doesn't contain source alias `SourceTableAlias.` then leave unchanged
     * - if field contains "." and it's a source table field then leave unchanged
     * - if no above conditions is satisfied then it doesn't exists in source table so set it for target table
     *   `TargetTableAlias.field` delegating to any ExtensionOf association linked to target table
     *
     * {@inheritDoc}
     */
    protected function _appendFields($query, $surrogate, $options)
    {
        $selectClause = $query->clause('select');
        if (empty($selectClause)) {
            return parent::_appendFields($query, $surrogate, $options);
        }

        $source = $this->source();
        $target = $this->target();
        $targetSelect = [];

        foreach ($selectClause as $key => $val) {
            if (!is_string($val)) {
                continue;
            }

            if (strpos($val, '.') === false && $target->hasField($val)) {
                $selectClause[$key] = $target->alias() . '.' . $val;
                continue;
            }

            if (strpos($val, $source->alias() . '.') === false) {
                continue;
            }

            $field = str_replace($source->alias() . '.', '', $val);
            if ($source->hasField($field)) {
                continue;
            }

            unset($selectClause[$key]);
            $targetSelect[] = $field;
        }

        $selectClause += $query->aliasFields($targetSelect, $target->alias());
        $query->select($selectClause, true);

        return parent::_appendFields($query, $surrogate, $options);
    }

    // public function attachTo(Query $query, array $options = [])
    // {
        // use query traverse
    // }
}

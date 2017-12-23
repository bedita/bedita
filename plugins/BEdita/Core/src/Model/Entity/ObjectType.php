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

namespace BEdita\Core\Model\Entity;

use BEdita\Core\Utility\JsonApiSerializable;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * ObjectType Entity.
 *
 * @property int $id
 * @property string $name
 * @property string $singular
 * @property string $alias
 * @property string $description
 * @property string $plugin
 * @property string $model
 * @property string $table
 * @property string $associations
 * @property string $hidden
 * @property string[] $relations
 * @property bool $is_abstract
 * @property int $parent_id
 * @property int $tree_left
 * @property int $tree_right
 * @property string $parent_name
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property bool $core_type
 * @property bool $enabled
 * @property \BEdita\Core\Model\Entity\ObjectEntity[] $objects
 * @property \BEdita\Core\Model\Entity\Relation[] $left_relations
 * @property \BEdita\Core\Model\Entity\Relation[] $right_relations
 * @property \BEdita\Core\Model\Entity\Property[] $properties
 * @property \BEdita\Core\Model\Entity\ObjectType $parent
 * @property mixed $schema
 */
class ObjectType extends Entity implements JsonApiSerializable
{
    use JsonApiModelTrait {
        listAssociations as protected jsonApiListAssociations;
    }

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => false,
        'name' => true,
        'singular' => true,
        'description' => true,
        'table' => true,
        'associations' => true,
        'hidden' => true,
        'is_abstract' => true,
        'parent_name' => true,
        'enabled' => true,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_virtual = [
        'alias',
        'table',
        'parent_name',
        'relations',
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'objects',
        'model',
        'plugin',
        'properties',
        'parent_id',
        'tree_left',
        'tree_right',
    ];

    /**
     * Setter for property `name`.
     *
     * Force `name` field to be underscored via inflector.
     *
     * @param string $name Object type name.
     * @return string
     */
    protected function _setName($name)
    {
        return Inflector::underscore($name);
    }

    /**
     * Getter for property `singular`.
     *
     * If `singular` field is not set or empty, use inflected form of `name`.
     *
     * @return string
     */
    protected function _getSingular()
    {
        if (!empty($this->_properties['singular'])) {
            return $this->_properties['singular'];
        }

        return Inflector::singularize($this->name);
    }

    /**
     * Setter for property `singular`.
     *
     * Force `singular` field to be underscored via inflector.
     *
     * @param string|null $singular Object type singular name.
     * @return string
     */
    protected function _setSingular($singular)
    {
        return Inflector::underscore($singular);
    }

    /**
     * Getter for virtual property `alias`.
     *
     * @return string
     */
    protected function _getAlias()
    {
        return Inflector::camelize($this->name);
    }

    /**
     * Getter for virtual property `table`.
     *
     * @return string
     */
    protected function _getTable()
    {
        $table = $this->plugin . '.';
        if ($table == '.') {
            $table = '';
        }

        $table .= $this->model;

        return $table;
    }

    /**
     * Setter for virtual property `table`.
     *
     * @param string $table Full table name.
     * @return void
     */
    protected function _setTable($table)
    {
        list($plugin, $model) = pluginSplit($table);

        $this->plugin = $plugin;
        $this->model = $model;
    }

    /**
     * Getter for virtual property `relations`.
     *
     * @return string[]|null
     */
    protected function _getRelations()
    {
        if (!$this->has('left_relations') || !$this->has('right_relations')) {
            return null;
        }

        $relations = array_merge(
            Hash::extract($this->left_relations, '{n}.name'),
            Hash::extract($this->right_relations, '{n}.inverse_name')
        );

        return $relations;
    }

    /**
     * {@inheritDoc}
     */
    protected static function listAssociations(Table $Table, array $hidden = [])
    {
        $associations = static::jsonApiListAssociations($Table, $hidden);
        $associations = array_diff($associations, ['relations']);

        return $associations;
    }

    /**
     * Getter for virtual property `parent_name`.
     *
     * @return string
     */
    protected function _getParentName()
    {
        if (!$this->parent_id) {
            return null;
        }

        if (!empty($this->parent)) {
            return $this->parent->get('name');
        }

        return TableRegistry::get('ObjectTypes')->get($this->parent_id)->get('name');
    }

    /**
     * Setter for virtual property `parent_name`.
     *
     * @param string $parentName Parent object type name.
     * @return string
     */
    protected function _setParentName($parentName)
    {
        try {
            $objectType = TableRegistry::get('ObjectTypes')->get($parentName);
            if (!$objectType->get('is_abstract') || !$objectType->get('enabled')) {
                return null;
            }
            $this->parent = $objectType;
            $this->parent_id = $objectType->id;
        } catch (RecordNotFoundException $e) {
            return null;
        }

        return $parentName;
    }

    /**
     * Getter for virtual property `schema`.
     *
     * @return mixed
     */
    protected function _getSchema()
    {
        if ($this->is_abstract || empty($this->id)) {
            return false;
        }

        /** @var \BEdita\Core\Model\Entity\Property[] $allProperties */
        $allProperties = TableRegistry::get('Properties')
            ->find('objectType', [$this->id])
            ->toArray();
        $entity = TableRegistry::get($this->name)->newEntity();
        $hiddenProperties = $entity->hiddenProperties();

        $properties = $required = [];
        foreach ($allProperties as $property) {
            $accessMode = null;
            if (!$entity->isAccessible($property->name)) {
                $accessMode = 'readOnly';
            } elseif (in_array($property->name, $hiddenProperties)) {
                $accessMode = 'writeOnly';
            }
            $properties[$property->name] = $property->getSchema($accessMode);

            if ($property->required && $accessMode === null) {
                $required[] = $property->name;
            }
        }

        return compact('properties', 'required');
    }
}

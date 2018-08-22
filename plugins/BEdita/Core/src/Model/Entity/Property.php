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

namespace BEdita\Core\Model\Entity;

use BEdita\Core\Model\Table\ObjectTypesTable;
use BEdita\Core\Utility\JsonApiSerializable;
use Cake\Cache\Cache;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * Property Entity.
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $enabled
 * @property bool $is_nullable
 * @property bool $required
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $object_type_id
 * @property string $object_type_name
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 * @property int $property_type_id
 * @property string $property_type_name
 * @property \BEdita\Core\Model\Entity\PropertyType $property_type
 *
 * @since 4.0.0
 */
class Property extends Entity implements JsonApiSerializable
{

    use JsonApiModelTrait;

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'object_type_id' => false,
        'object_type' => false,
        'property_type_id' => false,
        'property_type' => false,
        'property' => false,
        'enabled' => false,
        'created' => false,
        'modified' => false,
        'default' => false,
        'required' => false,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_virtual = [
        'property_type_name',
        'object_type_name',
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'object_type_id',
        'object_type',
        'property_type_id',
        'property_type',
        'enabled',
    ];

    /**
     * Getter for `property_type` property with lazy loading.
     *
     * @return \BEdita\Core\Model\Entity\PropertyType|null
     */
    protected function _getPropertyType()
    {
        if (array_key_exists('property_type', $this->_properties)) {
            return $this->_properties['property_type'];
        }

        try {
            $this->_properties['property_type'] = TableRegistry::get('PropertyTypes')
                ->get($this->property_type_id, [
                    'cache' => ObjectTypesTable::CACHE_CONFIG,
                ]);

            return $this->_properties['property_type'];
        } catch (RecordNotFoundException $e) {
            return null;
        } catch (InvalidPrimaryKeyException $e) {
            return null;
        }
    }

    /**
     * Getter for `property_type_name` virtual property.
     *
     * @return string
     */
    protected function _getPropertyTypeName()
    {
        if (!$this->property_type) {
            return null;
        }

        return $this->property_type->name;
    }

    /**
     * Setter for `property_type_name` virtual property.
     *
     * @param string $propertyType Property type name.
     * @return string
     */
    protected function _setPropertyTypeName($propertyType)
    {
        /* @var \BEdita\Core\Model\Entity\PropertyType[] $propertyTypes */
        $propertyTypes = Cache::remember(
            'property_types',
            function () {
                return TableRegistry::get('PropertyTypes')->find()
                    ->indexBy('name')
                    ->toArray();
            },
            ObjectTypesTable::CACHE_CONFIG
        );

        if (empty($propertyTypes[$propertyType])) {
            // Unknown property type.
            $this->property_type = null;
            $this->property_type_id = null;

            return null;
        }

        $this->property_type = $propertyTypes[$propertyType];
        $this->property_type_id = $this->property_type->id;

        return $this->property_type->name;
    }

    /**
     * Getter for `object_type_name` virtual property.
     *
     * @return string
     */
    protected function _getObjectTypeName()
    {
        if (!$this->object_type) {
            try {
                $this->object_type = TableRegistry::get('ObjectTypes')->get($this->object_type_id);
            } catch (RecordNotFoundException $e) {
                return null;
            } catch (InvalidPrimaryKeyException $e) {
                return null;
            }
        }

        return $this->object_type->name;
    }

    /**
     * Setter for `object_type` virtual property.
     *
     * @param string $objectTypeName Object type name.
     * @return string
     */
    protected function _setObjectTypeName($objectTypeName)
    {
        try {
            $this->object_type = TableRegistry::get('ObjectTypes')->get($objectTypeName);
            $this->object_type_id = $this->object_type->id;
        } catch (RecordNotFoundException $e) {
            $this->object_type = null;
            $this->object_type_id = null;

            return null;
        }

        return $objectTypeName;
    }

    /**
     * Getter for `required` virtual property.
     *
     * @return bool
     */
    protected function _getRequired()
    {
        return !$this->is_nullable;
    }

    /**
     * Get property schema.
     *
     * @param string|null $accessMode Access mode (either `"readOnly"` or `"writeOnly"`, or `null` for read-write access).
     * @return mixed
     */
    public function getSchema($accessMode = null)
    {
        if (!$this->property_type) {
            // Missing property type. Validation party: anything is allowed.
            return true;
        }

        $schema = $this->property_type->params;
        if (!is_array($schema)) {
            // Booleans are valid schemas, though they're quite uncommon.
            return $schema;
        }

        if ($this->is_nullable) {
            // Property is nullable.
            $schema = [
                'oneOf' => [
                    [
                        'type' => 'null',
                    ],
                    $schema,
                ],
            ];
        }

        // Additional metadata.
        $schema['$id'] = sprintf('/properties/%s', $this->name);
        $schema['title'] = Inflector::humanize($this->name);
        $schema['description'] = $this->description;
        if (in_array($accessMode, ['readOnly', 'writeOnly'])) {
            $schema[$accessMode] = true;
        }

        return $schema;
    }
}

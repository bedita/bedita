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

use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Property Entity.
 *
 * @property int $id
 * @property string $name
 * @property int $object_type_id
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 * @property string $object_type_name
 * @property int $property_type_id
 * @property \BEdita\Core\Model\Entity\PropertyType $property_type
 * @property string $property_type_name
 * @property bool $multiple
 * @property string $options_list
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property string $description
 * @property bool $enabled
 *
 * @since 4.0.0
 */
class Property extends Entity
{

    use JsonApiTrait;

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
     * Getter for `property_type_name` virtual property.
     *
     * @return string
     */
    protected function _getPropertyTypeName()
    {
        if (!$this->property_type) {
            try {
                $this->property_type = TableRegistry::get('PropertyTypes')->get($this->property_type_id);
            } catch (RecordNotFoundException $e) {
                return null;
            } catch (InvalidPrimaryKeyException $e) {
                return null;
            }
        }

        return $this->property_type->name;
    }

    /**
     * Setter for `property_type_name` virtual property.
     *
     * @param string $property Property type name.
     * @return string
     */
    protected function _setPropertyTypeName($property)
    {
        try {
            $this->property_type = TableRegistry::get('PropertyTypes')->find()
                ->where(['name' => $property])
                ->firstOrFail();
            $this->property_type_id = $this->property_type->id;
        } catch (RecordNotFoundException $e) {
            return null;
        }

        return $property;
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
            return null;
        }

        return $objectTypeName;
    }
}

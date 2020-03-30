<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
use Cake\ORM\TableRegistry;

/**
 * Trait implementing virtual object type getter/setter via its name.
 *
 * @since 4.0.0
 *
 * @property int $object_type_id
 * @property string $object_type_name
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 */
trait ObjectTypeNameTrait
{
    /**
     * Getter for `object_type_name` virtual property.
     *
     * @return string|null
     */
    protected function _getObjectTypeName(): ?string
    {
        if (empty($this->object_type)) {
            try {
                $this->object_type = TableRegistry::getTableLocator()->get('ObjectTypes')->get($this->object_type_id);
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
     * @return string|null
     */
    protected function _setObjectTypeName(string $objectTypeName): ?string
    {
        try {
            $this->object_type = TableRegistry::getTableLocator()->get('ObjectTypes')->get($objectTypeName);
            $this->object_type_id = $this->object_type->id;
        } catch (RecordNotFoundException $e) {
            $this->object_type = null;
            $this->object_type_id = null;

            return null;
        }

        return $objectTypeName;
    }
}

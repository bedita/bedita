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

use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Object Entity.
 *
 * @property int $id
 * @property int $object_type_id
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 * @property string $type
 * @property string $status
 * @property string $uname
 * @property bool $locked
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $published
 * @property string $title
 * @property string $description
 * @property string $body
 * @property string $extra
 * @property string $lang
 * @property int $created_by
 * @property int $modified_by
 * @property \Cake\I18n\Time $publish_start
 * @property \Cake\I18n\Time $publish_end
 *
 * @since 4.0.0
 */
class Object extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'object_type_id' => false,
        'object_type' => false,
        'type' => false,
        'deleted' => false,
        'locked' => false,
        'created' => false,
        'modified' => false,
        'published' => false,
        'created_by' => false,
        'modified_by' => false,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_virtual = [
        'type',
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'object_type_id',
        'object_type',
        'deleted',
    ];

    /**
     * Getter for `type` virtual property.
     *
     * @return string
     */
    protected function _getType()
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

        return $this->object_type->pluralized;
    }

    /**
     * Setter for `type` virtual property.
     *
     * @param string $type Object type name.
     * @return string
     */
    protected function _setType($type)
    {
        try {
            $this->object_type = TableRegistry::get('ObjectTypes')->get($type);
            $this->object_type_id = $this->object_type->id;
        } catch (RecordNotFoundException $e) {
            return null;
        }

        return $type;
    }
}

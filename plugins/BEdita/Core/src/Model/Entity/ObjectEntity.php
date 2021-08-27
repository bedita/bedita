<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Object Entity.
 *
 * @property int $id
 * @property int $object_type_id
 * @property bool $deleted
 * @property string $type
 * @property string $status
 * @property string $uname
 * @property bool $locked
 * @property \Cake\I18n\Time|\Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\Time|\Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\Time|\Cake\I18n\FrozenTime $published
 * @property string $title
 * @property string $description
 * @property string $body
 * @property array $custom_props
 * @property array $extra
 * @property string $lang
 * @property int $created_by
 * @property int $modified_by
 * @property \Cake\I18n\Time|\Cake\I18n\FrozenTime $publish_start
 * @property \Cake\I18n\Time|\Cake\I18n\FrozenTime $publish_end
 *
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 * @property \BEdita\Core\Model\Entity\User $created_by_user
 * @property \BEdita\Core\Model\Entity\User $modified_by_user
 * @property \BEdita\Core\Model\Entity\DateRange[] $date_ranges
 * @property \BEdita\Core\Model\Entity\Folder[] $parents
 * @property \BEdita\Core\Model\Entity\Tree[] $tree_nodes
 * @property \BEdita\Core\Model\Entity\Translation[] $translations
 *
 * @since 4.0.0
 */
class ObjectEntity extends Entity implements JsonApiSerializable
{
    use JsonApiTrait {
        listAssociations as protected jsonApiListAssociations;
        getMeta as protected jsonApiGetMeta;
    }

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
        'created_by_user',
        'modified_by_user',
        'object_type_id',
        'object_type',
        'deleted',
        'custom_props',
        'tree_nodes',
    ];

    /**
     * See if a property has been set in an entity.
     * Could be set in `_properties` array or a virtual one.
     * Options to exclude hidden properties and to include virtual properties.
     *
     * @param string $property Property name
     * @param bool $hidden Include hidden (default true)
     * @param bool $virtual Include virtual (default false)
     * @return bool
     */
    public function hasProperty(string $property, bool $hidden = true, bool $virtual = false)
    {
        if ($hidden && !$virtual) {
            return array_key_exists($property, $this->_properties);
        }

        $properties = array_keys($this->_properties);
        if (!$hidden) {
            $properties = array_diff($properties, $this->_hidden);
        }
        if ($virtual) {
            $properties = array_merge($properties, $this->_virtual);
        }

        return in_array($property, $properties);
    }

    /**
     * {@inheritDoc}
     */
    public function getTable()
    {
        return TableRegistry::getTableLocator()->get($this->type ?: $this->getSource());
    }

    /**
     * {@inheritDoc}
     */
    protected function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    protected function getMeta()
    {
        return array_diff_key($this->jsonApiGetMeta(), array_flip(['type']));
    }

    /**
     * {@inheritDoc}
     */
    protected function getLinks()
    {
        $options = [
            '_name' => 'api:objects:resource',
            'object_type' => $this->type,
            'id' => $this->id,
        ];
        if ($this->deleted) {
            $options = [
                '_name' => 'api:trash:resource',
                'id' => $this->id,
            ];
        }

        return [
            'self' => Router::url($options, true),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected static function listAssociations(Table $Table, array $hidden = [])
    {
        $associations = static::jsonApiListAssociations($Table, $hidden);
        $associations = array_diff($associations, ['date_ranges', 'categories', 'tags']);

        return $associations;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRelationships()
    {
        $relationships = $included = [];
        if ($this->deleted) {
            return [$relationships, $included];
        }

        $entity = $this;
        $table = $this->getTable();
        if ($table->getRegistryAlias() !== $this->getSource()) {
            $entity = $table->newEntity();
        }

        $associations = $entity::listAssociations($table, $entity->getHidden());
        foreach ($associations as $relationship) {
            $self = Router::url(
                [
                    '_name' => 'api:objects:relationships',
                    'object_type' => $this->type,
                    'relationship' => $relationship,
                    'id' => $this->getId(),
                ],
                true
            );
            $related = Router::url(
                [
                    '_name' => 'api:objects:related',
                    'object_type' => $this->type,
                    'relationship' => $relationship,
                    'related_id' => $this->getId(),
                ],
                true
            );

            if ($this->has($relationship)) {
                $entities = $this->get($relationship);
                $data = $this->getIncluded($entities);
                if (!is_array($entities)) {
                    $entities = [$entities];
                }
                $included = array_merge($included, $entities);
            }

            $relationships[$relationship] = [
                'links' => compact('related', 'self'),
            ];
            if (isset($data)) {
                $relationships[$relationship] += compact('data');
                unset($data);
            }
            $count = $this->getRelationshipCount($relationship);
            if ($count !== null) {
                $relationships[$relationship] += [
                    'meta' => compact('count'),
                ];
            }
        }

        return [$relationships, $included];
    }

    /**
     * {@inheritDoc}
     */
    public function getVisible()
    {
        $visible = parent::getVisible();
        $this->loadObjectType();
        if (!$this->object_type) {
            return $visible;
        }

        $hidden = !empty($this->object_type->hidden) ? $this->object_type->hidden : [];

        return array_diff($visible, $hidden);
    }

    /**
     * Load `object_type`, read from object_types table if not set.
     *
     * @return void
     */
    protected function loadObjectType()
    {
        if (!$this->object_type) {
            try {
                $typeId = $this->object_type_id ?: $this->getSource();
                $this->object_type = TableRegistry::getTableLocator()->get('ObjectTypes')->get($typeId);
            } catch (RecordNotFoundException $e) {
            } catch (InvalidPrimaryKeyException $e) {
            }
        }
    }

    /**
     * Getter for `type` virtual property.
     *
     * @return string
     */
    protected function _getType()
    {
        $this->loadObjectType();
        if (!$this->object_type) {
            return null;
        }

        return $this->object_type->name;
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
            $this->object_type = TableRegistry::getTableLocator()->get('ObjectTypes')->get($type);
            $this->object_type_id = $this->object_type->id;
            $this->setDirty('object_type_id', true);
        } catch (RecordNotFoundException $e) {
            return null;
        }

        return $type;
    }
}

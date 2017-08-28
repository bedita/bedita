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
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Object Entity.
 *
 * @property int $id
 * @property int $object_type_id
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 * @property bool $deleted
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
 * @property array $extra
 * @property string $lang
 * @property int $created_by
 * @property int $modified_by
 * @property \Cake\I18n\Time $publish_start
 * @property \Cake\I18n\Time $publish_end
 * @property \Bedita\Core\Model\Entity\DateRange[] $date_ranges
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
    ];

    /**
     * {@inheritDoc}
     */
    public function getTable()
    {
        return TableRegistry::get($this->type ?: $this->getSource());
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
        $associations = array_diff($associations, ['date_ranges']);

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

        $associations = static::listAssociations($table, $entity->getHidden());
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
            $entityDest = $this->getTable()->associations()->getByProperty($relationship)->getTarget()->newEntity();
            if ($entityDest instanceof JsonApiSerializable) {
                $destObj = $entityDest->jsonApiSerialize(JsonApiSerializable::JSONAPIOPT_BASIC);
                if (empty($destObj['type'])) {
                    $objectType = TableRegistry::get('ObjectTypes')->get($this->type);
                    foreach ($objectType->right_relations as $relation) {
                        if ($relation->inverse_name !== $relationship) {
                            continue;
                        }
                        $result = Hash::extract($relation->left_object_types, '{n}.name');
                    }
                    foreach ($objectType->left_relations as $relation) {
                        if ($relation->name !== $relationship) {
                            continue;
                        }
                        $result = Hash::extract($relation->right_object_types, '{n}.name');
                    }
                    if (!empty($result)) {
                        $available = Router::url(
                            [
                                '_name' => 'api:objects:index',
                                'object_type' => 'objects',
                                'filter' => ['type' => array_values($result)],
                            ],
                            true
                        );
                    }
                } else {
                    $available = Router::url(
                        [
                            '_name' => 'api:resources:index',
                            'controller' => $destObj['type'],
                        ],
                        true
                    );
                }
            }

            if ($this->has($relationship)) {
                $entities = $this->get($relationship);
                $data = $this->getIncluded($entities);
                $included = array_merge($included, $entities);
            }

            $relationships[$relationship] = compact('data') + [
                'links' => compact('related', 'self', 'available'),
            ];
            unset($data);
        }

        return [$relationships, $included];
    }

    /**
     * {@inheritDoc}
     */
    public function visibleProperties()
    {
        $visible = parent::visibleProperties();
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
                $this->object_type = TableRegistry::get('ObjectTypes')->get($typeId);
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
            $this->object_type = TableRegistry::get('ObjectTypes')->get($type);
            $this->object_type_id = $this->object_type->id;
            $this->setDirty('object_type_id', true);
        } catch (RecordNotFoundException $e) {
            return null;
        }

        return $type;
    }
}

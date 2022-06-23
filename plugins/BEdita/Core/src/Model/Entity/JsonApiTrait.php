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

use BEdita\Core\Utility\JsonApiSerializable;
use Cake\ORM\Association;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Trait for exposing useful properties required for JSON API response formatting at the entity level.
 *
 * @since 4.0.0
 * @property string $type
 * @property string[] $relationships
 * @property string[] $meta
 */
trait JsonApiTrait
{
    /**
     * Selected fields in JSON API `attributes` and `meta` response.
     * If empty all fields are shown.
     *
     * @var array
     */
    protected $_fields = [];

    /**
     * Getter for entity's visible properties.
     *
     * @return string[]
     */
    abstract public function getVisible();

    /**
     * Getter for entity's hidden properties.
     *
     * @return string[]
     */
    abstract public function getHidden();

    /**
     * Getter for entity's virtual properties.
     *
     * @return string[]
     */
    abstract public function getVirtual();

    /**
     * Getter for source model registry alias.
     *
     * @return string
     */
    abstract public function getSource();

    /**
     * Getter for model table.
     *
     * @return \Cake\ORM\Table
     */
    public function getTable()
    {
        return TableRegistry::getTableLocator()->get($this->getSource());
    }

    /**
     * Checks if a property is accessible.
     *
     * @param string $property Property name to check
     * @return bool
     */
    abstract public function isAccessible(string $property);

    /**
     * Extract properties from an entity.
     *
     * @param array $properties List of properties to extract
     * @param bool $onlyDirty Return only dirty properties.
     * @return array
     */
    abstract public function extract(array $properties, $onlyDirty = false);

    /**
     * Check if a property exists.
     *
     * @param string $property Property name.
     * @return bool
     */
    abstract public function has($property);

    /**
     * Getter for a property.
     *
     * @param string $property Property name.
     * @return mixed
     */
    abstract public function &get($property);

    /**
     * Getter for `id`.
     *
     * @return string
     */
    protected function getId()
    {
        return implode(',', $this->extract((array)$this->getTable()->getPrimaryKey()));
    }

    /**
     * Getter for `type`.
     *
     * @return string
     */
    protected function getType()
    {
        return $this->getTable()->getTable();
    }

    /**
     * Setter for `_fields`.
     *
     * @param array $fields List of fields.
     * @return void
     */
    protected function setFields(array $fields)
    {
        $this->_fields = $fields;
    }

    /**
     * Filter fields list depending on requested fields in `$_fields`.
     *
     * @param array $fields List of fields.
     * @return array
     */
    protected function filterFields(array $fields)
    {
        if (empty($this->_fields)) {
            return $fields;
        }

        return array_intersect($this->_fields, $fields);
    }

    /**
     * Getter for `attributes`.
     *
     * @return array
     */
    protected function getAttributes()
    {
        $table = $this->getTable();
        $associations = static::listAssociations($table, $this->getHidden());
        $visible = $this->filterFields($this->getVisible());

        $properties = array_filter(
            array_diff($visible, (array)$table->getPrimaryKey(), $associations, ['_joinData', '_matchingData', '_countData']),
            [$this, 'isAccessible']
        );

        return $this->extract($properties);
    }

    /**
     * Getter for `meta`.
     *
     * @return array
     */
    protected function getMeta()
    {
        $table = $this->getTable();
        $associations = static::listAssociations($table, $this->getHidden());
        $visible = $this->filterFields($this->getVisible());
        $virtual = $this->getVirtual();

        $properties = array_filter(
            array_diff($visible, (array)$table->getPrimaryKey(), $associations, ['_joinData', '_matchingData', '_countData']),
            function ($property) {
                return !$this->isAccessible($property);
            }
        );
        $extraProperties = array_filter(
            $properties,
            function ($property) use ($table, $virtual) {
                return !in_array($property, $virtual) && !$table->hasField($property);
            }
        );

        $meta = $this->extract(array_diff($properties, $extraProperties));
        if (!empty($extraProperties)) {
            $meta['extra'] = $this->extract($extraProperties);
        }
        $meta += array_filter(['relation' => $this->joinData()]);

        return $meta;
    }

    /**
     * Retrieve internal `_joinData` to be added as `meta.relation`
     *
     * @return array
     */
    protected function joinData(): array
    {
        $joinData = $this->get('_joinData');
        if (!$joinData instanceof \JsonSerializable) {
            return [];
        }

        if ($joinData instanceof Tree) {
            $joinData->unsetProperty([
                'id',
                'parent_id',
                'object_id',
                'root_id',
                'parent_node_id',
                'tree_left',
                'tree_right',
            ]);
        }

        return (array)$joinData->jsonSerialize();
    }

    /**
     * Get prefix used for `_name` in routing urls creation.
     *
     * @return string
     */
    public function routeNamePrefix()
    {
        return 'api:resources';
    }

    /**
     * Getter for `links`.
     *
     * @return array
     */
    protected function getLinks()
    {
        $self = Router::url(
            [
                '_name' => $this->routeNamePrefix() . ':resource',
                'controller' => Inflector::camelize($this->getType()),
                'id' => $this->getId(),
            ],
            true
        );

        return compact('self');
    }

    /**
     * Get included resources.
     *
     * @param mixed $related Related entities.
     * @return array
     */
    protected function getIncluded($related)
    {
        $data = [];
        if (empty($related)) {
            return $data;
        }

        $single = false;
        if (!is_array($related) || !Hash::numeric(array_keys($related))) {
            $single = true;
            $related = [$related];
        }
        foreach ($related as $item) {
            if (!$item instanceof JsonApiSerializable) {
                throw new \InvalidArgumentException(sprintf(
                    'Objects must implement "%s", got "%s" instead',
                    JsonApiSerializable::class,
                    is_object($item) ? get_class($item) : gettype($item)
                ));
            }

            $data[] = $item->jsonApiSerialize(JsonApiSerializable::JSONAPIOPT_EXCLUDE_ATTRIBUTES | JsonApiSerializable::JSONAPIOPT_EXCLUDE_META | JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS | JsonApiSerializable::JSONAPIOPT_EXCLUDE_RELATIONSHIPS);
        }

        return $single ? $data[0] : $data;
    }

    /**
     * Getter for `relationships`.
     *
     * @return array[]
     */
    protected function getRelationships()
    {
        $relationships = $included = [];

        $associations = static::listAssociations($this->getTable(), $this->getHidden());
        foreach ($associations as $relationship) {
            $self = Router::url(
                [
                    '_name' => $this->routeNamePrefix() . ':relationships',
                    'controller' => Inflector::camelize($this->getType()),
                    'relationship' => $relationship,
                    'id' => $this->getId(),
                ],
                true
            );
            $related = Router::url(
                [
                    '_name' => $this->routeNamePrefix() . ':related',
                    'controller' => Inflector::camelize($this->getType()),
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

            $relationships[$relationship] = [];
            if (isset($data)) {
                $relationships[$relationship] += compact('data');
                unset($data);
            }
            $relationships[$relationship] += [
                'links' => compact('related', 'self'),
            ];

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
     * Get count of relationship searching in self::$relationshipsCount`.
     *
     * @param string $relationship The relationship name
     * @return int|null
     */
    public function getRelationshipCount(string $relationship): ?int
    {
        $count = Hash::get((array)$this->get('_countData'), $relationship);
        if (is_numeric($count)) {
            return $count;
        }

        return null;
    }

    /**
     * List all available relationships for a model.
     *
     * @param \Cake\ORM\Table $Table Table object instance.
     * @param array $hidden List of relationships to be excluded.
     * @return array
     */
    protected static function listAssociations(Table $Table, array $hidden = [])
    {
        $associations = $Table->associations();
        $btmJunctionAliases = array_map(
            function (BelongsToMany $val) {
                return $val->junction()->getAlias();
            },
            $associations->getByType('BelongsToMany')
        );

        $relationships = [];
        foreach ($associations as $association) {
            [, $associationType] = namespaceSplit(get_class($association));
            $name = $association->getProperty();
            if (
                !($association instanceof Association) ||
                in_array($name, $hidden) ||
                ($associationType === 'HasMany' && in_array($association->getTarget()->getAlias(), $btmJunctionAliases))
            ) {
                continue;
            }

            $relationships[] = $name;
        }

        return $relationships;
    }

    /**
     * JSON API serializer.
     *
     * @param int $options Serializer options. Can be any combination of `JSONAPIOPT_*` constants defined in this class.
     * @param array $fields Selected fields to view in `attributes` and `meta`, default empty => all fields are serialized
     * @return array
     */
    public function jsonApiSerialize($options = 0, $fields = [])
    {
        $id = $this->getId();
        $type = $this->getType();
        if (!empty($fields[$type])) {
            $this->setFields($fields[$type]);
        } elseif (!empty($fields['_common'])) {
            $this->setFields($fields['_common']);
        }

        $attributes = $meta = $links = $relationships = $included = null;
        if (($options & JsonApiSerializable::JSONAPIOPT_EXCLUDE_ATTRIBUTES) === 0) {
            $attributes = $this->getAttributes();
        }
        if (($options & JsonApiSerializable::JSONAPIOPT_EXCLUDE_META) === 0) {
            $meta = $this->getMeta();
        }
        if (($options & JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS) === 0) {
            $links = $this->getLinks();
        }
        if (($options & JsonApiSerializable::JSONAPIOPT_EXCLUDE_RELATIONSHIPS) === 0) {
            [$relationships, $included] = $this->getRelationships();
        }

        return array_filter(compact('id', 'type', 'attributes', 'meta', 'links', 'relationships', 'included'));
    }
}

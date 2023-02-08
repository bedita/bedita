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
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use Cake\ORM\Entity;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use Generator;

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
 * @property array $associations
 * @property array $hidden
 * @property string[] $relations
 * @property bool $is_abstract
 * @property int $parent_id
 * @property array $never_translate
 * @property array $always_translate
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
class ObjectType extends Entity implements JsonApiSerializable, EventDispatcherInterface
{
    use EventDispatcherTrait;
    use JsonApiModelTrait {
        listAssociations as protected jsonApiListAssociations;
    }
    use LocatorAwareTrait;

    /**
     * JSON Schema definition of nullable objects array.
     * Basic schema for categories, tags, date_ranges and other properties.
     *
     * @var array
     */
    public const NULLABLE_OBJECT_ARRAY = [
        'oneOf' => [
            [
                'type' => 'null',
            ],
            [
                'type' => 'array',
                'uniqueItems' => true,
                'items' => [
                    'type' => 'object',
                ],
            ],
        ],
    ];

    /**
     * List of associations represented as properties.
     *
     * @var array
     */
    public const ASSOC_PROPERTIES = ['Tags', 'Categories', 'DateRanges'];

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    protected $_virtual = [
        'alias',
        'table',
        'parent_name',
        'relations',
    ];

    /**
     * @inheritDoc
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
    protected function _setName(string $name): string
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
    protected function _getSingular(): ?string
    {
        if (!empty($this->_fields['singular'])) {
            return $this->_fields['singular'];
        }

        return Inflector::singularize((string)$this->name);
    }

    /**
     * Setter for property `singular`.
     *
     * Force `singular` field to be underscored via inflector.
     *
     * @param string|null $singular Object type singular name.
     * @return string
     */
    protected function _setSingular(?string $singular): string
    {
        return Inflector::underscore($singular);
    }

    /**
     * Getter for virtual property `alias`.
     *
     * @return string
     */
    protected function _getAlias(): string
    {
        return Inflector::camelize((string)$this->name);
    }

    /**
     * Getter for virtual property `table`.
     *
     * @return string
     */
    protected function _getTable(): string
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
    protected function _setTable(string $table): void
    {
        [$plugin, $model] = pluginSplit($table);

        $this->plugin = $plugin;
        $this->model = $model;
    }

    /**
     * Get parent object type, if set.
     *
     * @return self|null
     */
    public function getParent(): ?self
    {
        if ($this->parent_id === null) {
            return null;
        }

        if ($this->parent !== null) {
            return $this->parent;
        }

        /** @var \BEdita\Core\Model\Table\ObjectTypesTable $table */
        $table = $this->getTableLocator()->get($this->getSource());

        return $table->get($this->parent_id);
    }

    /**
     * Iterate through full inheritance chain.
     *
     * @return \Generator|self[]
     */
    public function getFullInheritanceChain(): Generator
    {
        $objectType = $this;
        while ($objectType !== null) {
            yield $objectType;

            $objectType = $objectType->getParent();
        }
    }

    /**
     * Get all relations, including relations inherited from parent object types, indexed by their name.
     *
     * @param string $side Filter relations by side this object type stays on. Either `left`, `right` or `both`.
     * @return \BEdita\Core\Model\Entity\Relation[]
     */
    public function getRelations(string $side = 'both'): array
    {
        if ($side === 'both') {
            return $this->getRelations('left') + $this->getRelations('right');
        }

        $indexBy = 'name';
        if ($side === 'right') {
            $indexBy = 'inverse_name';
        }

        $property = sprintf('%s_relations', $side);

        return collection($this->getFullInheritanceChain())
            ->unfold(function (self $objectType) use ($property): Generator {
                yield from (array)$objectType->get($property);
            })
            ->indexBy($indexBy)
            ->toArray();
    }

    /**
     * Getter for virtual property `relations`.
     *
     * @return string[]|null
     */
    protected function _getRelations(): ?array
    {
        if (!$this->has('left_relations') || !$this->has('right_relations')) {
            return null;
        }

        return array_keys($this->getRelations());
    }

    /**
     * @inheritDoc
     */
    protected static function listAssociations(Table $Table, array $hidden = []): array
    {
        $associations = static::jsonApiListAssociations($Table, $hidden);
        $associations = array_diff($associations, ['relations']);

        return $associations;
    }

    /**
     * Getter for virtual property `parent_name`.
     *
     * @return string|null
     */
    protected function _getParentName(): ?string
    {
        $parent = $this->getParent();
        if ($parent === null) {
            return null;
        }

        return $parent->name;
    }

    /**
     * Setter for virtual property `parent_name`.
     *
     * @param string|null $parentName Parent object type name.
     * @return string
     */
    protected function _setParentName(?string $parentName): ?string
    {
        if ($parentName === null) {
            $this->parent = null;
            $this->parent_id = null;

            return null;
        }
        try {
            /** @var \BEdita\Core\Model\Table\ObjectTypesTable $table */
            $table = $this->getTableLocator()->get($this->getSource());
            $objectType = $table->get($parentName);
            if (!$objectType->is_abstract || !$objectType->enabled) {
                return null;
            }

            if ($this->parent_id !== $objectType->id) {
                $this->parent = $objectType;
                $this->parent_id = $objectType->id;
            }
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
        if ($this->is_abstract || empty($this->id) || $this->enabled === false) {
            return false;
        }

        $associations = (array)$this->associations;
        $relations = static::objectTypeRelations($this->getRelations('right'), 'right') +
            static::objectTypeRelations($this->getRelations('left'), 'left');

        $schema = $this->objectTypeProperties() + compact('associations', 'relations');
        $event = $this->dispatchEvent('ObjectType.getSchema', ['schema' => $schema, 'objectType' => $this], $this);
        if ($event->isStopped()) {
            return false;
        }

        return $event->getResult() ?: $schema;
    }

    /**
     * Retrieve relation information as associative array:
     *  * relation name as key, direct or inverse
     *  * label, params and related types as values
     *
     * @param array $relations Relations array
     * @param string $side Relation side, 'left' or 'right'
     * @return array
     */
    protected static function objectTypeRelations(array $relations, string $side): array
    {
        if ($side === 'left') {
            $name = 'name';
            $label = 'label';
            $relTypes = 'right_object_types';
        } else {
            $name = 'inverse_name';
            $label = 'inverse_label';
            $relTypes = 'left_object_types';
        }

        $res = [];
        foreach ($relations as $relation) {
            $types = array_map(
                function ($t) {
                    return $t->get('name');
                },
                (array)$relation->get($relTypes)
            );
            sort($types);
            $res[$relation->get($name)] = [
                'label' => $relation->get($label),
                'params' => $relation->params,
            ] + compact('types');
        }

        return $res;
    }

    /**
     * Return object type properties in JSON SCHEMA format
     *
     * @return array
     */
    protected function objectTypeProperties(): array
    {
        /** @var \BEdita\Core\Model\Entity\Property[] $allProperties */
        // Fetch all properties, properties with `is_static` true at the end.
        // This way we can override default property type of a static property.
        $allProperties = $this->getTableLocator()->get('Properties')
            ->find('objectType', [$this->id])
            ->order(['is_static' => 'ASC'])
            ->toArray();
        $entity = $this->getTableLocator()->get($this->name)->newEmptyEntity();

        $required = $translatable = [];
        $properties = $this->associationProperties();
        foreach ($allProperties as $property) {
            if (in_array($property->name, (array)$this->hidden)) {
                continue;
            }
            $accessMode = $this->accessMode($property, $entity);
            $properties[$property->name] = $property->getSchema($accessMode);

            if ($property->required && $accessMode === null) {
                $required[] = $property->name;
            }
            if ($this->translatableProperty($property)) {
                $translatable[] = $property->name;
            }
        }

        return compact('properties', 'required', 'translatable');
    }

    /**
     * See if a property is translatable looking at property type (static or dynamic)
     * and using `always_translate` and `never_translate` attributes.
     *
     * @param \BEdita\Core\Model\Entity\Property $property The property
     * @return bool
     */
    protected function translatableProperty(Property $property): bool
    {
        if (in_array($property->name, (array)$this->always_translate)) {
            return true;
        }
        $excludeDefault = ['uname', 'status', 'lang', 'custom_props', 'extra'];
        $excluded = array_merge((array)$this->never_translate, $excludeDefault);
        $translatable = $property->translatable;

        return $translatable && !in_array($property->name, $excluded);
    }

    /**
     * Get property access mode: can be `readOnly`, `writeOnly` or null if no access mode is set
     *
     * @param \BEdita\Core\Model\Entity\Property $property The property
     * @param \Cake\Datasource\EntityInterface $entity Default object entity
     * @return string|null
     */
    protected function accessMode(Property $property, EntityInterface $entity): ?string
    {
        if (!$entity->isAccessible($property->name)) {
            return 'readOnly';
        } elseif (in_array($property->name, $entity->getHidden())) {
            return 'writeOnly';
        }

        return null;
    }

    /**
     * Add internal associations as properties
     *
     * @return array
     */
    protected function associationProperties(): array
    {
        $assocProps = array_intersect(static::ASSOC_PROPERTIES, (array)$this->associations);
        $res = [];
        foreach ($assocProps as $assoc) {
            $name = Inflector::delimit($assoc);
            $res[$name] = self::NULLABLE_OBJECT_ARRAY + [
                '$id' => sprintf('/properties/%s', $name),
                'title' => $assoc,
            ];
        }

        return $res;
    }

    /** Check if an object type is child of another object type.
     *
     * @param \BEdita\Core\Model\Entity\ObjectType $ancestor Ancestor object type to test.
     * @return bool
     */
    public function isDescendantOf(self $ancestor): bool
    {
        foreach ($this->getFullInheritanceChain() as $objectType) {
            if ((int)$objectType->id === (int)$ancestor->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the closest common parent object type for a set of object types.
     *
     * @param \BEdita\Core\Model\Entity\ObjectType ...$objectTypes Object types to find common ancestor for.
     * @return static|null
     */
    public static function getClosestCommonAncestor(self ...$objectTypes): ?self
    {
        if (empty($objectTypes)) {
            return null;
        }

        $parent = array_shift($objectTypes);
        foreach ($parent->getFullInheritanceChain() as $commonAncestor) {
            $isCommonAncestor = array_reduce(
                $objectTypes,
                function (bool $store, ObjectType $item) use ($commonAncestor): bool {
                    return $store && $item->isDescendantOf($commonAncestor);
                },
                true
            );
            if ($isCommonAncestor) {
                return $commonAncestor;
            }
        }

        return null;
    }
}

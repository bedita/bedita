<?php
declare(strict_types=1);

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

namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\ORM\Inheritance\Table as InheritanceTable;
use BEdita\Core\Search\BaseAdapter;
use BEdita\Core\Search\SearchRegistry;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\Table;

/**
 * Behavior to add text-based search to model.
 *
 * @since 4.0.0
 */
class SearchableBehavior extends Behavior
{
    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'minLength' => 3,
        'maxWords' => 10,
        'columnTypes' => [
            'string',
            'text',
        ],
        'fields' => [
            '*' => 1,
        ],
        'implementedFinders' => [
            'query' => 'findQuery',
        ],
    ];

    /**
     * The Search adapters registry.
     *
     * @var \BEdita\Core\Search\SearchRegistry
     */
    protected static $searchRegistry = null;

    /**
     * {@inheritDoc}
     *
     * If fields or column types are specified - do *not* merge them with existing config,
     * overwrite the fields to search on.
     */
    public function initialize(array $config): void
    {
        foreach (['columnTypes', 'fields'] as $key) {
            if (isset($config[$key])) {
                $this->setConfig($key, $config[$key], false);
            }
        }
    }

    /**
     * Update search adapters index when a resource is saved.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cake\Datasource\EntityInterface $entity The resource entity
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity): void
    {
        foreach (array_keys((array)Configure::read('Search.adapters')) as $name) {
            $this->getAdapter($name)->indexResource($entity, 'edit');
        }
    }

    /**
     * Update search adapters index when a resource is deleted.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cake\Datasource\EntityInterface $entity The resource entity
     * @return void
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity): void
    {
        foreach (array_keys((array)Configure::read('Search.adapters')) as $name) {
            $this->getAdapter($name)->indexResource($entity, 'delete');
        }
    }

    /**
     * Get search adapters registry.
     *
     * @return \BEdita\Core\Search\SearchRegistry
     */
    protected static function getSearchRegistry(): SearchRegistry
    {
        if (!isset(static::$searchRegistry)) {
            static::$searchRegistry = new SearchRegistry();
        }

        return static::$searchRegistry;
    }

    /**
     * Get an adapter by name.
     *
     * @param string $name The adapter name
     * @return \BEdita\Core\Search\BaseAdapter
     */
    protected function getAdapter($name): BaseAdapter
    {
        $searchRegistry = static::getSearchRegistry();
        if ($searchRegistry->has($name)) {
            return $searchRegistry->get($name);
        }

        $conf = (array)Configure::read(sprintf('Search.adapters.%s', $name));

        return $searchRegistry->load($name, $conf);
    }

    /**
     * Get all fields whose column type is amongst those allowed in `columnTypes` configuration key.
     *
     * @param \Cake\ORM\Table $table Table object.
     * @return string[]
     */
    protected function getAllFields(Table $table)
    {
        $columnTypes = $this->getConfig('columnTypes');
        $fields = array_filter( // Filter fields that are of a searchable type.
            $table->getSchema()->columns(),
            function ($column) use ($columnTypes, $table) {
                return in_array($table->getSchema()->getColumnType($column), $columnTypes);
            }
        );

        if ($table instanceof InheritanceTable && $table->inheritedTable() !== null) {
            // If table inherits from another table, merge parent table's fields.
            $fields = array_merge($fields, $this->getAllFields($table->inheritedTable()));
        }

        return $fields;
    }

    /**
     * Get searchable fields and their priorities.
     *
     * @return array Array where keys are columns, and values are priorities.
     */
    public function getFields()
    {
        $wildCard = $this->getConfig('fields.*');

        $fields = (array)$this->getConfig('fields');
        $allFields = $this->getAllFields($this->table());

        $fields = array_intersect_key($fields, array_flip($allFields));
        if ($wildCard !== null) {
            // If wildcard `*` is present, all other fields have default priority.
            $fields += array_diff_key(
                array_fill_keys($allFields, $wildCard),
                $fields
            );
        }

        return $fields;
    }

    /**
     * Finder for query search.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options.
     * @return \Cake\ORM\Query
     */
    public function findQuery(Query $query, array $options)
    {
        $options += [
            'exact' => false,
        ];

        $text = $options['string'] ?? $options[0] ?? null;
        if (!isset($text) || !is_string($text)) {
            // Bad filter options.
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'query filter requires a non-empty query string',
            ]);
        }

        unset($options[0], $options['string']);

        $config = [
            'minLength' => $this->getConfig('minLength'),
            'maxWords' => $this->getConfig('maxWords'),
            'fields' => $this->getFields(),
        ];

        return $this->getAdapter(Configure::read('Search.use', 'default'))
            ->search($query, $text, $options, $config);
    }
}

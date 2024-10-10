<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Model\Behavior;

use ArrayObject;
use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Search\Adapter\SimpleAdapter;
use BEdita\Core\Search\BaseAdapter;
use BEdita\Core\Search\SearchRegistry;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * Behavior to add text-based search to model.
 *
 * @since 4.0.0
 */
class SearchableBehavior extends Behavior
{
    /**
     * {@inheritDoc}
     *
     * Deprecated configuration keys:
     * - 'minLength' => 3,
     * - 'maxWords' => 10,
     * - 'columnTypes' => ['string', 'text'],
     * - 'fields' => ['*' => 1]
     *
     * if present they are used in `SimpleAdapter` for backward compatibility.
     */
    protected $_defaultConfig = [
        'operationName' => [
            'Model.afterSave' => 'edit',
            'Model.afterDelete' => 'delete',
        ],
        'scopes' => [],
        'implementedFinders' => [
            'query' => 'findQuery',
        ],
    ];

    /**
     * The Search adapters registry instance.
     *
     * @var \BEdita\Core\Search\SearchRegistry
     */
    protected $searchRegistry = null;

    /**
     * Get operation name for the entity being saved or deleted.
     *
     * @param \Cake\Event\EventInterface $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Resource entity.
     * @return string|null
     */
    protected function getOperation(EventInterface $event, EntityInterface $entity): ?string
    {
        $operations = (array)$this->getConfig('operationName');
        $operationName = $operations[$event->getName()] ?? null;

        if (is_callable($operationName)) {
            $operationName = $operationName($event, $entity);
        }

        if ($operationName !== null && !is_string($operationName)) {
            throw new \UnexpectedValueException(
                sprintf('Operation name must be string or null, got %s', gettype($operationName))
            );
        }

        return $operationName;
    }

    /**
     * Update search adapters index when a resource is saved or deleted.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cake\Datasource\EntityInterface $entity The resource entity
     * @return void
     */
    protected function indexEntity(EventInterface $event, EntityInterface $entity): void
    {
        $operation = $this->getOperation($event, $entity);
        if ($operation === null) {
            return;
        }

        foreach ($this->getSearchAdapters() as $adapter) {
            $adapter->indexResource($entity, $operation);
        }
    }

    /**
     * Update search adapters index when a resource is saved or deleted.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cake\Datasource\EntityInterface $entity The resource entity
     * @param \ArrayObject $options Save options.
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        if (empty($options['_primary']) || !empty($options['_skipSearchIndex'])) {
            // Do not reindex non-primary saved entities, as they will probably be incomplete, nor entities for which
            // skipping reindex is explicitly requested.
            return;
        }

        $this->indexEntity($event, $entity);
    }

    /**
     * Update search adapters index when a resource is saved or deleted.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cake\Datasource\EntityInterface $entity The resource entity
     * @return void
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity): void
    {
        $this->indexEntity($event, $entity);
    }

    /**
     * Get iterable of adapters.
     * The keys are the adapter name and the values are the adapters instances.
     *
     * @return iterable<string, \BEdita\Core\Search\BaseAdapter>
     */
    public function getSearchAdapters(): iterable
    {
        $scopes = (array)$this->getConfig('scopes');
        foreach ((array)Configure::read('Search.adapters') as $name => $config) {
            if (!empty($scopes) && !empty($config['scopes']) && !array_intersect($scopes, $config['scopes'])) {
                continue;
            }

            yield (string)$name => $this->getAdapter((string)$name);
        }
    }

    /**
     * Get search adapters registry.
     *
     * @return \BEdita\Core\Search\SearchRegistry
     */
    protected function getSearchRegistry(): SearchRegistry
    {
        if (!isset($this->searchRegistry)) {
            $this->searchRegistry = new SearchRegistry();
        }

        return $this->searchRegistry;
    }

    /**
     * Get an adapter by name.
     *
     * @param string $name The adapter name
     * @return \BEdita\Core\Search\BaseAdapter
     */
    protected function getAdapter(?string $name = null): BaseAdapter
    {
        $name ??= (string)Configure::read('Search.use', 'default');
        $searchRegistry = $this->getSearchRegistry();
        if ($searchRegistry->has($name)) {
            return $searchRegistry->get($name);
        }

        $adapter = $searchRegistry->load($name, (array)Configure::read(sprintf('Search.adapters.%s', $name)));
        $this->table()->dispatchEvent('SearchAdapter.initialize', [$this->table()], $adapter);

        // backward compatibility
        if ($adapter instanceof SimpleAdapter) {
            $this->fitSimpleAdapterConf($adapter);
        }

        return $adapter;
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

        return $this->getAdapter()->search($query, $text, $options);
    }

    /**
     * Fit configuration of `SimpleAdapter` to maintain backward compatibility with 5.x.
     *
     * @param \BEdita\Core\Search\Adapter\SimpleAdapter $adapter The adapter.
     * @return void
     */
    protected function fitSimpleAdapterConf(SimpleAdapter $adapter): void
    {
        $config = array_intersect_key(
            $this->getConfig(),
            array_flip(['minLength', 'maxWords'])
        );
        $adapter->setConfig($config);

        // Config keys that must be overridden
        foreach (['columnTypes', 'fields'] as $key) {
            $conf = $this->getConfig($key);
            if (!is_array($conf)) {
                continue;
            }

            // `fields` key in SimpleAdapter is changed.
            // It is now a list of fields without unused priority.
            if ($key === 'fields') {
                deprecationWarning('"fields" must be a list of strings. Unused priorities have been removed.');
                $conf = array_keys($conf);
            }

            $adapter->setConfig($key, $conf, false);
        }
    }
}

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
namespace BEdita\Core\Search;

use Cake\Core\InstanceConfigTrait;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * Abstract search adapter.
 *
 * @since 5.14.0
 */
abstract class BaseAdapter
{
    use InstanceConfigTrait;

    /**
     * Default configuration for adapter.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'allowedOperations' => ['edit', 'delete'],
    ];

    /**
     * Initialize adapter with configuration.
     *
     * @param array $config Adapter configuration
     * @return bool Success or failure
     */
    public function initialize(array $config): bool
    {
        $this->setConfig($config);

        return true;
    }

    /**
     * Build `$query` used for search.
     *
     * @param \Cake\ORM\Query $query The query instance
     * @param string $text The text to look for
     * @param array $options Options
     * @return \Cake\ORM\Query
     */
    abstract public function search(Query $query, string $text, array $options = []): Query;

    /**
     * Index a resource by `$operation`.
     * Every adapter that needs an index should implement this method.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to index
     * @param string $operation The operation
     * @return void
     */
    public function indexResource(EntityInterface $entity, string $operation): void
    {
        if (!in_array($operation, (array)$this->getConfig('allowedOperations'))) {
            throw new \InvalidArgumentException(sprintf('Invalid operation "%s" indexing resource', $operation));
        }
    }
}

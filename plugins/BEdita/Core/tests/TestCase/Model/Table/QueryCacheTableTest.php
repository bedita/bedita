<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\QueryCacheTable;
use Cake\Cache\Cache;
use Cake\Datasource\ModelAwareTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\QueryCacheTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\QueryCacheTable
 */
class QueryCacheTableTest extends TestCase
{
    use ModelAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Config',
    ];

    /**
     * Test `afterDelete` method
     *
     * @return void
     *
     * @covers ::afterDelete()
     */
    public function testAfterDelete(): void
    {
        $this->loadModel('Config');
        $config = $this->Config->fetchConfig(null, null)->toArray();
        $read = Cache::read('config_*_*', QueryCacheTable::CACHE_CONFIG);
        static::assertNotEmpty($read);

        $config = $this->Config->get(1);
        $this->Config->deleteOrFail($config);

        $read = Cache::read('config_*_*', QueryCacheTable::CACHE_CONFIG);
        static::assertFalse($read);
    }

    /**
     * Test `afterSave` method
     *
     * @return void
     *
     * @covers ::afterSave()
     */
    public function testAfterSave(): void
    {
        $this->loadModel('Config');
        $config = $this->Config->fetchConfig(null, null)->toArray();
        $read = Cache::read('config_*_*', QueryCacheTable::CACHE_CONFIG);
        static::assertNotEmpty($read);

        $config = $this->Config->get(1);
        $config->content = 'new content';
        $this->Config->saveOrFail($config);

        $read = Cache::read('config_*_*', QueryCacheTable::CACHE_CONFIG);
        static::assertFalse($read);
    }
}

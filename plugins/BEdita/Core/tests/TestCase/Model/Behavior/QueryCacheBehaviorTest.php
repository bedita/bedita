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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use Cake\Cache\Cache;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\QueryCacheBehavior} Test Case
 *
 * @property \BEdita\Core\Model\Table\ConfigTable $Config
 * @coversDefaultClass \BEdita\Core\Model\Behavior\QueryCacheBehavior
 */
#[\AllowDynamicProperties]
class QueryCacheBehaviorTest extends TestCase
{
    use LocatorAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Config',
    ];

    /**
     * Test `afterDelete` method
     *
     * @return void
     * @covers ::afterDelete()
     * @covers ::queryCache()
     */
    public function testAfterDelete(): void
    {
        $this->Config = $this->fetchTable('Config');
        $this->Config->fetchConfig(null, null)->toArray();
        $cacheConf = $this->Config->behaviors()->get('QueryCache')->getConfig('cacheConfig');
        $read = Cache::read('config_any_any', $cacheConf);
        static::assertNotEmpty($read);

        $config = $this->Config->get(1);
        $this->Config->deleteOrFail($config);

        $read = Cache::read('config_any_any', $cacheConf);
        static::assertNull($read);
    }

    /**
     * Test `afterSave` method
     *
     * @return void
     * @covers ::afterSave()
     */
    public function testAfterSave(): void
    {
        $this->Config = $this->fetchTable('Config');
        $this->Config->fetchConfig(null, null)->toArray();
        $behavior = $this->Config->behaviors()->get('QueryCache');
        $read = Cache::read('config_any_any', $behavior->getConfig('cacheConfig'));
        static::assertNotEmpty($read);

        $config = $this->Config->get(1);
        $config->content = 'new content';
        $this->Config->saveOrFail($config);

        $read = Cache::read('config_any_any', $behavior->getConfig('cacheConfig'));
        static::assertNull($read);
    }
}

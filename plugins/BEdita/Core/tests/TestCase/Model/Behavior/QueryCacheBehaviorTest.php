<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Behavior\QueryCacheBehavior;
use BEdita\Core\Model\Table\ConfigTable;
use Cake\Cache\Cache;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\Core\Model\Behavior\QueryCacheBehavior} Test Case
 */
#[CoversClass(QueryCacheBehavior::class)]
class QueryCacheBehaviorTest extends TestCase
{
    use LocatorAwareTrait;

    /**
     * ConfigTable instance.
     *
     * @var \BEdita\Core\Model\Table\ConfigTable
     */
    protected ConfigTable $Config;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Config',
    ];

    /**
     * Test `afterDelete` method
     *
     * @return void
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

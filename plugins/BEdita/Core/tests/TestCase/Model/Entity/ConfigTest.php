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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Config} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Config
 */
class ConfigTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\ConfigTable
     */
    public $Config;

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
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->Config = TableRegistry::getTableLocator()->get('Config');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Config);

        parent::tearDown();
    }

    /**
     * Test entity
     *
     * @return void
     * @coversNothing
     */
    public function testEntity()
    {
        $config = $this->Config->findByName('Name2')->firstOrFail();
        $this->assertEquals('group1', $config->context);
        $this->assertEquals('true', $config->content);

        $data = [
            'content' => 'true',
        ];
        $config = $this->Config->patchEntity($config, $data);
        $this->assertEquals('true', $config->content);

        $config = $this->Config->findByName('Key2')->firstOrFail();
        $this->assertEquals('group1', $config->context);
        $config->content = json_decode($config->content, true);
        $this->assertEquals('some data', $config->content['test1']);

        $config = $this->Config->findByName('IntVal')->firstOrFail();
        $this->assertEquals('group2', $config->context);
        $this->assertEquals('14', $config->content);
    }

    /**
     * Test `_setApplication` method
     *
     * @return void
     * @covers ::_setApplication()
     */
    public function testSetApplication()
    {
        $config = $this->Config->findByName('Name2')->firstOrFail();
        $config->set('application', null);
        static::assertNull($config->get('application_id'));

        $config->set('application', 'First app');
        static::assertEquals(1, $config->get('application_id'));
    }
}

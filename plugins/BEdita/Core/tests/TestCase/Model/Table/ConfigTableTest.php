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

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Configure\Engine\DatabaseConfig;
use BEdita\Core\State\CurrentApplication;
use Cake\Cache\Cache;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Table\ConfigTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\ConfigTable
 */
class ConfigTableTest extends TestCase
{

    /**
     * Test subject
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
     * Test initialization.
     *
     * @return void
     * @coversNothing
     */
    public function testInitialization()
    {
        $this->Config->initialize([]);
        $this->assertEquals('config', $this->Config->getTable());
        $this->assertEquals('id', $this->Config->getPrimaryKey());
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'valid' => [
                true,
                [
                    'name' => 'KeyName',
                    'context' => 'Group1',
                    'content' => 'null'
                ],
            ],
            'notValid' => [
                false,
                [
                    'name' => 'missingContent',
                    'context' => '',
                ],
            ],
            'notValid2' => [
                false,
                [
                    'name' => 'bad.key',
                    'context' => 'somecontext',
                    'content' => 'some content'
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     *
     * @return void
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $config = $this->Config->newEntity($data);

        $error = (bool)$config->getErrors();
        $this->assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->Config->save($config);
            $this->assertTrue((bool)$success);
        }
    }

    /**
     * Test `mine` finder
     *
     * @return void
     * @covers ::findMine()
     */
    public function testFindMine()
    {
        $config = $this->Config->find('mine')->toArray();
        $names = Hash::extract($config, '{n}.name');
        // `appVal` must not be present
        static::assertFalse(in_array('appVal', $names));

        CurrentApplication::setApplication(TableRegistry::getTableLocator()->get('Applications')->get(1));

        $config = $this->Config->find('mine')->toArray();
        $names = Hash::extract($config, '{n}.name');
        // `appVal` must be present
        static::assertTrue(in_array('appVal', $names));
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function findNameProvider()
    {
        return [
            'simple' => [
                1,
                [
                    'name' => 'appVal',
                ],
            ],
            'app' => [
                1,
                [
                    'name' => 'appVal',
                    'application_id' => 1,
                ],
            ],
            'app name' => [
                1,
                [
                    'name' => 'appVal',
                    'application' => 'First app',
                ],
            ],
            'not found' => [
                0,
                [
                    'name' => 'appVal',
                    'application' => 'New app',
                ],
            ],
            'none' => [
                0,
                [
                    'name' => 'KeyName',
                ],
            ],
            'bad' => [
                new BadRequestException('Missing mandatory option "name"'),
                [
                    'gustavo' => 'KeyName',
                ],
            ],
        ];
    }

    /**
     * Test `name` finder
     *
     * @dataProvider findNameProvider
     * @covers ::findName()
     *
     * @param int|\Exception $expected Result number or Exception.
     * @param array $data Find options.
     * @return void
     */
    public function testFindName($expected, array $data)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $config = $this->Config->find('name', $data)->toArray();
        static::assertEquals($expected, count($config));
    }

    /**
     * Test `afterDelete` method
     *
     * @return void
     *
     * @covers ::afterDelete()
     */
    public function testAfterDelete(): void
    {
        $configData = (new DatabaseConfig())->read(null);
        $read = Cache::read('db_conf__0', DatabaseConfig::CACHE_CONFIG);
        static::assertNotEmpty($read);

        $config = $this->Config->get(1);
        $this->Config->deleteOrFail($config);

        $read = Cache::read('db_conf__0', DatabaseConfig::CACHE_CONFIG);
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
        $configData = (new DatabaseConfig())->read(null);
        $read = Cache::read('db_conf__0', DatabaseConfig::CACHE_CONFIG);
        static::assertNotEmpty($read);

        $config = $this->Config->get(1);
        $config->content = 'new content';
        $this->Config->saveOrFail($config);

        $read = Cache::read('db_conf__0', DatabaseConfig::CACHE_CONFIG);
        static::assertFalse($read);
    }

    /**
     *
     * @return void
     */
    public function testFetchConfig(): void
    {

    }
}

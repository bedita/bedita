<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\AppConfigTable;
use BEdita\Core\State\CurrentApplication;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\AppConfigTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\AppConfigTable
 * @since 5.0.0
 */
class AppConfigTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\AppConfigTable
     */
    public $AppConfig;

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
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->AppConfig = $this->fetchTable('AppConfig');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->AppConfig);
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
        $this->AppConfig->initialize([]);
        $this->assertEquals('config', $this->AppConfig->getTable());
        $this->assertEquals('id', $this->AppConfig->getPrimaryKey());
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
                    'content' => 'null',
                ],
            ],
            'notValid' => [
                false,
                [
                    'name' => 'missingContent',
                    'context' => '',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     * @return void
     * @dataProvider validationProvider
     * @covers ::validationDefault()
     */
    public function testValidation($expected, array $data): void
    {
        $config = $this->AppConfig->newEntity($data);

        $error = (bool)$config->getErrors();
        $this->assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->AppConfig->save($config);
            $this->assertTrue((bool)$success);
        }
    }

    /**
     * Test `all` finder
     *
     * @return void
     * @covers ::findAll()
     */
    public function testFindAll(): void
    {
        $app = CurrentApplication::getApplication();

        CurrentApplication::setApplication($this->fetchTable('Applications')->get(1));
        $config = $this->AppConfig->find('all')->toArray();
        static::assertNotEmpty($config);
        static::assertEquals(1, count($config));
        static::assertEquals('appVal', $config[0]->get('name'));

        CurrentApplication::setApplication($app);
    }

    /**
     * Test `newEmptyEntity` method
     *
     * @return void
     * @covers ::newEmptyEntity()
     */
    public function testNewEmptyEntity(): void
    {
        $config = $this->AppConfig->newEmptyEntity();
        static::assertEquals(AppConfigTable::DEFAULT_CONTEXT, $config->get('context'));
        static::assertEquals(CurrentApplication::getApplicationId(), $config->get('application_id'));
    }
}

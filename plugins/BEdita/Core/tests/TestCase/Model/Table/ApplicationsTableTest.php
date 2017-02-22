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

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\ApplicationsTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\ApplicationsTable
 */
class ApplicationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ApplicationsTable
     */
    public $Applications;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.applications',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Applications = TableRegistry::get('Applications');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Applications);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->Applications->initialize([]);
        $this->assertEquals('applications', $this->Applications->getTable());
        $this->assertEquals('id', $this->Applications->getPrimaryKey());
        $this->assertEquals('name', $this->Applications->getDisplayField());

        $this->assertInstanceOf('\Cake\ORM\Behavior\TimestampBehavior', $this->Applications->behaviors()->get('Timestamp'));
        $this->assertInstanceOf('\Cake\ORM\Association\hasMany', $this->Applications->EndpointPermissions);
        $this->assertInstanceOf('\BEdita\Core\Model\Table\EndpointPermissionsTable', $this->Applications->EndpointPermissions->getTarget());
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
                    'name' => 'Unique Application Name',
                    'description' => 'app description'
                ],
            ],
            'notUniqueName' => [
                false,
                [
                    'name' => 'First app',
                    'description' => 'app description'
                ],
            ],
            'missingName' => [
                false,
                [
                    'description' => 'Where is app name?',
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
        $application = $this->Applications->newEntity($data);
        $error = (bool)$application->errors();
        $this->assertEquals($expected, !$error);
        if ($expected) {
            $success = $this->Applications->save($application);
            $this->assertTrue((bool)$success);
        }
    }

    /**
     * Data provider for `testApiKey` test case.
     *
     * @return array
     */
    public function apiKeyGenerationProvider()
    {
        return [
            'new' => [
                null,
                false,
            ],
            'newAndSet' => [
                '123abc',
                false,
            ],
            'updateNoApiKey' => [
                null,
                true,
            ],
            'updateApiKey' => [
                '456dfg',
                true,
            ],
        ];
    }

    /**
     * Test api key generation.
     *
     * @param string $apiKey The api key to set. Empty to leave unchanged on update or auto generation on create
     * @param bool $update If the operation is an update or create
     *
     * @return void
     * @dataProvider apiKeyGenerationProvider
     */
    public function testApiKeyGeneration($apiKey, $update)
    {
        if ($update) {
            $application = $this->Applications->get(1);
        } else {
            $application = $this->Applications->newEntity([
                'name' => 'Second App',
                'description' => 'app description'
            ]);
        }

        if ($apiKey) {
            $application->api_key = $apiKey;
        }

        $success = $this->Applications->save($application);
        $this->assertTrue((bool)$success);

        $testApp = $this->Applications->get($application->id);
        if ($update) {
            if ($apiKey) {
                $this->assertEquals($apiKey, $testApp->api_key);
            } else {
                $this->assertEquals($application->api_key, $testApp->api_key);
            }
        } else {
            if ($apiKey) {
                $this->assertEquals($apiKey, $testApp->api_key);
            } else {
                $this->assertNotEmpty($testApp->api_key);
                // check sha1
                $this->assertTrue(ctype_xdigit($testApp->api_key));
                $this->assertEquals(40, strlen($testApp->api_key));
            }
        }
    }

    /**
     * Data provider for `testFindApiKey` test case.
     *
     * @return array
     */
    public function findApiKeyProvider()
    {
        return [
            'found' => [
                1,
                API_KEY,
            ],
            'disabled' => [
                0,
                'abcdef12345',
            ],
            'invalid' => [
                0,
                'invalid',
            ],
            'badMethodException' => [
                new \BadMethodCallException('Required option "apiKey" must be a not empty string'),
                ['this', 'is', 'not', 'a', 'string'],
            ],
        ];
    }

    /**
     * Test finder by API key.
     *
     * @param int|\Exception $expected Expected count.
     * @param string $apiKey API key.
     * @return void
     *
     * @covers ::findApiKey()
     * @dataProvider findApiKeyProvider()
     */
    public function testFindApiKey($expected, $apiKey)
    {
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        $count = $this->Applications->find('apiKey', compact('apiKey'))->count();

        static::assertSame($expected, $count);
    }
}

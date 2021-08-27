<?php
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

namespace BEdita\Core\Test\TestCase\State;

use BEdita\Core\State\CurrentApplication;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\State\CurrentApplication} Test Case
 *
 * @coversDefaultClass \BEdita\Core\State\CurrentApplication
 */
class CurrentApplicationTest extends TestCase
{

    /**
     * Test subject's table
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
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Config',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Applications = TableRegistry::getTableLocator()->get('Applications');
        Cache::clear(false, '_bedita_core_');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Applications);

        parent::tearDown();
    }

    /**
     * Test `getApplication` method.
     *
     * @return void
     *
     * @covers ::get()
     * @covers ::getApplication()
     */
    public function testGetApplication()
    {
        static::assertNull(null, CurrentApplication::getApplication());

        $application = $this->Applications->get(1);
        CurrentApplication::setApplication($application);

        static::assertSame($application, CurrentApplication::getApplication());
    }

    /**
     * Test `getApplicationId` method.
     *
     * @return void
     *
     * @covers ::id()
     * @covers ::getApplicationId()
     */
    public function testGetApplicationId()
    {
        static::assertNull(null, CurrentApplication::getApplicationId());

        $application = $this->Applications->get(1);
        CurrentApplication::setApplication($application);

        static::assertSame(1, CurrentApplication::getApplicationId());
    }

    /**
     * Test `setApplication` method.
     *
     * @return void
     *
     * @covers ::set()
     * @covers ::setApplication()
     */
    public function testSetApplication()
    {
        $application = $this->Applications->get(1);
        CurrentApplication::setApplication($application);

        static::assertAttributeSame($application, 'application', CurrentApplication::getInstance());
    }

    /**
     * Test `setFromApiKey` method.
     *
     * @return void
     *
     * @covers ::setFromApiKey()
     */
    public function testSetFromApiKey()
    {
        $apiKey = $this->Applications->get(1)->get('api_key');
        CurrentApplication::setFromApiKey($apiKey);

        $application = CurrentApplication::getApplication();
        static::assertNotNull($application);
        static::assertEquals(1, $application->id);
    }

    /**
     * Test `loadConfiguration` method.
     *
     * @return void
     *
     * @covers ::loadConfiguration()
     */
    public function testLoadConfiguration()
    {
        static::assertNull(Configure::read('appVal'));

        $application = $this->Applications->get(1);
        CurrentApplication::setApplication($application);

        $result = Configure::read('appVal');
        $expected = ['val' => 42];
        static::assertEquals($expected, $result);
    }

    /**
     * Test `loadApplicationConfiguration` method.
     *
     * @return void
     *
     * @covers ::loadApplicationConfiguration()
     */
    public function testLoadApplicationConfiguration()
    {
        $application = $this->Applications->get(1);
        CurrentApplication::setApplication($application);

        static::assertNull(Configure::read('someVal'));

        CurrentApplication::loadApplicationConfiguration('somecontext');

        static::assertEquals(42, Configure::read('someVal'));
    }

    /**
     * Test `setFromApiKey` method with invalid API key.
     *
     * @return void
     *
     * @covers ::setFromApiKey()
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testSetFromApiKeyFailure()
    {
        CurrentApplication::setFromApiKey('INVALID_API_KEY');
    }

    /**
     * Data provider for `testSetFromRequest` test case.
     *
     * @return array
     */
    public function setFromRequestProvider()
    {
        return [
            'standard' => [
                1,
                [
                    'HTTP_X_API_KEY' => API_KEY,
                ],
            ],
            'invalid API key' => [
                new ForbiddenException('Invalid API key'),
                [
                    'HTTP_X_API_KEY' => 'this API key is invalid!',
                ],
            ],
            'missing API key' => [
                new ForbiddenException('Missing API key'),
                [],
                [],
                true,
            ],
            'anonymous application' => [
                null,
                [],
            ],
            'query string api key' => [
                1,
                [],
                [
                    'api_key' => API_KEY,
                ],
            ],
            'query string failure' => [
                new ForbiddenException('Invalid API key'),
                [],
                [
                    'api_key' => 'this API key is invalid!',
                ]
            ],
        ];
    }

    /**
     * Test getting application from request headers.
     *
     * @param int|\Exception $expected Expected application ID.
     * @param array $environment Request headers.
     * @param array $query Request query strings.
     * @param bool $blockAnonymous Block anonymous apps flag.
     * @return void
     *
     * @dataProvider setFromRequestProvider()
     * @covers ::setFromRequest()
     */
    public function testSetFromRequest($expected, array $environment, array $query = [], $blockAnonymous = false)
    {
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        Configure::write('Security.blockAnonymousApps', $blockAnonymous);
        CurrentApplication::getInstance()->set(null);
        $environment += ['HTTP_ACCEPT' => 'application/json'];
        $request = new ServerRequest(compact('environment', 'query'));

        CurrentApplication::setFromRequest($request);

        static::assertEquals($expected, CurrentApplication::getApplicationId());
    }

    /**
     * Test default behavior on missing 'Security.blockAnonymousApps' key
     *
     * @return void
     * @coversNothing
     */
    public function testSetFromRequestDefault()
    {
        static::expectException(ForbiddenException::class);
        static::expectExceptionMessage('Missing API key');

        Configure::delete('Security.blockAnonymousApps');
        CurrentApplication::getInstance()->set(null);
        $environment = ['HTTP_ACCEPT' => 'application/json'];
        $request = new ServerRequest(compact('environment'));

        CurrentApplication::setFromRequest($request);
    }
}

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
        'plugin.BEdita/Core.applications',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Applications = TableRegistry::get('Applications');
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
        $this->assertNull(null, CurrentApplication::getApplication());

        $application = $this->Applications->get(1);
        CurrentApplication::setApplication($application);

        $this->assertSame($application, CurrentApplication::getApplication());
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

        $this->assertAttributeSame($application, 'application', CurrentApplication::getInstance());
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
        $this->assertNotNull($application);
        $this->assertEquals(1, $application->id);
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
}

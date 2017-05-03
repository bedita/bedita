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
namespace BEdita\Core\Test\TestCase\Job;

use BEdita\Core\Job\JobService;
use BEdita\Core\Job\ServiceRunner;
use Cake\TestSuite\TestCase;

class Example implements JobService
{
    public function run($payload, $options = [])
    {
        return true;
    }
}

/**
 * {@see \BEdita\Core\Job\ServiceRunner} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Job\ServiceRunner
 */
class ServiceRunnerTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Job\ServiceRunner
     */
    public $ServiceRunner;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.async_jobs',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->ServiceRunner = new ServiceRunner();
    }

    /**
     * Test getService method
     *
     * @return void
     *
     * @covers ::getService()
     */
    public function testGetService()
    {
        $result = $this->ServiceRunner->getService('mail');
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(\BEdita\Core\Job\JobService::class, $result);

        // test instance registry
        $result2 = $this->ServiceRunner->getService('mail');
        $this->assertSame($result, $result2);
    }

    /**
     * Test getService failure
     *
     * @return void
     * @covers ::getService()
     */
    public function testGetServiceFail()
    {
        $this->expectException(\LogicException::class);
        $this->ServiceRunner->getService('gustavo');
    }

    /**
     * Test register method
     *
     * @return void
     *
     * @covers ::register()
     */
    public function testRegister()
    {
        $exampleService = new Example();
        $this->ServiceRunner->register('example', $exampleService);

        $result = $this->ServiceRunner->getService('example');
        $this->assertNotEmpty($result);
        $this->assertSame($exampleService, $result);
    }

    /**
     * Test register failure
     *
     * @return void
     * @covers ::register()
     */
    public function testRegisterFail()
    {
        $this->expectException(\LogicException::class);
        $this->ServiceRunner->register('gustavo', $this);
    }

    /**
     * Test run method
     *
     * @return void
     * @covers ::run()
     */
    public function testRun()
    {
        $exampleService = new Example();
        $this->ServiceRunner->register('example', $exampleService);

        $result = $this->ServiceRunner->run('d6bb8c84-6b29-432e-bb84-c3c4b2c1b99c');
        $this->assertTrue($result);
    }

    /**
     * Test run fail
     *
     * @return void
     * @covers ::reset()
     * @covers ::run()
     */
    public function testRunFail()
    {
        $this->ServiceRunner->reset();
        $result = $this->ServiceRunner->run('d6bb8c84-6b29-432e-bb84-c3c4b2c1b99c');
        $this->assertFalse($result);
    }

    /**
     * Test runPending method
     *
     * @return void
     * @covers ::runPending()
     */
    public function testRunPending()
    {
        $exampleService = new Example();
        $this->ServiceRunner->register('example', $exampleService);

        $result = $this->ServiceRunner->runPending(1);
        $expected = ['d6bb8c84-6b29-432e-bb84-c3c4b2c1b99c' => true];
        $this->assertNotEmpty($result);
        $this->assertEquals($expected, $result);
    }
}

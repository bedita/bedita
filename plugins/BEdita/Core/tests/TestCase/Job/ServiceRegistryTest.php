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
use BEdita\Core\Job\ServiceRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Job\ServiceRegistry} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Job\ServiceRegistry
 */
class ServiceRegistryTest extends TestCase
{

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        ServiceRegistry::reset();
    }

    /**
     * Get mock service.
     *
     * @param bool $return Return value for `run()` method.
     * @return \BEdita\Core\Job\JobService
     */
    protected function getMockService($return = true)
    {
        $service = $this->getMockBuilder(JobService::class)
            ->getMock();

        $service->method('run')
            ->will(static::returnValue($return));

        return $service;
    }

    /**
     * Test getService method
     *
     * @return void
     *
     * @covers ::get()
     */
    public function testGet()
    {
        $result = ServiceRegistry::get('mail');

        static::assertNotEmpty($result);
        static::assertInstanceOf(JobService::class, $result);

        // test instance registry
        $result2 = ServiceRegistry::get('mail');

        static::assertSame($result, $result2);

        // test dot notation
        $result = ServiceRegistry::get('BEdita/Core.mail');

        static::assertNotEmpty($result);
        static::assertInstanceOf(JobService::class, $result);
    }

    /**
     * Test getService failure
     *
     * @return void
     *
     * @expectedException \LogicException
     * @covers ::get()
     */
    public function testGetFail()
    {
        ServiceRegistry::get('gustavo');
    }

    /**
     * Test register method
     *
     * @return void
     *
     * @covers ::set()
     */
    public function testSet()
    {
        $service = $this->getMockService();

        ServiceRegistry::set('example', $service);
        static::assertAttributeContains($service, 'instances', ServiceRegistry::class);

        $result = ServiceRegistry::get('example');

        static::assertNotEmpty($result);
        static::assertSame($service, $result);
    }

    /**
     * Test registry reset.
     *
     * @return void
     *
     * @covers ::reset()
     */
    public function testReset()
    {
        ServiceRegistry::set('example', $this->getMockService());
        static::assertAttributeNotEmpty('instances', ServiceRegistry::class);

        ServiceRegistry::reset();
        static::assertAttributeSame([], 'instances', ServiceRegistry::class);
    }
}

<?php
declare(strict_types=1);

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
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\Core\Job\ServiceRegistry} Test Case
 */
#[CoversClass(ServiceRegistry::class)]
class ServiceRegistryTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function tearDown(): void
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
    protected function getStubService($return = true)
    {
        $service = $this->getStubBuilder(JobService::class)
            ->getStub();

        $service->method('run')
            ->willReturn($return);

        return $service;
    }

    /**
     * Test getService method
     *
     * @return void
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

        // test full path notation
        $result = ServiceRegistry::get('\\BEdita\\Core\\Job\\Service\\MailService');

        static::assertNotEmpty($result);
        static::assertInstanceOf(JobService::class, $result);
    }

    /**
     * Test getService failure
     *
     * @return void
     */
    public function testGetFail()
    {
        $this->expectException(LogicException::class);
        ServiceRegistry::get('gustavo');
    }

    /**
     * Test register method
     *
     * @return void
     */
    public function testSet()
    {
        $service = $this->getStubService();

        ServiceRegistry::set('example', $service);
        static::assertEquals(['example'], ServiceRegistry::keys());

        $result = ServiceRegistry::get('example');

        static::assertNotEmpty($result);
        static::assertSame($service, $result);
    }

    /**
     * Test registry reset.
     *
     * @return void
     */
    public function testReset()
    {
        ServiceRegistry::set('example', $this->getStubService());
        static::assertNotEmpty(ServiceRegistry::keys());

        ServiceRegistry::reset();
        static::assertEquals([], ServiceRegistry::keys());
    }
}

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
namespace BEdita\API\Test\Event;

use BEdita\API\Event\CommonEventHandler;
use BEdita\Core\Utility\LoggedUser;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\MiddlewareQueue;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\API\Event\CommonEventHandler
 */
class CommonEventHandlerTest extends TestCase
{
    public $fixtures = [
        'plugin.BEdita/Core.fake_animals'
    ];

    /**
     * Test implemented events
     * @covers ::implementedEvents()
     */
    public function testImplementedEvents()
    {
        static::assertCount(0, EventManager::instance()->listeners('Server.buildMiddleware'));

        EventManager::instance()->on(new CommonEventHandler());
        static::assertCount(1, EventManager::instance()->listeners('Server.buildMiddleware'));
    }

    /**
     * test build middleware stack
     *
     * @return void
     * @covers ::buildMiddlewareStack()
     */
    public function testBuildMiddlewareStack()
    {
        EventManager::instance()->on(new CommonEventHandler());

        $middleware = new MiddlewareQueue();
        static::assertCount(0, $middleware);

        $middleware->add(new ErrorHandlerMiddleware());
        static::assertCount(1, $middleware);

        $event = new Event('Server.buildMiddleware', null, ['middleware' => $middleware]);
        EventManager::instance()->dispatch($event);
        static::assertCount(2, $middleware);
        static::assertInstanceOf(ErrorHandlerMiddleware::class, $middleware->get(0));
        static::assertInstanceOf('\BEdita\API\Middleware\CorsMiddleware', $middleware->get(1));
    }

    /**
     * test after identify
     *
     * @return void
     * @covers ::afterIdentify()
     */
    public function testAfterIdentify()
    {
        LoggedUser::resetUser();
        EventManager::instance()->on(new CommonEventHandler());

        static::assertEquals([], LoggedUser::getUser());
        $event = new Event('Auth.afterIdentify', null, ['user' => ['id' => 1]]);
        EventManager::instance()->dispatch($event);
        static::assertEquals(['id' => 1], LoggedUser::getUser());
    }
}

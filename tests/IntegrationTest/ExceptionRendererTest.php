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

namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Exception\HttpException;

/**
 * @coversNothing
 */
class ExceptionRendererTest extends IntegrationTestCase
{

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        ConnectionManager::alias('test', 'default');
        ConnectionManager::drop('__fail_db_connection');

        parent::tearDown();
    }

    /**
     * Data provider for `testContentType` test case.
     *
     * @return array
     */
    public function contentTypeProvider()
    {
        return [
            'JSON' => [
                200,
                'application/json',
                'application/json',
            ],
            'JSON API' => [
                200,
                'application/vnd.api+json',
                'application/vnd.api+json',
            ],
            'JSON API (wrong media type)' => [
                415,
                'application/vnd.api+json',
                'application/vnd.api+json; m=test',
            ],
        ];
    }

    /**
     * Test content type negotiation rules.
     *
     * @param int $expectedCode Expected response code.
     * @param string|null $expectedContentType Expected content type.
     * @param string $accept Request's "Accept" header.
     * @param array|null $config Configuration to be written.
     * @return void
     *
     * @dataProvider contentTypeProvider
     */
    public function testContentType($expectedCode, $expectedContentType, $accept, array $config = null)
    {
        Configure::write($config);

        $this->configRequest([
            'headers' => [
                'Accept' => $accept,
            ],
        ]);

        $this->get('/roles');

        $this->assertResponseCode($expectedCode);
        $this->assertContentType($expectedContentType);
    }

    /**
     * Data provider for `testContentType` test case.
     *
     * @return array
     */
    public function contentTypeErrorProvider()
    {
        // Using an exception that surely isn't thrown anywhere else in our code.
        $exception = new HttpException('I\'m a teapot', 418);

        return [
            'JSON (initialize)' => [
                $exception->getCode(),
                'application/json',
                'application/json',
                $exception,
                'Controller.initialize',
            ],
            'JSON (beforeRender)' => [
                $exception->getCode(),
                'application/json',
                'application/json',
                $exception,
                'Controller.beforeRender',
            ],
            'JSON API (initialize)' => [
                $exception->getCode(),
                'application/vnd.api+json',
                'application/vnd.api+json',
                $exception,
                'Controller.initialize',
            ],
            'JSON API (beforeRender)' => [
                $exception->getCode(),
                'application/vnd.api+json',
                'application/vnd.api+json',
                $exception,
                'Controller.beforeRender',
            ],
        ];
    }

    /**
     * Test content type negotiation rules when error occurs.
     *
     * @param int $expectedCode Expected response code.
     * @param string|null $expectedContentType Expected content type.
     * @param string $accept Request's "Accept" header.
     * @param \Exception $error Error to be injected.
     * @param string $event Event name.
     * @return void
     *
     * @dataProvider contentTypeErrorProvider
     */
    public function testContentTypeError($expectedCode, $expectedContentType, $accept, \Exception $error, $event)
    {
        $eventManager = EventManager::instance();

        // Inject an error.
        $listener = function (Event $event) use ($eventManager, $error, &$listener) {
            // Immediately detach the listener to ensure it is executed only once.
            $eventManager->off($event->getName(), $listener);

            throw $error;
        };
        $eventManager->on($event, $listener);

        $this->configRequest([
            'headers' => [
                'Accept' => $accept,
            ],
        ]);
        $this->get('/roles');

        static::assertEquals($expectedCode, $this->_response->getStatusCode());
        $this->assertContentType($expectedContentType);
    }

    /**
     * Test DB connection failure
     *
     * @return void
     */
    public function testDBFail()
    {
        // change db connection to simulate db connection fails
        $connection = ConnectionManager::get('default');
        $dbConf = $connection->config();
        $dbConf['database'] = '__fail_db_connection';
        unset($dbConf['name']);
        ConnectionManager::setConfig('__fail_db_connection', $dbConf);
        ConnectionManager::alias('__fail_db_connection', 'default');

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/roles');

        $this->assertResponseCode(500);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotContains('<!DOCTYPE html>');
    }
}

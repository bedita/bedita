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

namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Exception\NotFoundException;

/**
 * @coversDefaultClass \BEdita\API\Controller\AppController
 */
class AppControllerTest extends IntegrationTestCase
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
            'json' => [
                200,
                'application/json',
                'application/json',
            ],
            'jsonapi' => [
                200,
                'application/vnd.api+json',
                'application/vnd.api+json',
            ],
            'jsonapiWrongMediaType' => [
                415,
                'application/vnd.api+json',
                'application/vnd.api+json; m=test',
            ],
            'htmlNotAllowed' => [
                406,
                'application/vnd.api+json',
                'text/html,application/xhtml+xml',
                [
                    'debug' => 0,
                    'Accept.html' => 0,
                ],
            ],
            'htmlDebugMode' => [
                200,
                'text/html',
                'text/html,application/xhtml+xml',
                [
                    'debug' => 1,
                    'Accept.html' => 0,
                ],
            ],
            'htmlAccepted' => [
                200,
                'text/html',
                'text/html,application/xhtml+xml',
                [
                    'debug' => 0,
                    'Accept.html' => 1,
                ],
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
     * @covers \BEdita\API\Controller\Component\JsonApiComponent::startup()
     * @covers \BEdita\API\Controller\Component\JsonApiComponent::beforeRender()
     * @covers \BEdita\API\Error\ExceptionRenderer::render()
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
        return [
            'notFoundJson' => [
                404,
                'application/json',
                'application/json',
                new NotFoundException(),
            ],
            'notFoundJsonapi' => [
                404,
                'application/vnd.api+json',
                'application/vnd.api+json',
                new NotFoundException(),
            ],
            'notFoundHtmlDebug' => [
                404,
                'text/html',
                'text/html,application/xhtml+xml',
                new NotFoundException(),
                [
                    'debug' => 1,
                    'Accept.html' => 0,
                ],
            ],
            'notFoundHtmlAccepted' => [
                404,
                'text/html',
                'text/html,application/xhtml+xml',
                new NotFoundException(),
                [
                    'debug' => 0,
                    'Accept.html' => 1,
                ],
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
     * @param array|null $config Configuration to be written.
     * @return void
     *
     * @dataProvider contentTypeErrorProvider
     * @covers \BEdita\API\Controller\Component\JsonApiComponent::startup()
     * @covers \BEdita\API\Controller\Component\JsonApiComponent::beforeRender()
     * @covers \BEdita\API\Error\ExceptionRenderer::render()
     */
    public function testContentTypeError($expectedCode, $expectedContentType, $accept, \Exception $error, array $config = null)
    {
        Configure::write($config);

        $events = ['Controller.initialize', 'Controller.beforeRender'];

        foreach ($events as $name) {
            $this->_controller = null;
            $this->injectError($name, $error);

            $this->configRequest([
                'headers' => [
                    'Accept' => $accept,
                ],
            ]);
            $this->get('/roles');
            static::assertEquals($expectedCode, $this->_response->getStatusCode(), 'Error with event ' . $name);
            $this->assertContentType($expectedContentType, 'Error with event ' . $name);
        }
    }

    /**
     * Helper method to inject error throwing an exception when an event is triggered
     *
     * @param string $eventName The event name
     * @param \Exception $exception The exception to throw when the event is triggered
     * @return void
     */
    protected function injectError($eventName, \Exception $exception)
    {
        $listener = function (Event $event) use ($exception, &$listener) {
            // immediately off the listener to assure to execute just one time
            EventManager::instance()->off($event->getName(), $listener);

            throw $exception;
        };

        EventManager::instance()->on($eventName, $listener);
    }

    /**
     * Data provider for `testHtmlResponseTemplates` test case.
     *
     * @return array
     */
    public function htmlResponseTemplatesProvider()
    {
        return [
            'success' => [
                200,
                '/roles'
            ],
            'missingRoute' => [
                404,
                '/find_me_if_you_can'
            ],
            'missingRecord' => [
                404,
                '/roles/9999999'
            ],
            'methodNotAllowed' => [
                405,
                '/roles'
            ],
        ];
    }

    /**
     * Test templates on HTML response.
     *
     * @param int $expectedCode Expected response code.
     * @param string $endpoint The endpoint to call
     * @return void
     *
     * @dataProvider htmlResponseTemplatesProvider
     * @covers ::html()
     * @covers \BEdita\API\Error\ExceptionRenderer::render()
     */
    public function testHtmlResponseTemplates($expectedCode, $endpoint)
    {
        Configure::write('debug', 1);

        // use $_SERVER array to assure using the right HTTP_ACCEPT header also if request
        // is recreated from globals as in \Cake\Error\ExceptionRenderer::_getController()
        $_SERVER['HTTP_ACCEPT'] = 'text/html';

        if ($expectedCode == 405) {
            $this->post($endpoint);
        } else {
            $this->get($endpoint);
        }

        $this->assertResponseCode($expectedCode);
        $this->assertLayout('html.ctp');
        $this->assertResponseContains('<!DOCTYPE html>');
        if ($expectedCode < 400) {
            $this->assertTemplate('html.ctp');
        } else {
            $this->assertTemplate('error.ctp');
        }
    }

    /**
     * Test DB connection failure
     *
     * @return void
     * @covers \BEdita\API\Error\ExceptionRenderer::render()
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

        // use $_SERVER array to assure using the right HTTP_ACCEPT header also
        // if request is recreated from globals as in \Cake\Error\ExceptionRenderer::_getController()
        $_SERVER['HTTP_ACCEPT'] = 'application/vnd.api+json';

        $this->get('/roles');

        $this->assertResponseCode(500);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotContains('<!DOCTYPE html>');
    }

    /**
     * Data provider for `testApiKey` test case.
     *
     * @return array
     */
    public function apiKeysProvider()
    {
        return [
            'apiKeyMissing' => [
                403,
                [
                    'eqe12131231231231412414' => [
                        'origin' => '*',
                    ],
                ],
            ],
            'apiKeyOk' => [
                200,
                [
                    'eqe12131231231231412414' => [
                        'origin' => '*',
                    ],
                ],
                'eqe12131231231231412414',
            ],
            'noApiKey' => [
                200,
                [],
            ],
            'originOk' => [
                200,
                [
                    'eqe12131231231231412414' => [
                        'origin' => 'example.com',
                    ],
                ],
                'eqe12131231231231412414',
                'example.com'
            ],
            'originKo' => [
                403,
                [
                    'eqe12131231231231412414' => [
                        'origin' => 'example.com',
                    ],
                ],
                'eqe12131231231231412414',
                'otherdomain.com'
            ],
        ];
    }

    /**
     * Test API KEY check rules.
     *
     * @param int $expectedCode Expected response code.
     * @param array $apiKeyCfg API KEY configuration.
     * @param string|null $apiKeyReq API KEY in request header.
     * @param string|null $origin Request's "Origin" header.
     * @return void
     *
     * @dataProvider apiKeysProvider
     * @covers ::apiKeyCheck()
     */
    public function testApiKeys($expectedCode, $apiKeyCfg, $apiKeyReq = null, $origin = null)
    {
        Configure::write('ApiKeys', $apiKeyCfg);

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/vnd.api+json',
                'Origin' => $origin,
                'X-Api-Key' => $apiKeyReq,
            ]
        ]);

        $this->get('/home');

        $this->assertResponseCode($expectedCode);
    }

    /**
     * Test API meta info header.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testMetaInfo()
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/vnd.api+json',
            ],
        ]);

        $this->_sendRequest('/home', 'HEAD');

        $this->assertHeader('X-BEdita-Version', Configure::read('BEdita.version'));
    }
}

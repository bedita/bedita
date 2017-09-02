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

namespace BEdita\API\Test\TestCase\Error;

use BEdita\API\Error\ExceptionRenderer;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Http\Response;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\API\Error\ExceptionRenderer
 */
class ExceptionRendererTest extends TestCase
{
    /**
     * The configuration to restore at the end of every unit test
     *
     * @var array
     */
    protected $backupConf = [];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->backupConf = [
            'debug' => Configure::read('debug'),
            'Accept.html' => Configure::read('Accept.html'),
            'App.paths.templates' => Configure::read('App.paths.templates'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        foreach ($this->backupConf as $key => $val) {
            Configure::write($key, $val);
        }

        // restore plugin if was unloaded
        if (!Plugin::loaded('BEdita/API')) {
            Plugin::load('BEdita/API', ['routes' => true]);
        }

        parent::tearDown();
    }

    /**
     * Data provider for `testErrorDetails` test case.
     *
     * @return array
     */
    public function errorDetailsProvider()
    {
        return [
            'simple' => [
                'err msg',
                'err msg',
            ],
            'detail' => [
                ['title' => 'err title', 'detail' => 'err detail'],
                'err title',
                'err detail'
            ],
            'detailArray' => [
                ['title' => 'new title', 'detail' => [['field' => ['cause' => 'err detail']]]],
                'new title',
                '[0.field.cause]: err detail '
            ],
            'detailArray2' => [
                [
                    'title' => 'new title',
                    'detail' => [
                        'field' => ['cause' => 'err detail'],
                        'nestedFields' => [
                            'field2' => ['cause2' => 'err detail2'],
                            'field3' => ['cause3' => 'err detail3'],
                        ]
                    ]
                ],
                'new title',
                '[field.cause]: err detail [nestedFields.field2.cause2]: err detail2 [nestedFields.field3.cause3]: err detail3 '
            ],
            'code' => [
                ['title' => 'err title', 'code' => 'err-code'],
                'err title',
                null,
                'err-code',
            ],
            'badCode' => [
                ['title' => 'err title', 'code' => ['err-code']],
                'err title',
            ],
            'not a Cake exception' => [
                new \LogicException('hello'),
                'hello',
            ],
        ];
    }

    /**
     * Test error detail on response
     *
     * @param array|string|\Exception $exception Expected error.
     * @param string $title Expected error title.
     * @param string $detail Additional details.
     * @param string $code Error code.
     * @return void
     *
     * @dataProvider errorDetailsProvider
     * @covers ::render()
     * @covers ::_message()
     * @covers ::_template()
     * @covers ::errorDetail()
     * @covers ::appErrorCode()
     */
    public function testErrorDetails($exception, $title, $detail = '', $code = '')
    {
        if (!($exception instanceof \Exception)) {
            $exception = new NotFoundException($exception);
        }

        $renderer = new ExceptionRenderer($exception);
        $renderer->controller->request = $renderer->controller->request->withEnv('HTTP_ACCEPT', 'application/json');
        $response = $renderer->render();

        $responseBody = json_decode((string)$response->getBody(), true);
        static::assertEquals('error', $renderer->template);
        static::assertEquals($title, $responseBody['error']['title']);
        if ($detail) {
            static::assertEquals($detail, $responseBody['error']['detail']);
        } else {
            static::assertArrayNotHasKey('detail', $responseBody['error']);
        }
        if ($code) {
            static::assertEquals($code, $responseBody['error']['code']);
        } else {
            static::assertArrayNotHasKey('code', $responseBody['error']);
        }
    }

    /**
     * Data provider for `testRenderJson` test case.
     *
     * @return array
     */
    public function renderJsonProvider()
    {
        return [
            'debugOn' => [
                'application/vnd.api+json',
                [
                    'debug' => 1,
                ],
            ],
            'debugOff' => [
                'text/html',
                [
                    'debug' => 0,
                    'Accept' => [
                        'html' => false
                    ]
                ],
            ],
            'debugOnPluginOff' => [
                'application/vnd.api+json',
                [
                    'debug' => 1,
                ],
                true
            ],
            'debugOffPluginOff' => [
                'text/html',
                [
                    'debug' => 0,
                    'Accept' => [
                        'html' => false
                    ]
                ],
                true
            ],
        ];
    }

    /**
     * Test render json error
     *
     * @param string $accept Request's "Accept" header.
     * @param array $config The configuration to use.
     * @param bool $unloadPlugin If unload BEdita/API before render.
     * @return void
     *
     * @dataProvider renderJsonProvider
     * @coversNothing
     */
    public function testRenderJson($accept, $config, $unloadPlugin = false)
    {
        Configure::write($config);

        if ($unloadPlugin) {
            Plugin::unload('BEdita/API');
        }

        $renderer = new ExceptionRenderer(new NotFoundException('test html'));
        $renderer->controller->request = $renderer->controller->request->withEnv('HTTP_ACCEPT', $accept);
        $response = $renderer->render();

        $this->checkResponseJson($renderer, $response, $config['debug']);
    }

    /**
     * Test render json error forcing the fallback to ::_outputMessageSafe()
     *
     * @param string $accept Request's "Accept" header.
     * @param array $config The configuration to use.
     * @param bool $unloadPlugin If unload BEdita/API before render.
     * @return void
     *
     * @dataProvider renderJsonProvider
     * @covers ::_outputMessageSafe()
     */
    public function testRenderJsonSafe($accept, $config, $unloadPlugin = false)
    {
        Configure::write($config);

        if ($unloadPlugin) {
            Plugin::unload('BEdita/API');
        }

        $renderer = new ExceptionRenderer(new NotFoundException('test html'));
        $renderer->controller->request = $renderer->controller->request->withEnv('HTTP_ACCEPT', $accept);

        $renderer->controller->getEventManager()->on('Controller.beforeRender', function () {
            throw new InternalErrorException();
        });

        $response = $renderer->render();

        $this->checkResponseJson($renderer, $response, $config['debug']);
    }

    /**
     * Perform some asserts to check JSON response
     *
     * @param \BEdita\API\Error\ExceptionRenderer $renderer
     * @param \Cake\Http\Response $response
     * @param int $debug
     * @return void
     */
    protected function checkResponseJson(ExceptionRenderer $renderer, Response $response, $debug)
    {
        $accept = $renderer->controller->request->getHeaderLine('accept');
        $contentTypeExpected = ($accept == 'application/json') ? $accept : 'application/vnd.api+json';
        $this->assertStringStartsWith($contentTypeExpected, $response->type());
        $responseBody = json_decode((string)$response->getBody(), true);
        $this->assertTrue(is_array($responseBody));

        $this->assertArrayHasKey('error', $responseBody);
        $this->assertArrayHasKey('status', $responseBody['error']);
        $this->assertArrayHasKey('title', $responseBody['error']);
        $this->assertEquals(404, $responseBody['error']['status']);
        $this->assertEquals('test html', $responseBody['error']['title']);
        if (!$debug) {
            $this->assertArrayNotHasKey('meta', $responseBody['error']);
        } else {
            $this->assertArrayHasKey('meta', $responseBody['error']);
            $this->assertNotEmpty($responseBody['error']['meta']);
            $this->assertArrayHasKey('trace', $responseBody['error']['meta']);
            $this->assertNotEmpty($responseBody['error']['meta']['trace']);
        }
    }
}

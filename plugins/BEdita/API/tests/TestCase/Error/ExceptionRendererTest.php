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
use Cake\Event\Event;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;
use Cake\Network\Response;
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
    public function errDetailsProvider()
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
        ];
    }

    /**
     * Test error detail on response
     *
     * @param string $errorMessage Expected error message.
     * @param string $title Expected error title.
     * @param string $detail Additional details.
     * @param string $code Error code.
     * @return void
     *
     * @dataProvider errDetailsProvider
     * @covers ::render()
     * @covers ::_message()
     * @covers ::errorDetail()
     * @covers ::appErrorCode()
     */
    public function testErrorDetails($errorMessage, $title, $detail = '', $code = '')
    {
        $renderer = new ExceptionRenderer(new NotFoundException($errorMessage));
        $renderer->controller->request->env('HTTP_ACCEPT', 'application/json');
        $response = $renderer->render();

        $responseBody = json_decode((string)$response->getBody(), true);
        $this->assertEquals($title, $responseBody['error']['title']);
        if ($detail) {
            $this->assertEquals($detail, $responseBody['error']['detail']);
        } else {
            $this->assertArrayNotHasKey('detail', $responseBody['error']);
        }
        if ($code) {
            $this->assertEquals($code, $responseBody['error']['code']);
        } else {
            $this->assertArrayNotHasKey('code', $responseBody['error']);
        }
    }

    /**
     * Data provider for `testIsHtmlToSend` test case.
     *
     * @return array
     */
    public function isHtmlToSendProvider()
    {
        return [
            'debugOnHtmlNotAccepted' => [
                true,
                'text/html',
                [
                    'debug' => 1,
                    'Accept' => [
                        'html' => false
                    ]
                ]
            ],
            'debugOffHtmlAccepted' => [
                true,
                'text/html',
                [
                    'debug' => 0,
                    'Accept' => [
                        'html' => true
                    ]
                ]
            ],
            'debugOffHtmlNotAccepted' => [
                false,
                'text/html',
                [
                    'debug' => 0,
                    'Accept' => [
                        'html' => false
                    ]
                ]
            ],
            'noHtmlRequest' => [
                false,
                'application/json',
                [
                    'debug' => 1,
                    'Accept' => [
                        'html' => true
                    ]
                ]
            ],
        ];
    }

    /**
     * Test isHtmlToSend()
     *
     * @param bool $expected Expected result.
     * @param string $accept Request's "Accept" header
     * @param array $config The configuration to use.
     * @return void
     *
     * @dataProvider isHtmlToSendProvider
     * @covers ::isHtmlToSend()
     * @covers ::__construct()
     */
    public function testIsHtmlToSend($expected, $accept, $config)
    {
        Configure::write($config);
        $renderer = new ExceptionRenderer(new NotFoundException());
        $renderer->controller->request->env('HTTP_ACCEPT', $accept);

        $this->assertEquals($expected, $renderer->isHtmlToSend());
    }

    /**
     * Data provider for `testRenderHtml` test case.
     *
     * @return array
     */
    public function renderHtmlProvider()
    {
        return [
            'debugOn' => [
                [
                    'debug' => 1,
                ],
            ],
            'debugOff' => [
                [
                    'debug' => 0,
                    'Accept' => [
                        'html' => true
                    ],
                ],
            ],
            'debugOnPluginOff' => [
                [
                    'debug' => 1,
                ],
                true
            ],
            'debugOffPluginOff' => [
                [
                    'debug' => 0,
                    'Accept' => [
                        'html' => true
                    ],
                ],
                true
            ],
        ];
    }

    /**
     * Test render html error.
     *
     * @param array $config The configuration to use.
     * @param bool $unloadPlugin If unload BEdita/API before render.
     * @return void
     *
     * @dataProvider renderHtmlProvider
     * @covers ::render()
     * @covers ::setupView()
     * @covers ::jsonError()
     */
    public function testRenderHtml($config, $unloadPlugin = false)
    {
        Configure::write($config);

        if ($unloadPlugin) {
            Plugin::unload('BEdita/API');
        }

        $renderer = new ExceptionRenderer(new NotFoundException('test html'));
        $renderer->controller->request->env('HTTP_ACCEPT', 'text/html');
        $response = $renderer->render();

        $this->checkResponseHtml($renderer, $response, $config['debug']);
    }

    /**
     * Test render html error forcing the fallback to ::_outputMessageSafe()
     *
     * @param array $config The configuration to use.
     * @param bool $unloadPlugin If unload BEdita/API before render.
     * @return void
     *
     * @dataProvider renderHtmlProvider
     * @covers ::render()
     * @covers ::setupView()
     * @covers ::jsonError()
     * @covers ::_outputMessageSafe()
     */
    public function testRenderHtmlSafe($config, $unloadPlugin = false)
    {
        Configure::write($config);

        if ($unloadPlugin) {
            Plugin::unload('BEdita/API');
        }

        $renderer = new ExceptionRenderer(new NotFoundException('test html'));
        $renderer->controller->request->env('HTTP_ACCEPT', 'text/html');

        $renderer->controller->eventManager()->on('Controller.beforeRender', function (Event $event) {
            // force missing layout exception
            $event->getSubject()->viewBuilder()->setLayoutPath('find_me_if_you_can');
        });

        $response = $renderer->render();

        $this->assertEquals('', $renderer->controller->viewBuilder()->getLayoutPath());
        $this->checkResponseHtml($renderer, $response, $config['debug']);
    }

    /**
     *  Perform some asserts to check html response
     *
     * @param \BEdita\API\Error\ExceptionRenderer $renderer
     * @param \Cake\Network\Response $response
     * @param int $debug
     * @return void
     */
    protected function checkResponseHtml(ExceptionRenderer $renderer, Response $response, $debug)
    {
        $this->assertStringStartsWith('text/html', $response->type());
        $doctype = strpos(strtolower((string)$response->getBody()), '<!doctype html>');
        $this->assertNotFalse($doctype);

        $this->assertArrayHasKey('responseBody', $renderer->controller->viewVars);
        $responseBody = json_decode($renderer->controller->viewVars['responseBody'], true);

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

        $pluginApiTemplatePath = ROOT . DS . 'plugins' . DS . 'BEdita' . DS . 'API' . DS . 'src' . DS . 'Template' . DS;
        $pathsTemplates = Configure::read('App.paths.templates');
        if (Plugin::loaded('BEdita/API')) {
            $this->assertEquals('BEdita/API', $renderer->controller->viewBuilder()->getPlugin());
            $this->assertNotContains($pluginApiTemplatePath, $pathsTemplates);
        } else {
            $this->assertNotEquals('BEdita/API', $renderer->controller->viewBuilder()->getPlugin());
            $this->assertContains($pluginApiTemplatePath, $pathsTemplates);
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
     * @covers ::setupView()
     * @covers ::jsonError()
     */
    public function testRenderJson($accept, $config, $unloadPlugin = false)
    {
        Configure::write($config);

        if ($unloadPlugin) {
            Plugin::unload('BEdita/API');
        }

        $renderer = new ExceptionRenderer(new NotFoundException('test html'));
        $renderer->controller->request->env('HTTP_ACCEPT', $accept);
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
     * @covers ::setupView()
     * @covers ::jsonError()
     * @covers ::_outputMessageSafe()
     */
    public function testRenderJsonSafe($accept, $config, $unloadPlugin = false)
    {
        Configure::write($config);

        if ($unloadPlugin) {
            Plugin::unload('BEdita/API');
        }

        $renderer = new ExceptionRenderer(new NotFoundException('test html'));
        $renderer->controller->request->env('HTTP_ACCEPT', $accept);

        $renderer->controller->eventManager()->on('Controller.beforeRender', function () {
            throw new InternalErrorException();
        });

        $response = $renderer->render();

        $this->checkResponseJson($renderer, $response, $config['debug']);
    }

    /**
     *  Perform some asserts to check JSON response
     *
     * @param \BEdita\API\Error\ExceptionRenderer $renderer
     * @param \Cake\Network\Response $response
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

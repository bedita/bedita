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
                false
            ],
            'debugOff' => [
                [
                    'debug' => 0,
                    'Accept' => [
                        'html' => true
                    ],
                ],
                false
            ],
            'forceOutputSafe' => [
                [
                    'debug' => 1,
                ],
                false,
                true
            ],
            'noPlugin' => [
                [
                    'debug' => 1,
                ],
                true
            ],
            'noPluginForceOutputSafe' => [
                [
                    'debug' => 1,
                ],
                true,
                true
            ],
        ];
    }

    /**
     * Test content type negotiation rules.
     *
     * @param array $config The configuration to use.
     * @param bool $unloadPlugin If BEdita/API should be unloaded before testing.
     * @param bool $forceOutputSafe If should be forced the fallback to _outputMessageSafe()
     * @return void
     *
     * @dataProvider renderHtmlProvider
     * @covers ::setupView()
     * @covers ::jsonError()
     * @covers ::_outputMessageSafe()
     */
    public function testRenderHtml($config, $unloadPlugin, $forceOutputSafe = false)
    {
        Configure::write($config);
        // unload plugin to simulate error when BEdita/API is not already loaded
        if ($unloadPlugin) {
            Plugin::unload('BEdita/API');
        }

        $renderer = new ExceptionRenderer(new NotFoundException('test html'));
        $renderer->controller->request->env('HTTP_ACCEPT', 'text/html');

        if ($forceOutputSafe) {
            $renderer->controller->eventManager()->on('Controller.beforeRender', function (Event $event) {
                // force missing layout exception
                $event->subject()->viewBuilder()->layoutPath('find_me_if_you_can');
            });
        }

        $response = $renderer->render();

        $this->assertStringStartsWith('text/html', $response->type());
        $doctype = strpos(strtolower($response->body()), '<!doctype html>');
        $this->assertNotFalse($doctype);

        $pluginApiTemplatePath = ROOT . DS . 'plugins' . DS . 'BEdita' . DS . 'API' . DS . 'src' . DS . 'Template' . DS;
        $pathsTemplates = Configure::read('App.paths.templates');

        if ($unloadPlugin) {
            $this->assertNotEquals('BEdita/API', $renderer->controller->viewBuilder()->plugin());
            $this->assertContains($pluginApiTemplatePath, $pathsTemplates);
        } else {
            $this->assertEquals('BEdita/API', $renderer->controller->viewBuilder()->plugin());
            $this->assertNotContains($pluginApiTemplatePath, $pathsTemplates);
        }

        if ($forceOutputSafe) {
            $this->assertEquals('', $renderer->controller->viewBuilder()->layoutPath());
        }

        $this->assertArrayHasKey('responseBody', $renderer->controller->viewVars);
        $responseBody = json_decode($renderer->controller->viewVars['responseBody'], true);

        $this->assertArrayHasKey('error', $responseBody);
        $this->assertArrayHasKey('status', $responseBody['error']);
        $this->assertArrayHasKey('title', $responseBody['error']);
        $this->assertArrayHasKey('meta', $responseBody['error']);
        $this->assertEquals(404, $responseBody['error']['status']);
        $this->assertEquals('test html', $responseBody['error']['title']);
        if (!$config['debug']) {
            $this->assertEmpty($responseBody['error']['meta']);
        } else {
            $this->assertNotEmpty($responseBody['error']['meta']);
            $this->assertArrayHasKey('trace', $responseBody['error']['meta']);
            $this->assertNotEmpty($responseBody['error']['meta']['trace']);
        }
    }

    /**
     * Data provider for `testRenderNoHtml` test case.
     *
     * @return array
     */
    public function renderNoHtmlProvider()
    {
        return [
            'debugOn' => [
                'application/json',
                [
                    'debug' => 1,
                ],
                false
            ],
            'debugOff' => [
                'text/html',
                [
                    'debug' => 0,
                ],
                false
            ],
            'forceOutputSafe' => [
                'application/vnd.api+json',
                [
                    'debug' => 1,
                ],
                false,
                true
            ],
            'noPlugin' => [
                'application/json',
                [
                    'debug' => 1,
                ],
                true
            ],
            'noPluginForceOutputSafe' => [
                'application/vnd.api+json',
                [
                    'debug' => 1,
                ],
                true,
                true
            ],
        ];
    }

    /**
     * Test content type negotiation rules.
     *
     * @param string $accept Request's "Accept" header.
     * @param array $config The configuration to use.
     * @param bool $unloadPlugin If BEdita/API should be unloaded before testing.
     * @param bool $forceOutputSafe If should be forced the fallback to _outputMessageSafe()
     * @return void
     *
     * @dataProvider renderNoHtmlProvider
     * @covers ::setupView()
     * @covers ::jsonError()
     * @covers ::_outputMessageSafe()
     */
    public function testRenderNoHtml($accept, $config, $unloadPlugin, $forceOutputSafe = false)
    {
        Configure::write($config);
        // unload plugin to simulate error when BEdita/API is not already loaded
        if ($unloadPlugin) {
            Plugin::unload('BEdita/API');
        }

        $renderer = new ExceptionRenderer(new NotFoundException('test html'));
        $renderer->controller->request->env('HTTP_ACCEPT', $accept);

        if ($forceOutputSafe) {
            $renderer->controller->eventManager()->on('Controller.beforeRender', function (Event $event) {
                throw new InternalErrorException();
            });
        }

        $response = $renderer->render();

        $contentTypeExpected = ($accept === 'application/json') ? $accept : 'application/vnd.api+json';

        $this->assertStringStartsWith($contentTypeExpected, $response->type());
        $responseBody = json_decode($response->body(), true);
        $this->assertTrue(is_array($responseBody));

        $this->assertArrayHasKey('error', $responseBody);
        $this->assertArrayHasKey('status', $responseBody['error']);
        $this->assertArrayHasKey('title', $responseBody['error']);
        $this->assertEquals(404, $responseBody['error']['status']);
        $this->assertEquals('test html', $responseBody['error']['title']);
        if (!$config['debug']) {
            $this->assertArrayNotHasKey('meta', $responseBody['error']);
        } else {
            $this->assertArrayHasKey('meta', $responseBody['error']);
            $this->assertNotEmpty($responseBody['error']['meta']);
            $this->assertArrayHasKey('trace', $responseBody['error']['meta']);
            $this->assertNotEmpty($responseBody['error']['meta']['trace']);
        }
    }
}

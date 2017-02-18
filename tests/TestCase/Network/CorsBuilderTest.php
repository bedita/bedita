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
namespace BEdita\API\Test\TestCase\Network;

use BEdita\API\Network\CorsBuilder;
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

/**
 * Test for RoutingMiddleware
 *
 * @covers \BEdita\API\Network\CorsBuilder
 */
class CorsBuilderTest extends TestCase
{
    /**
     * Setup method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Test build CORS response
     *
     * @return void
     */
    public function testBuild()
    {
        // no origin
        $corsBuilder = new CorsBuilder(new Response(), '');
        $response = $corsBuilder->allowOrigin('*')
            ->allowHeaders(['Authorization'])
            ->allowMethods(['POST', 'PUT', 'GET'])
            ->build();

        $this->assertEmpty($response->getHeader('Access-Control-Allow-Origin'));
        $this->assertEmpty($response->getHeader('Access-Control-Allow-Methods'));
        $this->assertEmpty($response->getHeader('Access-Control-Allow-Headers'));

        // No matching origin
        $corsBuilder = new CorsBuilder(new Response(), 'http://example.com');
        $response = $corsBuilder->allowOrigin('http://bedita.com')
            ->allowHeaders(['Authorization'])
            ->allowMethods(['POST', 'PUT', 'GET'])
            ->build();

        $this->assertEmpty($response->getHeader('Access-Control-Allow-Origin'));
        $this->assertEmpty($response->getHeader('Access-Control-Allow-Methods'));
        $this->assertEmpty($response->getHeader('Access-Control-Allow-Headers'));

        // Matching origin
        $corsBuilder = new CorsBuilder(new Response(), 'http://example.com');
        $response = $corsBuilder->allowOrigin(['http://bedita.com', 'http://example.com'])
            ->allowHeaders(['Authorization'])
            ->allowMethods(['POST', 'PUT', 'GET'])
            ->build();

        $this->assertEquals('http://example.com', $response->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('POST, PUT, GET', $response->getHeaderLine('Access-Control-Allow-Methods'));
        $this->assertEquals('Authorization', $response->getHeaderLine('Access-Control-Allow-Headers'));
    }
}

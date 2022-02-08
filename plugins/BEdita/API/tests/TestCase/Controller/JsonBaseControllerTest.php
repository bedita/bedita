<?php
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\Controller\JsonBaseController;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * Test case controller class
 */
class TestController extends JsonBaseController
{
    public function index(): void
    {
    }
}

/**
 * BEdita\API\Controller\JsonBaseController Test Case
 *
 * @uses \BEdita\API\Controller\JsonBaseController
 */
class JsonBaseControllerTest extends TestCase
{
    /**
     * Test `initialize()` method
     *
     * @covers ::initialize()
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $request = new ServerRequest([
            'environment' => [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_CONTENT_TYPE' => 'application/json',
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'input' => true,
            ]
        ]);

        $controller = new TestController($request);
        $controller->index();

        static::assertEquals('Json', $controller->RequestHandler->getConfig('viewClassMap.json'));
        static::assertEquals(['json_decode', true], $controller->RequestHandler->getConfig('inputTypeMap.json'));
        static::assertFalse($controller->components()->has('JsonApi'));
        static::assertEquals('Json', $controller->viewBuilder()->getClassName());
    }
}

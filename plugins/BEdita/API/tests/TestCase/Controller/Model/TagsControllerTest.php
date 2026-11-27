<?php
declare(strict_types=1);

namespace BEdita\API\Test\TestCase\Controller\Model;

use BEdita\API\Controller\Model\TagsController;
use BEdita\API\TestSuite\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TagsController::class)]
class TagsControllerTest extends IntegrationTestCase
{
    /**
     * Test /model/tags/usage endpoint
     *
     * @return void
     */
    public function testFilterUsage()
    {
        $this->configRequestHeaders('GET', ['Accept' => 'application/json']);
        $this->get('/model/tags/usage');
        $actual = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/json');
        $this->assertEquals(['data' => [['name' => 'first-tag', 'count' => 1]]], $actual);
    }
}

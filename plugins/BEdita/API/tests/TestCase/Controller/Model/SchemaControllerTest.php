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
namespace BEdita\API\Test\TestCase\Controller\Model;

use BEdita\API\Controller\Model\SchemaController;
use BEdita\API\Test\TestConstants;
use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Test\Utility\TestArraySubsetTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\API\Controller\Model\SchemaController} Test Case
 */
#[CoversClass(SchemaController::class)]
class SchemaControllerTest extends IntegrationTestCase
{
    use TestArraySubsetTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
    ];

    /**
     * Data provider for `testJsonSchema`
     *
     * @return array
     */
    public static function jsonSchemaProvider(): array
    {
        return [
            'locations' => [
                'locations',
            ],
            'roles' => [
                'roles',
                'application/schema+json',
            ],
            'users' => [
                'users',
                'text/html',
            ],
        ];
    }

    /**
     * Test `jsonSchema` method.
     *
     * @param string $type Type name
     * @param string $accept Accept request header
     * @return void
     */
    #[DataProvider('jsonSchemaProvider')]
    public function testJsonSchema($type, $accept = '')
    {
        $expected = [
            'definitions' => [],
            '$id' => "http://api.example.com/model/schema/$type",
            '$schema' => 'http://json-schema.org/draft-06/schema#',
            'type' => 'object',
        ];
        $headers = empty($accept) ? [] : ['Accept' => $accept];
        $this->configRequestHeaders('GET', $headers);
        $this->get("/model/schema/$type");
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/schema+json');
        static::assertArraySubset($expected, $result);
    }

    /**
     * Test `jsonSchema` method with an abstract object type.
     *
     * @return void
     */
    public function testJsonSchemaAbstractType()
    {
        $this->configRequestHeaders('GET');
        $this->get('model/schema/objects');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/schema+json');
        static::assertFalse($result);
    }

    /**
     * Test `jsonSchema` method on a disabled object type.
     *
     * @return void
     */
    #[CoversNothing]
    public function testJsonSchemaDisabled()
    {
        $this->configRequestHeaders('GET');
        $this->get('model/schema/news');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/schema+json');
        static::assertFalse($result);
    }

    /**
     * Test ETag response header and Not Modified response.
     *
     * @return void
     */
    public function testETag()
    {
        $this->configRequestHeaders('GET');
        $this->get('/model/schema/roles');

        $etagHeader = $this->_response->getHeaderLine('ETag');
        static::assertEquals(TestConstants::SCHEMA_REVISIONS['roles'], trim($etagHeader, '"'));

        $this->configRequestHeaders('GET', ['If-None-Match' => $etagHeader]);
        $this->get('/model/schema/roles');

        $this->assertResponseCode(304);
        static::assertEmpty((string)$this->_response->getBody());
    }
}

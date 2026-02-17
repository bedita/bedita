<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\Exception\ExpiredTokenException;
use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Utility\Hash;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for expired JWT token handling.
 */
#[CoversNothing]
class ExpiredTokenTest extends IntegrationTestCase
{
    /**
     * Provider for `testExpiredJwtToken`
     *
     * @return array
     */
    public static function expiredProvider(): array
    {
        return [
            'json api' => [
                '/roles',
                'application/vnd.api+json',
            ],
            'plain json' => [
                '/model/project',
                'application/json',
            ],
        ];
    }

    /**
     * Test API call with expired JWT bearer token.
     *
     * Verifies that an API request with an expired JWT token
     * returns a 401 error response with code 'be_token_expired'.
     *
     * @return void
     */
    #[DataProvider('expiredProvider')]
    public function testExpiredJwtToken(string $endoint, string $contentType): void
    {
        // Generate an expired JWT token (expired 10 seconds ago)
        $expiredToken = JWT::encode(['exp' => time() - 10], Security::getSalt(), 'HS256');

        $headers = [
            'Host' => 'api.example.com',
            'Accept' => $contentType,
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $expiredToken,
        ];
        $this->configRequestHeaders('GET', $headers);
        $this->get($endoint);

        $this->assertResponseCode(401);
        $this->assertContentType($contentType);
        $this->assertResponseNotEmpty();

        $body = json_decode((string)$this->_response->getBody(), true);

        static::assertArrayHasKey('error', $body);
        static::assertEquals('401', $body['error']['status']);
        static::assertEquals(ExpiredTokenException::BE_TOKEN_EXPIRED, Hash::get($body, 'error.code'));
    }
}

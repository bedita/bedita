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
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Text;

/**
 * Tests on anonymous login.
 *
 * @coversNothing
 */
class UuidLoginTest extends IntegrationTestCase
{

    /**
     * Test anonymous login with a UUID.
     *
     * @return void
     */
    public function testLoginUuid()
    {
        $uuid = Text::uuid();
        $this->configRequestHeaders('POST', [
            'Authorization' => 'UUID ' . $uuid,
        ]);
        $this->post('/auth');

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotEmpty();
        $body = json_decode((string)$this->_response->getBody(), true);

        $jwt = Hash::get($body, 'meta.jwt');
        static::assertNotNull($jwt);
        $payload = json_decode(base64_decode(explode('.', $jwt)[1]), true);

        $exists = TableRegistry::get('ExternalAuth')->exists([
            'provider_username' => $uuid,
            'user_id' => $payload['id'],
            'auth_provider_id' => 2,
        ]);
        static::assertTrue($exists);
    }

    /**
     * Test anonymous login with a UUID when `uuid` auth provider is disabled.
     *
     * @return void
     */
    public function testLoginUuidDisabled()
    {
        TableRegistry::get('AuthProviders')->delete(TableRegistry::get('AuthProviders')->get(2));

        $uuid = Text::uuid();
        $this->configRequestHeaders('POST', [
            'Authorization' => 'UUID ' . $uuid,
        ]);
        $this->post('/auth');

        $this->assertResponseCode(401);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotEmpty();
        $body = json_decode((string)$this->_response->getBody(), true);

        $jwt = Hash::get($body, 'meta.jwt');
        static::assertNull($jwt);

        $exists = TableRegistry::get('ExternalAuth')->exists([
            'provider_username' => $uuid,
        ]);
        static::assertFalse($exists);
    }

    /**
     * Test authentication with UUID, that is expected to fail on an endpoint other than `/auth`.
     *
     * @return void
     */
    public function testAuthUuidFailure()
    {
        $uuid = Text::uuid();
        $this->configRequestHeaders('POST', [
            'Authorization' => 'UUID ' . $uuid,
        ]);
        $this->post('/roles');

        $this->assertResponseCode(401);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseNotEmpty();

        $exists = TableRegistry::get('ExternalAuth')->exists([
            'provider_username' => $uuid,
        ]);
        static::assertFalse($exists);
    }
}

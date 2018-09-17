<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;
use Cake\I18n\Time;

/**
 * Fixture for `user_tokens` table.
 */
class UserTokensFixture extends TestFixture
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->records = [
            [
                'user_id' => 5,
                'application_id' => 1,
                'client_token' => 'toktoktoktoktok',
                'secret_token' => 'secretsecretsecret',
                'token_type' => 'otp',
                'created' => date('Y-m-d H:i:s'),
                'expires' => date('Y-m-d H:i:s', time() + 9000),
                'used' => null,
            ],
            [
                'user_id' => 1,
                'application_id' => null,
                'client_token' => 'abcdefghilkmnop',
                'secret_token' => null,
                'token_type' => 'refresh',
                'created' => date('Y-m-d H:i:s'),
                'expires' => null,
                'used' => null,
            ],
        ];

        parent::init();
    }
}

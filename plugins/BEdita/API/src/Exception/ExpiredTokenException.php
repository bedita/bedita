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

namespace BEdita\API\Exception;

use Cake\Network\Exception\UnauthorizedException;

/**
 * Exception raised on an expired JWT token
 */
class ExpiredTokenException extends UnauthorizedException
{
    /**
     * 401 Expired token
     *
     * @var string
     */
    const BE_TOKEN_EXPIRED = 'be_token_expired';

    /**
     * {@inheritDoc}
     */
    public function __construct($message = null)
    {
        if (empty($message)) {
            $message = [
                'title' => __d('bedita', 'Expired token'),
                'detail' => __d('bedita', 'Provided token has expired'),
                'code' => self::BE_TOKEN_EXPIRED,
            ];
        }
        parent::__construct($message);
    }
}

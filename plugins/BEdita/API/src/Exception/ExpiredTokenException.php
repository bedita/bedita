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

namespace BEdita\API\Exception;

use Cake\Http\Exception\UnauthorizedException;

/**
 * Exception raised on an expired JWT token.
 */
class ExpiredTokenException extends UnauthorizedException
{
    /**
     * Expired token internal application error code
     *
     * @var string
     */
    public const BE_TOKEN_EXPIRED = 'be_token_expired';

    /**
     * Constructor
     *
     * @param string $message If no message is given 'Expired token' will be the message
     */
    public function __construct(?string $message = null)
    {
        if (empty($message)) {
            $message = __d('bedita', 'Expired token');
        }
        parent::__construct($message);
        $this->_attributes['code'] = static::BE_TOKEN_EXPIRED;
    }
}

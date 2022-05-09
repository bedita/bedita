<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Exception;

use Cake\Core\Exception\CakeException as Exception;

/**
 * Exception raised when an already existing user is found.
 */
class UserExistsException extends Exception
{
    /**
     * Application error code
     *
     * @var string
     */
    public const BE_USER_EXISTS = 'be_user_exists';

    /**
     * {@inheritDoc}
     *
     * Use 400 as HTTP status code and add options details array to internal attributes.
     *
     * @codeCoverageIgnore
     */
    public function __construct(string $message, ?array $details = null)
    {
        parent::__construct($message, 400);
        $this->_attributes['code'] = static::BE_USER_EXISTS;
        $this->_attributes['detail'] = $details;
    }
}

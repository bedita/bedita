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
namespace BEdita\API\Exception;

use Cake\Network\Exception\HttpException;

/**
 * Represents an HTTP 415 error 'Unsupported Media Type'
 *
 */
class UnsupportedMediaTypeException extends HttpException
{

    /**
     * Constructor
     *
     * @param string|null $message If no message is given 'Unsupported Media Type' will be the message
     * @param int $code Status code, defaults to 415
     */
    public function __construct($message = null, $code = 415)
    {
        if (empty($message)) {
            $message = 'Unsupported Media Type';
        }
        parent::__construct($message, $code);
    }
}

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

namespace BEdita\Core\Exception;

use Cake\Core\Exception\Exception;

/**
 * Exception raised when bad filter data are passed to Model/ORM classes
 */
class BadFilterException extends Exception
{
    /**
     * {@inheritDoc}
     *
     * Default error code 400
     *
     * @codeCoverageIgnore
     */
    public function __construct($message, $code = 400, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

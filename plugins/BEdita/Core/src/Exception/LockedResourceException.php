<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
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
 * Exception raised when performing write actions on a resource that is locked.
 */
class LockedResourceException extends Exception
{
    /**
     * {@inheritDoc}
     *
     * Default error code 403
     *
     * @codeCoverageIgnore
     */
    public function __construct($message = null, $code = 403)
    {
        if ($message === null) {
            $message = __d('bedita', 'This resource is locked');
        }

        parent::__construct($message, $code);
    }
}

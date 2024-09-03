<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Error;

use Cake\Error\ErrorHandler as CakeErrorHandler;
use Cake\Utility\Hash;

class ErrorHandler extends CakeErrorHandler
{
    /**
     * @inheritDoc
     */
    protected function _displayError(array $error, $debug): void
    {
        if (!$debug) {
            return;
        }
        $msg = Hash::get($error, 'error');
        $code = Hash::get($error, 'code');
        $description = Hash::get($error, 'description');
        throw new \LogicException(sprintf('%s [%s] %s', $msg, $code, $description));
    }
}

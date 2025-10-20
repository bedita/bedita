<?php
declare(strict_types=1);

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

use Cake\Http\Exception\HttpException;
use Throwable;

/**
 * Exception raised when performing write actions on a resource that is locked.
 */
class LockedResourceException extends HttpException
{
    /**
     * @inheritDoc
     */
    protected int $_defaultCode = 403;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct(array|string $message = '', ?int $code = null, ?Throwable $previous = null)
    {
        if ($message === null) {
            $message = __d('bedita', 'This resource is locked');
        }

        parent::__construct($message, $code, $previous);
    }
}

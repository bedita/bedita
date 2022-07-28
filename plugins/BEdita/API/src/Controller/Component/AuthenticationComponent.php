<?php
declare(strict_types=1);

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
namespace BEdita\API\Controller\Component;

use Authentication\Authenticator\ResultInterface;
use Authentication\Controller\Component\AuthenticationComponent as CakeAuthenticationComponent;
use BEdita\API\Exception\ExpiredTokenException;
use Cake\Utility\Hash;
use Firebase\JWT\ExpiredException;

/**
 * Authentication component extended to handle expired token exception.
 */
class AuthenticationComponent extends CakeAuthenticationComponent
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->checkExpiredToken();
    }

    /**
     * Check if a JWT token expired exception
     * has been set in authentication result object.
     *
     * @return void
     * @throws \BEdita\API\Exception\ExpiredTokenException
     */
    protected function checkExpiredToken(): void
    {
        $result = $this->getResult();
        if (empty($result) || ($result->getStatus() !== ResultInterface::FAILURE_CREDENTIALS_INVALID)) {
            return;
        }
        $exception = Hash::get($result->getErrors(), 'exception');
        if (!$exception instanceof ExpiredException) {
            return;
        }

        throw new ExpiredTokenException();
    }
}

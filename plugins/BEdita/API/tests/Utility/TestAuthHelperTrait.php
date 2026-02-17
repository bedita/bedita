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
namespace BEdita\API\Test\Utility;

use Authentication\AuthenticationService;
use Authentication\Authenticator\AbstractAuthenticator;
use Authentication\Identity;
use BEdita\API\Utility\JWTHandler;
use BEdita\Core\Model\Entity\Application;
use BEdita\Core\State\CurrentApplication;
use PHPUnit\Framework\MockObject\Stub;

/**
 * Collection of useful authentication related methods.
 */
trait TestAuthHelperTrait
{
    /**
     * Helper method to generate JWT tokens.
     *
     * @param array $user The user data
     * @param \BEdita\Core\Model\Entity\Application $app The application entity
     * @return array
     */
    protected function generateJwtTokens(array $user, Application $app): array
    {
        $prevApp = CurrentApplication::getApplication();
        CurrentApplication::setApplication($app); // done to build JWT with the correct `app` claim
        $tokens = JWTHandler::tokens($user, 'https://example.com');
        CurrentApplication::setApplication($prevApp);

        return $tokens;
    }

    /**
     * Helper class for get stub of AuthenticationService.
     *
     * @param \Authentication\Identity|null $identity The identity
     * @param \Authentication\Authenticator\AbstractAuthenticator|null $authenticator The successful authenticator
     * @return \PHPUnit\Framework\MockObject\Stub
     */
    protected function getStubForAuthenticationService(?Identity $identity = null, ?AbstractAuthenticator $authenticator = null): Stub
    {
        $serviceStub = $this->getStubBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIdentity', 'getAuthenticationProvider'])
            ->getStub();

         $serviceStub
            ->method('getIdentity')
            ->willReturn($identity);

        $serviceStub
            ->method('getAuthenticationProvider')
            ->willReturn($authenticator);

        return $serviceStub;
    }
}

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
namespace BEdita\API\Middleware;

use Authentication\AuthenticationServiceInterface;
use Authentication\Authenticator\AuthenticatorInterface;
use Authentication\Authenticator\JwtAuthenticator;
use BEdita\Core\Model\Entity\User;
use BEdita\Core\Utility\LoggedUser;
use Cake\Event\EventDispatcherTrait;
use Cake\Http\Exception\UnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * LoggedUserMiddleware setup logged user info.
 */
class LoggedUserMiddleware implements MiddlewareInterface
{
    use EventDispatcherTrait;

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $service = $request->getAttribute('authentication');
        if (
            !$service instanceof AuthenticationServiceInterface ||
            empty($service->getIdentity())
        ) {
            if (in_array($request->getUri()->getPath(), ['/auth', '/auth/optout'])) {
                $this->dispatchEvent('Authentication.failure', compact('request'));
                throw new UnauthorizedException(__d('bedita', 'Login request not successful'));
            }

            return $handler->handle($request);
        }

        $this->checkLoggedUser($service);

        return $handler->handle($request);
    }

    /**
     * Set logged user and check payload if no user was found.
     *
     * @param \Authentication\AuthenticationServiceInterface $service Authentication service
     * @return void
     */
    protected function checkLoggedUser(AuthenticationServiceInterface $service): void
    {
        if ($this->setupLoggedUser($service)) {
            return;
        }

        $this->checkPayload($service->getAuthenticationProvider());
    }

    /**
     * Set logged user if present in authentication result.
     *
     * @param \Authentication\AuthenticationServiceInterface $service Authentication service
     * @return bool
     */
    protected function setupLoggedUser(AuthenticationServiceInterface $service): bool
    {
        $result = $service->getIdentity()->getOriginalData();
        if (
            (is_array($result) || $result instanceof \ArrayObject) &&
            !empty($result['username']) && !empty($result['id'])
        ) {
            LoggedUser::setUser($result);

            return true;
        }
        if (!$result instanceof User) {
            return false;
        }

        LoggedUser::setUser($result->toArray());

        return true;
    }

    /**
     * Check payload if no user has been found by authenticators.
     * In this case a `refresh_token` failure has happened.
     *
     * @param \Authentication\Authenticator\AuthenticatorInterface|null $provider Authenticator class.
     * @return void
     */
    protected function checkPayload(?AuthenticatorInterface $provider): void
    {
        if (!$provider instanceof JwtAuthenticator) {
            return;
        }

        $payload = $provider->getPayload();
        if (!empty($payload) && !empty($payload->sub)) { // refresh token of user was tried
            throw new UnauthorizedException(__d('bedita', 'Login request not successful'));
        }
    }
}

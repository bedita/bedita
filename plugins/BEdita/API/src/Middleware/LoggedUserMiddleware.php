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
use Authentication\Authenticator\JwtAuthenticator;
use BEdita\Core\Model\Entity\User;
use BEdita\Core\Utility\LoggedUser;
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
            $path = $request->getUri()->getPath();
            if (in_array($path, ['/auth', '/auth/optout']) && $request->getMethod() === 'POST') {
                throw new UnauthorizedException(__('Login request not successful'));
            }

            return $handler->handle($request);
        }

        $result = $service->getIdentity()->getOriginalData();
        if (
            (is_array($result) || $result instanceof \ArrayObject) &&
            !empty($result['username']) && !empty($result['id'])
        ) {
            LoggedUser::setUser($result);
        } elseif ($result instanceof User) {
            LoggedUser::setUser($result->toArray());
        } else {
            // check payload if a token refresh with user data failed
            $provider = $service->getAuthenticationProvider();
            if ($provider instanceof JwtAuthenticator) {
                $payload = $provider->getPayload();
                if (!empty($payload) && !empty($payload->sub)) {
                    throw new UnauthorizedException(__('Login request not successful'));
                }
            }
        }

        return $handler->handle($request);
    }
}

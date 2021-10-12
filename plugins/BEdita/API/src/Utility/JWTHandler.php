<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Utility;

use BEdita\API\Exception\ExpiredTokenException;
use BEdita\Core\State\CurrentApplication;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Encode/decode JWT token
 *
 * @since 4.6.0
 */
class JWTHandler
{
    /**
     * Decode JWT token.
     *
     * @param string $token JWT token to decode.
     * @return array The token's payload as a PHP array.
     * @throws \Exception Throws an exception if the token could not be decoded.
     */
    public static function decode(string $token)
    {
        $algorithm = Configure::read('Security.jwt.algorithm') ?: 'HS256';

        try {
            $payload = JWT::decode($token, Security::getSalt(), [$algorithm]);
        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new ExpiredTokenException();
        }

        return (array)$payload;
    }

    /**
     * Calculate JWT token for auth and renew operations
     *
     * @param array $user Minimal user data to encode in JWT
     * @param \Cake\Http\ServerRequest $request Request object.
     * @return array JWT tokens requested
     */
    public static function tokens(array $user, ServerRequest $request): array
    {
        $algorithm = Configure::read('Security.jwt.algorithm') ?: 'HS256';
        $duration = Configure::read('Security.jwt.duration') ?: '+20 minutes';
        $currentUrl = Router::reverse($request, true);
        $salt = Security::getSalt();
        $claims = [
            'iss' => Router::fullBaseUrl(),
            'iat' => time(),
            'nbf' => time(),
        ];
        $appId = CurrentApplication::getApplicationId();
        $payload = $user + $claims + [
            'app' => $appId,
            'exp' => strtotime($duration),
        ];
        $jwt = JWT::encode($payload, $salt, $algorithm);

        $payload = $claims + [
            'sub' => Hash::get($user, 'id'),
            'app' => $appId,
            'aud' => $currentUrl,
            'exp' => strtotime($duration),
        ];
        $renew = JWT::encode($payload, $salt, $algorithm);

        return compact('jwt', 'renew');
    }
}

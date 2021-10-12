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
use Cake\Core\InstanceConfigTrait;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Encode/decode JWT token
 *
 * @since 4.6.0
 */
class JWTHandler
{
    use InstanceConfigTrait;

    /**
     * Default config for this object.
     *
     * - `header` The header where the token is stored. Defaults to `'Authorization'`.
     * - `headerPrefix` The prefix to the token in header. Defaults to `'Bearer'`.
     * - `queryParam` The query parameter where the token is passed as a fallback. Defaults to `'token'`.
     * - `allowedAlgorithms` List of supported verification algorithms. Defaults to `['HS256']`.
     *   See API of JWT::decode() for more info.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'allowedAlgorithms' => [
            'HS256',
            'HS512',
        ],
    ];

    /**
     * Decode JWT token.
     *
     * @param string $token JWT token to decode.
     * @param \Cake\Http\ServerRequest $request Request object.
     * @return array The token's payload as a PHP array.
     * @throws \Exception Throws an exception if the token could not be decoded.
     */
    public function decode(string $token, ServerRequest $request)
    {
        try {
            $payload = JWT::decode($token, Security::getSalt(), $this->getConfig('allowedAlgorithms'));

            // if (isset($payload->aud)) {
            //     $audience = Router::url($payload->aud, true);
            //     if (strpos($audience, Router::reverse($request, true)) !== 0) {
            //         throw new \DomainException('Invalid audience');
            //     }
            // }
        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new ExpiredTokenException();
        }

        return (array)$payload;
    }
}

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
namespace BEdita\API\Middleware;

use BEdita\API\Utility\JWTHandler;
use BEdita\Core\State\CurrentApplication;
use Cake\Core\InstanceConfigTrait;
use Cake\Utility\Hash;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Middleware to parse JWT token or API KEY and retrieve current `application`
 *
 * @since 4.6.0
 */
class TokenMiddleware
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
        'header' => 'Authorization',
        'headerPrefix' => 'Bearer',
        'queryParam' => 'token',
        'payloadAttribute' => 'jwt',
    ];

    /**
     * Parsed token.
     *
     * @var string|null
     */
    protected $token = null;

    /**
     * Payload data.
     *
     * @var object|null
     */
    protected $payload = null;

    /**
     * Invoke method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @param callable $next Callback to invoke the next middleware.
     * @return \Psr\Http\Message\ResponseInterface A response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $token = $this->getToken($request);
        if (!empty($token)) {
            $jwt = new JWTHandler();
            $payload = $jwt->decode($token, $request);
            $request = $request->withAttribute($this->getConfig('payloadAttribute'), $payload);
        }
        CurrentApplication::setFromRequest($request);

        return $next($request, $response);
    }

    /**
     * Get token from header or query string.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @return string|null Token string if found else null.
     */
    protected function getToken(ServerRequestInterface $request): ?string
    {
        $header = trim($request->getHeaderLine($this->getConfig('header')));
        $headerPrefix = strtolower(trim($this->getConfig('headerPrefix'))) . ' ';
        $headerPrefixLength = strlen($headerPrefix);
        if ($header && strtolower(substr($header, 0, $headerPrefixLength)) == $headerPrefix) {
            return $this->token = substr($header, $headerPrefixLength);
        }

        if (!empty($this->getConfig('queryParam'))) {
            return $this->token = Hash::get(
                $request->getQueryParams(),
                $this->$this->getConfig('queryParam')
            );
        }

        return null;
    }
}

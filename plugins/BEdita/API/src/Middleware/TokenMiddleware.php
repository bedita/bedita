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

use BadMethodCallException;
use BEdita\API\Utility\JWTHandler;
use BEdita\Core\Model\Entity\Application;
use BEdita\Core\State\CurrentApplication;
use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
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
        'apiKey' => [
            'header' => 'X-Api-Key',
            'query' => 'api_key',
        ],
    ];

    /**
     * Request attribute name where payload is stored
     *
     * @var string
     */
    public const PAYLOAD_REQUEST_ATTRIBUTE = 'jwt';

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
        $payload = [];
        $token = $this->getToken($request);
        if (!empty($token)) {
            $payload = JWTHandler::decode($token);
            $request = $request->withAttribute(static::PAYLOAD_REQUEST_ATTRIBUTE, $payload);
        }

        // Read application from JWT payload first - fallback to API KEY
        $id = Hash::get($payload, 'app');
        if (!empty($id)) {
            $application = new Application(compact('id'));
            CurrentApplication::setApplication($application);
        } else {
            $this->applicationFromApiKey($request);
        }

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
        $headerPrefix = strtolower(trim((string)$this->getConfig('headerPrefix'))) . ' ';
        $headerPrefixLength = strlen($headerPrefix);
        if ($header && strtolower(substr($header, 0, $headerPrefixLength)) == $headerPrefix) {
            return substr($header, $headerPrefixLength);
        }

        if (!empty($this->getConfig('queryParam'))) {
            return Hash::get($request->getQueryParams(), $this->getConfig('queryParam'));
        }

        return null;
    }

    /**
     * Read application from API KEY.
     * This is done primarily with an API_KEY header like 'X-Api-Key',
     * alternatively `api_key` query string is used (not recommended)
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @return void
     * @throws \Cake\Http\Exception\ForbiddenException Throws an exception if API key is missing or invalid.
     */
    protected function applicationFromApiKey(ServerRequestInterface $request)
    {
        $apiKey = $request->getHeaderLine($this->getConfig('apiKey.header'));
        if (empty($apiKey)) {
            $apiKey = (string)Hash::get(
                $request->getQueryParams(),
                $this->getConfig('apiKey.query')
            );
        }
        if (empty($apiKey) && empty(Configure::read('Security.blockAnonymousApps', true))) {
            return;
        }

        try {
            CurrentApplication::setFromApiKey($apiKey);
        } catch (BadMethodCallException $e) {
            throw new ForbiddenException(__d('bedita', 'Missing API key'));
        } catch (RecordNotFoundException $e) {
            throw new ForbiddenException(__d('bedita', 'Invalid API key'));
        }
    }
}

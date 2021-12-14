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
     * - `apiKey` The `header` or `query` string used to contain API KEY. Defaults to 'X-Api-Key' header.
     * - `clientAuth` The request data used to identify a `client_credentials` grant type request.
     *      Default is `/auth` path and `client_credentials` ad value of `grant_type` field in request body.
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
        'clientAuth' => [
            'path' => '/auth',
            'field' => [
                'name' => 'grant_type',
                'value' => 'client_credentials',
            ],
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
        $payload = (array)$request->getAttribute(static::PAYLOAD_REQUEST_ATTRIBUTE);
        if (empty($payload)) {
            $token = $this->getToken($request);
            if (!empty($token)) {
                $payload = JWTHandler::decode($token);
                $request = $request->withAttribute(static::PAYLOAD_REQUEST_ATTRIBUTE, $payload);
            }
        }
        $this->readApplication($payload, $request);

        return $next($request, $response);
    }

    /**
     *  Read application from JWT payload first or from API KEY as fallback
     *
     * @param array $payload JWT Payload
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @return void
     */
    protected function readApplication(array $payload, ServerRequestInterface $request): void
    {
        $app = (array)Hash::get($payload, 'app');
        if (!empty($app) && !empty($app['id'])) {
            $application = new Application($app);
            CurrentApplication::setApplication($application);
        } else {
            $this->applicationFromApiKey($request);
        }
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

        $name = $this->getConfig('queryParam');
        if (!empty($name) && Hash::check($request->getQueryParams(), $name)) {
            return Hash::get($request->getQueryParams(), $name);
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
    protected function applicationFromApiKey(ServerRequestInterface $request): void
    {
        $apiKey = $this->fetchApiKey($request);
        // null API Key as return type is allowed => skip application load
        if ($apiKey === null) {
            return;
        }

        try {
            CurrentApplication::setFromApiKey($apiKey);
        } catch (RecordNotFoundException $e) {
            throw new ForbiddenException(__d('bedita', 'Invalid API key'));
        }
    }

    /**
     * Fetch API Key from headers or query string.
     * Check if a null API Key is allowed, otherwise raise a 403 Error
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @return string|null
     */
    protected function fetchApiKey(ServerRequestInterface $request): ?string
    {
        $apiKey = $request->getHeaderLine($this->getConfig('apiKey.header'));
        if (!empty($apiKey)) {
            return $apiKey;
        }

        $apiKey = (string)Hash::get(
            $request->getQueryParams(),
            $this->getConfig('apiKey.query')
        );
        if (!empty($apiKey)) {
            return $apiKey;
        }
        // An empty API KEY is allowed if 'Security.blockAnonymousApps' is set to false
        // or in case of an authentication request with `client_credentials` as grant type.
        if (empty(Configure::read('Security.blockAnonymousApps', true))) {
            return null;
        }
        $this->verifyClientCredentials($request);

        return null;
    }

    /**
     * Verify that the request is a `client_credentials` request:
     *  * `POST /auth` as method and path
     *  * `grant_type` field with `client_credentials` value must be set in POST body
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @return void
     */
    protected function verifyClientCredentials(ServerRequestInterface $request): void
    {
        $path = $request->getUri()->getPath();
        $isPost = $request->getMethod() === 'POST';
        // We assume that `Content-Type` is application/json
        $body = (array)json_decode((string)$request->getBody(), true);
        $value = Hash::get($body, $this->getConfig('clientAuth.field.name'));
        $contentType = $request->getHeaderLine('Content-Type');
        if (
            $path === $this->getConfig('clientAuth.path') &&
            $isPost && $value === $this->getConfig('clientAuth.field.value') &&
            $contentType === 'application/json'
        ) {
            return;
        }

        throw new ForbiddenException(__d('bedita', 'Missing API key'));
    }
}

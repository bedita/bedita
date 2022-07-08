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
use Authentication\Authenticator\TokenAuthenticator;
use Authentication\Identifier\JwtSubjectIdentifier;
use BEdita\API\Authenticator\ApplicationAuthenticator;
use BEdita\Core\Model\Entity\Application;
use BEdita\Core\State\CurrentApplication;
use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Utility\Hash;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * ApplicationMiddleware extracts application info and set current app.
 */
class ApplicationMiddleware implements MiddlewareInterface
{
    use InstanceConfigTrait;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'apiKey' => [
            'header' => 'X-Api-Key',
            'query' => 'api_key',
        ],
        'blockAnonymousApps' => true,
    ];

    /**
     * Constructor.
     *
     * @param array $config The middleware configuration.
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $service = $request->getAttribute('authentication');
        if (!$service instanceof AuthenticationServiceInterface) {
            return $handler->handle($request);
        }

        $provider = $service->getAuthenticationProvider();
        if ($provider instanceof ApplicationAuthenticator) {
            $payload = $service->getIdentity()->getOriginalData();
        } elseif (!$provider instanceof TokenAuthenticator || !method_exists($provider, 'getPayload')) {
            $provider = new JwtAuthenticator(new JwtSubjectIdentifier());
            try {
                $payload = $provider->getPayload($request);
            } catch (\Exception $e) {
                $payload = null;
            }
        } else {
            $payload = $provider->getPayload();
        }

        $this->readApplication($payload, $request);

        return $handler->handle($request);
    }

    /**
     *  Read application from JWT payload first or from API KEY as fallback
     *
     * @param object|null $payload JWT Payload
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @return void
     */
    protected function readApplication(?object $payload, ServerRequestInterface $request): void
    {
        if ($payload instanceof Application) {
            CurrentApplication::setApplication($payload);

            return;
        }

        $app = $payload->app ?? null;
        if (!empty($app) && !empty($app->id)) {
            $app = json_decode(json_encode($app), true);
            $application = new Application($app);
            CurrentApplication::setApplication($application);

            return;
        }

        $this->applicationFromApiKey($request);
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

        throw new ForbiddenException(__d('bedita', 'Missing API key'));
    }
}

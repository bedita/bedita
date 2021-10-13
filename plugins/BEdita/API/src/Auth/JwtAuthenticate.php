<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016-2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Auth;

use BEdita\API\Middleware\TokenMiddleware;
use Cake\Auth\BaseAuthenticate;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * An authentication adapter for authenticating using JSON Web Tokens.
 *
 * ```
 *  $this->Auth->config('authenticate', [
 *      'BEdita/Auth.Jwt' => [
 *          'parameter' => 'token',
 *          'userModel' => 'Users',
 *          'fields' => [
 *              'username' => 'id',
 *          ],
 *      ],
 *  ]);
 * ```
 *
 * @see http://jwt.io
 * @see http://tools.ietf.org/html/draft-ietf-oauth-json-web-token
 *
 * @since 4.0.0
 */
class JwtAuthenticate extends BaseAuthenticate
{

    /**
     * Default config for this object.
     *
     * - `fields` The fields to use to identify a user by.
     * - `userModel` The alias for users table, defaults to Users.
     * - `finder` The finder method to use to fetch user record. Defaults to 'all'.
     *   You can set finder name as string or an array where key is finder name and value
     *   is an array passed to `Table::find()` options.
     *   E.g. ['finderName' => ['some_finder_option' => 'some_value']]
     * - `authenticate` Boolean field indicating if we are performing an explicit `authenticate` action,
     *   used in `refresh_token` grant use case
     *
     * @var array
     */
    protected $_defaultConfig = [
        'fields' => [
            'username' => 'id',
            'password' => null,
        ],
        'userModel' => 'Users',
        'finder' => 'login',
        'authenticate' => false,
    ];

    /**
     * Payload data.
     *
     * @var object|null
     */
    protected $payload = null;

    /**
     * Get user record based on info available in JWT.
     *
     * @param \Cake\Http\ServerRequest $request The request object.
     * @param \Cake\Http\Response $response Response object.
     * @return array|false User record array or false on failure.
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        $this->setConfig('authenticate', true);

        return $this->getUser($request);
    }

    /**
     * Get user record based on info available in JWT.
     *
     * @param \Cake\Http\ServerRequest $request Request object.
     * @return array|false User record array, `false` on failure.
     */
    public function getUser(ServerRequest $request)
    {
        $payload = $this->getPayload($request);
        if (empty($payload)) {
            return false;
        }

        if ((string)$request->getData('grant_type') === 'refresh_token') {
            return $this->handleRefreshToken($request);
        }

        return isset($payload['id']) ? $payload : false;
    }

    /**
     * Handle refresh token grant looking at payload:
     *  - first `aud` (audience) claim is checked
     *  - if `sub` claim is not null and `authenticate()` was called => renew user access token
     *  - if `sub` claim is null and `app` claim is not => renew client credential token
     *
     * @param \Cake\Http\ServerRequest $request Request object.
     * @return array|false User record array, `false` on failure.
     */
    protected function handleRefreshToken(ServerRequest $request)
    {
        $this->checkAudience($request);

        if ($this->getConfig('authenticate') && !empty($this->payload['sub'])) {
            return $this->_findUser($this->payload['sub']);
        } elseif (empty($this->payload['sub']) && !empty($this->payload['app'])) {
            $this->_registry->getController()->Auth->setConfig('renewClientCredentials', true);
        }

        return false;
    }

    /**
     * Check audience claim.
     * If claim is missing or 'audience` check fails an exception is thrown.
     *
     * @param \Cake\Http\ServerRequest $request Request instance or null
     * @return void
     * @throws \DomainException|\Cake\Http\Exception\UnauthorizedException
     */
    protected function checkAudience(ServerRequest $request): void
    {
        $audience = Hash::get($this->payload, 'aud');
        if (empty($audience)) {
            throw new \DomainException('Invalid audience');
        }
        try {
            $url = Router::url($audience, true);
            if (strpos($url, Router::reverse($request, true)) !== 0) {
                throw new \DomainException('Invalid audience');
            }
        } catch (\Exception $ex) {
            throw new UnauthorizedException($ex->getMessage());
        }
    }

    /**
     * Get payload data.
     *
     * @param \Cake\Http\ServerRequest $request Request instance or null
     * @return array Payload array.
     * @throws \DomainException|\Cake\Http\Exception\UnauthorizedException If 'audience` check fails.
     */
    public function getPayload(ServerRequest $request): array
    {
        if (!empty($this->payload)) {
            return $this->payload;
        }
        $payload = $request->getAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, false);

        return $this->payload = (array)$payload;
    }

    /**
     * Handles an unauthenticated access attempt.
     *
     * @param \Cake\Http\ServerRequest $request A request object.
     * @param \Cake\Http\Response $response A response object.
     * @return void
     * @throws \Cake\Http\Exception\UnauthorizedException Throws an exception.
     */
    public function unauthenticated(ServerRequest $request, Response $response)
    {
        $message = $this->_registry->getController()->Auth->getConfig('authError');
        if ($this->error) {
            $message = $this->error->getMessage();
        }

        throw new UnauthorizedException($message);
    }
}

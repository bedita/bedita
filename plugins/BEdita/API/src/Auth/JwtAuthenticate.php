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
use BEdita\Core\State\CurrentApplication;
use Cake\Auth\BaseAuthenticate;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Exception;

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
     * - `header` The header where the token is stored. Defaults to `'Authorization'`.
     * - `headerPrefix` The prefix to the token in header. Defaults to `'Bearer'`.
     * - `queryParam` The query parameter where the token is passed as a fallback. Defaults to `'token'`.
     * - `allowedAlgorithms` List of supported verification algorithms. Defaults to `['HS256']`.
     *   See API of JWT::decode() for more info.
     * - `fields` The fields to use to identify a user by.
     * - `userModel` The alias for users table, defaults to Users.
     * - `finder` The finder method to use to fetch user record. Defaults to 'all'.
     *   You can set finder name as string or an array where key is finder name and value
     *   is an array passed to `Table::find()` options.
     *   E.g. ['finderName' => ['some_finder_option' => 'some_value']]
     * - `passwordHasher` Password hasher class. Can be a string specifying class name
     *    or an array containing `className` key, any other keys will be passed as
     *    config to the class. Defaults to 'Default'.
     * - Options `scope` and `contain` have been deprecated since 3.1. Use custom
     *   finder instead to modify the query to fetch user record.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'fields' => [
            'username' => 'id',
            'password' => null,
        ],
        'userModel' => 'Users',
        'scope' => [],
        'finder' => 'login',
        'contain' => null,
        'passwordHasher' => 'Default',
        'queryDatasource' => false,
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

        // if 'sub' claim is set in payload and 'queryDatasource' configuration is `true`
        // we are renewing the user access token
        if (!empty($payload['sub']) && $this->_config['queryDatasource']) {
            return $this->_findUser($payload['sub']);
        }
        // if `sub` claim exists and is null and application is set
        // we are renewing an application-only access token
        if (array_key_exists('sub', $payload) && $payload['sub'] === null && CurrentApplication::getApplicationId()) {
            $this->_registry->getController()->Auth->setConfig('clientCredentials', true);

            return false;
        }

        return isset($payload['id']) ? $payload : false;
    }

    /**
     * Get payload data.
     *
     * @param \Cake\Http\ServerRequest $request Request instance or null
     * @return array Payload array.
     * @throws \Exception Throws an exception if the token could not be decoded and debug is active.
     */
    public function getPayload(ServerRequest $request): array
    {
        if (!empty($this->payload)) {
            return $this->payload;
        }

        // retrieve payload from request and check audience
        $payload = $request->getAttribute(TokenMiddleware::PAYLOAD_REQUEST_ATTRIBUTE, false);
        if (empty($payload)) {
            return [];
        }
        if (!isset($payload['aud'])) {
            return $this->payload = (array)$payload;
        }

        // Check audience if set in payload
        try {
            $audience = Router::url($payload['aud'], true);
            if (strpos($audience, Router::reverse($request, true)) !== 0) {
                throw new \DomainException('Invalid audience');
            }
        } catch (\Exception $ex) {
            throw new UnauthorizedException($ex->getMessage());
        }

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

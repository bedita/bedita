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

namespace BEdita\Core\Utility;

use Cake\Core\InstanceConfigTrait;
use Cake\Http\Client;

/**
 * Class to call an OAuth2 provider URL using access tokens
 *
 * @since 4.6.0
 */
class OAuth2
{
    use InstanceConfigTrait;

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        // HTTP client configuration
        'client' => [],
        // OAuth2 request options
        'header' => 'Authorization',
        'headerPrefix' => 'Bearer',
        'queryParam' => 'access_token',
        // Additional headers required by OAuth2 provider
        'additionalHeaders' => [],
        // mode can be 'header' (default) or 'query'
        'mode' => 'header',
    ];

    /**
     * Get a response from an OAuth2 provider usgin access token
     *
     * @param string $url OAuth2 provider check URL
     * @param string $accessToken Access token to use in request
     * @param array $options OAuth2 request options
     * @return array Response from an OAuth2 provider
     */
    public function response(string $url, string $accessToken, array $options = []): array
    {
        $this->setConfig($options);
        $client = new Client((array)$this->getConfig('client'));
        $query = $this->getQuery($accessToken);
        $headers = $this->getHeaders($accessToken) + (array)$this->getConfig('additionalHeaders');
        $response = $client->get($url, $query, compact('headers'));

        return (array)$response->getJson();
    }

    /**
     * Get OAuth2 request query params
     *
     * @param string $token Access token.
     * @return array
     */
    protected function getQuery(string $token): array
    {
        if ($this->getConfig('mode') !== 'query') {
            return [];
        }

        return [
            $this->getConfig('queryParam') => $token,
        ];
    }

    /**
     * Get OAuth2 request headers
     *
     * @param string $token Access token.
     * @return array
     */
    protected function getHeaders(string $token): array
    {
        if ($this->getConfig('mode') !== 'header') {
            return [];
        }

        return [
            $this->getConfig('header') => sprintf('%s %s', $this->getConfig('headerPrefix'), $token),
        ];
    }
}

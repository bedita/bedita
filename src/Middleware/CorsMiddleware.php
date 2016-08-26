<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Middleware;

use BEdita\API\Network\CorsBuilder;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ForbiddenException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handle cross-origin HTTP requests setting the proper headers.
 *
 * The response of preflight request (OPTIONS) is delivered directly after the headers are applied.
 * For simple requests the CORS headers are applied to the response then it is passed to next middleware.
 *
 * @since 4.0.0
 */
class CorsMiddleware
{
    /**
     * CORS configuration
     *
     * where:
     *   - 'allowOrigin' is a single domain or an array of domains
     *   - 'allowMethods' is an array of HTTP methods (it's applied only to preflight requests)
     *   - 'allowHeaders' is an array of HTTP headers (it's applied only to preflight requests)
     *   - 'allowCredentials' enable cookies to be sent in CORS requests
     *   - 'exposeHeaders' is an array of headers that a client library/browser can expose to scripting
     *   - 'maxAge' is the max-age preflight OPTIONS requests are valid for (it's applied only to preflight requests)
     *
     * When value is falsy the related configuration is skipped.
     *
     * @var array
     */
    protected $corsConfig = [
        'allowOrigin' => false,
        'allowMethods' => false,
        'allowHeaders' => false,
        'allowCredentials' => false,
        'exposeHeaders' => false,
        'maxAge' => false,
    ];

    /**
     * Constructor
     *
     * Setup CORS using `$corsConfig` array
     *
     * @see self::corsConfig
     * @param array|null $corsConfig CORS configuration
     * @return void
     */
    public function __construct($corsConfig = null)
    {
        if (empty($corsConfig) || !is_array($corsConfig)) {
            return;
        }

        $allowedConfig = array_intersect_key($corsConfig, $this->corsConfig);
        $this->corsConfig = $allowedConfig + $this->corsConfig;
    }

    /**
     * If the request is a preflight send the response applying CORS rules.
     * If it is a simple request it applies CORS rules to the response and call next middleware
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @param callable $next The next middleware to call.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        try {
            if ($request->getMethod() == 'OPTIONS') {
                return $this->preflight($request, $response);
            }

            $response = $this->buildCors($request, $response);

            return $next($request, $response);
        } catch (\Exception $e) {
            return $response->withStatus($e->getCode());
        }
    }

    /**
     * Prepare the response for a preflight request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @return \Psr\Http\Message\ResponseInterface A response.
     * @throws \Cake\Network\Exception\BadRequestException When the request is malformed
     * @throws \Cake\Network\Exception\ForbiddenException When no CORS rule matches the preflight request
     */
    protected function preflight(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (!$request->hasHeader('Origin')) {
            throw new BadRequestException('Preflight request missing of "Origin" header');
        }

        $accessControlRequestMethod = $request->getHeaderLine('Access-Control-Request-Method');
        if (empty($accessControlRequestMethod)) {
            throw new BadRequestException('Preflight request missing of "Access-Control-Request-Method" header');
        }

        $allowedMethods = (array)$this->corsConfig['allowMethods'];
        if (!in_array($accessControlRequestMethod, $allowedMethods)) {
            throw new ForbiddenException('Preflight request refused. Access-Control-Request-Method not allowed');
        }

        $accessControlRequestHeaders = explode(', ', strtolower($request->getHeaderLine('Access-Control-Request-Headers')));
        $allowedHeaders = array_map(
            function ($header) {
                return strtolower($header);
            },
            (array)$this->corsConfig['allowHeaders']
        );

        $notAllowedHeaders = array_diff($accessControlRequestHeaders, $allowedHeaders);
        if (!empty($notAllowedHeaders)) {
            throw new ForbiddenException(
                'Preflight request refused. Access-Control-Request-Headers not allowed for ' . implode(', ', $notAllowedHeaders)
            );
        }

        return $this->buildCors($request, $response, true);
    }

    /**
     * Build response headers following CORS configuration
     * and return the new response
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @param bool $preflight If the request is a preflight
     * @return \Psr\Http\Message\ResponseInterface A response.
     * @throws \Cake\Network\Exception\ForbiddenException When origin
     */
    protected function buildCors(ServerRequestInterface $request, ResponseInterface $response, $preflight = false)
    {
        $origin = $request->getHeaderLine('Origin');
        $isSsl = ($request->getUri()->getScheme() == 'https');

        $corsBuilder = new CorsBuilder($response, $origin, $isSsl);

        $options = array_filter($this->corsConfig);
        if (!$preflight) {
            $options = array_diff_key($options, array_flip(['allowMethods', 'allowHeaders', 'maxAge']));
        }

        foreach ($options as $corsOption => $corsValue) {
            $corsBuilder->{$corsOption}($corsValue);
        }

        $response = $corsBuilder->build();
        if (!empty($origin) && !$response->hasHeader('Access-Control-Allow-Origin')) {
            throw new ForbiddenException('Origin not allowed');
        }

        return $response;
    }
}

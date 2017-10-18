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

use Cake\Http\Response;
use Cake\Log\Log;
use Cake\Log\LogTrait;
use Cake\Network\CorsBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Middleware to trace analytics data
 *
 * @since 4.0.0
 */
class AnalyticsMiddleware
{
    use LogTrait;

    /**
     * Request start time
     *
     * @var float
     */
    protected $startTime = null;

    /**
     * Configure analytics logger if not configured yet
     *
     * @return void
     */
    public function __construct()
    {
        $this->startTime = microtime(true);
        if (!in_array('analytics', Log::configured())) {
            Log::config('analytics', [
                'className' => 'Cake\Log\Engine\FileLog',
                'path' => LOGS,
                'levels' => ['info'],
                'scopes' => ['analytics'],
                'file' => 'analyitics',
            ]);
        }
    }

    /**
     * The middleware action.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @param callable $next The next middleware to call.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $response = $next($request, $response);
        $end = microtime(true);

        $message = sprintf('Request time for %s: %f seconds', (string)$request->getUri(), $end - $this->startTime);
        $this->log($message, 'info', ['analytics']);

        return $response;
    }
}

<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Middleware;

use BEdita\Core\State\CurrentApplication;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Log\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Middleware to trace analytics data
 *
 * @since 4.0.0
 */
class AnalyticsMiddleware
{
    /**
     * Request start time
     *
     * @var float
     */
    protected $startTime = null;

    /**
     * Analytics data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Registered callbacks fo analytics extensions
     *
     * @var array
     */
    protected static $callbacks = [];

    /**
     * Configure analytics logger if not configured yet
     *
     * @return void
     */
    public function __construct()
    {
        $this->startTime = microtime(true);
        if (defined('TIME_START')) {
            $this->startTime = TIME_START;
        }

        if (!in_array('analytics', Log::configured())) {
            Log::config('analytics', [
                'className' => 'File',
                'path' => LOGS,
                'scopes' => ['analytics'],
                'file' => 'analytics',
            ]);
        }
    }

    /**
     * Getter for $startTime
     *
     * @return float
     * @codeCoverageIgnore
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Getter for $data
     *
     * @return float
     * @codeCoverageIgnore
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Register new analytics extension callback
     *
     * @param mixed $newCallback Callback to register
     * @return void
     */
    public static function registerCallback($newCallback)
    {
        if (!is_callable($newCallback)) {
            Log::warning('Bad callback ' . print_r($newCallback, true));

            return;
        }
        static::$callbacks[] = $newCallback;
    }

    /**
     * Get analytics custom data from registered callbacks
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @return array
     */
    protected function readCallbackData(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (empty(static::$callbacks)) {
            return [];
        }

        $res = [];
        $params = [$request, $response];
        foreach (static::$callbacks as $call) {
            try {
                $values = call_user_func_array($call, $params);
                $res[] = $values;
            } catch (Throwable $t) {
                Log::error('Error calling callback ' . print_r($call, true));
            }
        }

        return $res;
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

        /**
         * TODO: add custom application error code
         */
        $this->data = [
            'r' => $request->getEnv('REQUEST_TIME'),
            'a' => CurrentApplication::getApplicationId(),
            'usr' => LoggedUser::id(),
            'm' => $request->getMethod(),
            'url' => $request->getUri()->getPath(),
            'q' => $request->getUri()->getQuery(),
            's' => $response->getStatusCode(),
            'x' => $this->readCallBackData($request, $response),
            'e' => round(microtime(true) - $this->startTime, 4, PHP_ROUND_HALF_EVEN),
        ];

        Log::info(json_encode($this->data), 'analytics');

        return $response;
    }
}

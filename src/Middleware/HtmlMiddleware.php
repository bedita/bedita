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

use Cake\Network\Request;
use Cake\View\ViewVarsTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Intercept HTML requests and render an user-friendly view.
 *
 * @since 4.0.0
 */
class HtmlMiddleware
{

    use ViewVarsTrait;

    /**
     * Render HTML requests using a user-friendly template.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object.
     * @param \Psr\Http\Message\ResponseInterface $response Response object.
     * @param callable $next Next middleware in queue.
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        if (!($request instanceof Request) || !$request->is('html')) {
            // Not an HTML request, or unable to detect easily.
            return $next($request, $response);
        }

        // Set correct "Accept" header, and proceed as usual.
        $request = $request->withHeader('Accept', 'application/vnd.api+json');

        /* @var \Cake\Network\Response $response */
        $response = $next($request, $response);

        if (!in_array($response->getHeaderLine('Content-Type'), ['application/json', 'application/vnd.api+json'])) {
            // Not a JSON response.
            return $response;
        }

        // Prepare HTML rendering.
        $this->viewBuilder()
            ->setPlugin('BEdita/API')
            ->setLayout('html')
            ->setTemplatePath('Common')
            ->setTemplate('html');
        $this->set(compact('request', 'response'));
        $view = $this->createView();

        $response = $response
            ->withType('html')
            ->withStringBody($view->render());

        return $response;
    }
}

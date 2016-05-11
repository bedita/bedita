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
namespace BEdita\API\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\NotAcceptableException;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;

/**
 * Base class for all API Controller endpoints.
 *
 * @since 4.0.0
 *
 * @property \BEdita\API\Controller\Component\JsonApiComponent $JsonApi
 */
class AppController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        if ($this->request->is(['json', 'jsonApi'])) {
            $this->RequestHandler->config('viewClassMap.json', 'BEdita/API.JsonApi');
            $this->loadComponent('BEdita/API.JsonApi', [
                'contentType' => $this->request->is('json') ? 'json' : null,
                'checkMediaType' => $this->request->is('jsonApi'),
            ]);
        }

        if (empty(Router::fullBaseUrl())) {
            Router::fullBaseUrl(
                rtrim(
                    sprintf('%s://%s/%s', $this->request->scheme(), $this->request->host(), $this->request->base),
                    '/'
                )
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        if ((Configure::read('debug') || Configure::read('Accept.html')) && $this->request->is('html')) {
            return $this->html();
        } elseif (!$this->request->is(['json', 'jsonApi'])) {
            throw new NotAcceptableException('Bad request content type "' . implode('" "', $this->request->accepts()) . '"');
        }

        return null;
    }

    /**
     * Action to display HTML layout.
     *
     * @return \Cake\Network\Response
     * @throws \Cake\Network\Exception\NotFoundException
     */
    protected function html()
    {
        if ($this->request->is('requested')) {
            throw new NotFoundException();
        }

        $method = $this->request->method();
        $url = $this->request->here;
        $response = $this->requestAction($this->request->params, [
            'environment' => [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
        ]);

        $this->set(compact('method', 'response', 'url'));

        $this->viewBuilder()->template('Common/html');

        return $this->render();
    }
}

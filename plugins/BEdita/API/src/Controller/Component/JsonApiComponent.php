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
namespace BEdita\API\Controller\Component;

use BEdita\API\Exception\UnsupportedMediaTypeException;
use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Routing\Router;

/**
 * Handles JSON API data format in input and in output
 *
 * @since 4.0.0
 *
 * @property \Cake\Controller\Component\RequestHandlerComponent $RequestHandler
 */
class JsonApiComponent extends Component
{
    /**
     * JSON API content type.
     *
     * @var string
     */
    const CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * {@inheritDoc}
     */
    public $components = ['RequestHandler'];

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'contentType' => null,
        'checkMediaType' => true,
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        $contentType = self::CONTENT_TYPE;
        if (!empty($config['contentType'])) {
            $contentType = $this->response->getMimeType($config['contentType']) ?: $config['contentType'];
        }
        $this->response->type([
            'jsonApi' => $contentType,
        ]);

        $this->RequestHandler->config('inputTypeMap.jsonApi', [[$this, 'parseInput']]);
        $this->RequestHandler->config('viewClassMap.jsonApi', 'BEdita/API.JsonApi');
    }

    /**
     * Input data parser for JSON API format.
     *
     * @return array
     */
    public function parseInput()
    {
        return [];
    }

    /**
     * Set occurred error.
     *
     * @param int $status HTTP error code.
     * @param string $title Brief description of error.
     * @param string $description Long description of error
     * @param array|null $meta Additional metadata about error.
     * @return void
     */
    public function error($status, $title, $description, array $meta = null)
    {
        $controller = $this->_registry->getController();

        $status = (string)$status;

        $error = compact('status', 'title', 'description', 'meta');
        $error = array_filter($error);

        $controller->set('_error', $error);
    }

    /**
     * Get links according to JSON API specifications.
     *
     * @return array
     */
    public function getLinks()
    {
        $links = [
            'self' => Router::url(null, true),
        ];

        if (!empty($this->request->params['paging']) && is_array($this->request->params['paging'])) {
            $paging = reset($this->request->params['paging']);
            $lastPage = ($paging['pageCount'] > 1) ? $paging['pageCount'] : null;
            $prevPage = ($paging['page'] > 2) ? $paging['page'] - 1 : null;
            $nextPage = $paging['page'] + 1;

            $links['first'] = Router::url(['page' => null, '_method' => 'GET'], true);
            $links['last'] = Router::url(['page' => $lastPage, '_method' => 'GET'], true);
            $links['prev'] = $paging['prevPage'] ? Router::url(['page' => $prevPage, '_method' => 'GET'], true) : null;
            $links['next'] = $paging['nextPage'] ? Router::url(['page' => $nextPage, '_method' => 'GET'], true) : null;
        }

        return $links;
    }

    /**
     * Get common metadata.
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = [];

        if (!empty($this->request->params['paging']) && is_array($this->request->params['paging'])) {
            $paging = reset($this->request->params['paging']);
            $paging += [
                'current' => null,
                'page' => null,
                'count' => null,
                'perPage' => null,
                'pageCount' => null,
            ];

            $meta['pagination'] = [
                'count' => $paging['count'],
                'page' => $paging['page'],
                'page_count' => $paging['pageCount'],
                'page_items' => $paging['current'],
                'page_size' => $paging['perPage'],
            ];
        }

        return $meta;
    }

    /**
     * Perform preliminary checks and operations.
     *
     * @return void
     * @throws \BEdita\API\Exception\UnsupportedMediaTypeException Throws an exception if the `Accept` header does not
     *      comply to JSON API specifications and `checkMediaType` configuration is enabled.
     */
    public function beforeFilter()
    {
        if ($this->config('checkMediaType') && trim($this->request->header('accept')) != self::CONTENT_TYPE) {
            // http://jsonapi.org/format/#content-negotiation-servers
            throw new UnsupportedMediaTypeException('Bad request content type "' . implode('" "', $this->request->accepts()) . '"');
        }
    }

    /**
     * Perform operations before view rendering.
     *
     * @param \Cake\Event\Event $event Triggered event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        $controller = $event->subject();
        if (!($controller instanceof Controller)) {
            return;
        }

        $links = [];
        if (isset($controller->viewVars['_links'])) {
            $links = (array)$controller->viewVars['_links'];
        }
        $links += $this->getLinks();

        $meta = [];
        if (isset($controller->viewVars['_meta'])) {
            $meta = (array)$controller->viewVars['_meta'];
        }
        $meta += $this->getMeta();

        $controller->set([
            '_links' => $links,
            '_meta' => $meta,
        ]);

        $this->RequestHandler->renderAs($controller, 'jsonApi');
    }
}

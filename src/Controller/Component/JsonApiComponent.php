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

use BEdita\API\Network\Exception\UnsupportedMediaTypeException;
use BEdita\API\Utility\JsonApi;
use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ConflictException;
use Cake\Network\Exception\ForbiddenException;
use Cake\Routing\Router;
use Cake\Utility\Hash;

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
        'resourceTypes' => null,
        'clientGeneratedIds' => false,
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        $contentType = self::CONTENT_TYPE;
        if (!empty($config['contentType'])) {
            $contentType = $this->getController()->response->getMimeType($config['contentType']) ?: $config['contentType'];
        }
        $this->getController()->response->type([
            'jsonapi' => $contentType,
        ]);

        $this->RequestHandler->setConfig('inputTypeMap.jsonapi', [[$this, 'parseInput']]); // Must be lowercase because reasons.
        $this->RequestHandler->setConfig('viewClassMap.jsonapi', 'BEdita/API.JsonApi');
    }

    /**
     * Input data parser for JSON API format.
     *
     * @param string $json JSON string.
     * @return array JSON API input data array
     * @throws \Cake\Network\Exception\BadRequestException When the request is malformed
     */
    public function parseInput($json)
    {
        try {
            $json = json_decode($json, true);
            if (json_last_error() || !is_array($json) || !isset($json['data'])) {
                throw new BadRequestException(__d('bedita', 'Invalid JSON input'));
            }

            return JsonApi::parseData((array)$json['data']);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestException([
                'title' => __d('bedita', 'Bad JSON input'),
                'detail' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Set occurred error.
     *
     * @param int $status HTTP error code.
     * @param string $title Brief description of error.
     * @param string $detail Long description of error
     * @param array|null $meta Additional metadata about error.
     * @return void
     */
    public function error($status, $title, $detail, array $meta = null)
    {
        $controller = $this->getController();

        $status = (string)$status;

        $error = compact('status', 'title', 'detail', 'meta');
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
        $request = $this->getController()->request->withParam('pass', []);
        $links = [
            'self' => Router::reverse($request, true),
            'home' => Router::url(['_name' => 'api:home'], true),
        ];

        $paging = $request->getParam('paging');
        if (!empty($paging) && is_array($paging)) {
            $paging = reset($paging);
            $query = $request->getQueryParams();

            $query['page'] = null;
            $links['first'] = Router::reverse($request->withQueryParams($query), true);

            $query['page'] = ($paging['pageCount'] > 1) ? $paging['pageCount'] : null;
            $links['last'] = Router::reverse($request->withQueryParams($query), true);

            $links['prev'] = null;
            if ($paging['prevPage']) {
                $query['page'] = ($paging['page'] > 2) ? $paging['page'] - 1 : null;
                $links['prev'] = Router::reverse($request->withQueryParams($query), true);
            }

            $links['next'] = null;
            if ($paging['nextPage']) {
                $query['page'] = $paging['page'] + 1;
                $links['next'] = Router::reverse($request->withQueryParams($query), true);
            }
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

        $paging = $this->getController()->request->getParam('paging');
        if (!empty($paging) && is_array($paging)) {
            $paging = reset($paging);
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
     * Check if given resource types are allowed.
     *
     * @param mixed $types One or more allowed types to check resources array against.
     * @param array|null $data Data to be checked. By default, this is taken from the request.
     * @return void
     * @throws \Cake\Network\Exception\ConflictException Throws an exception if a resource has a non-supported `type`.
     */
    protected function allowedResourceTypes($types, array $data = null)
    {
        $data = ($data === null) ? $this->getController()->request->getData() : $data;
        if (!$data || !$types) {
            return;
        }
        $data = (array)$data;
        $types = (array)$types;

        if (Hash::numeric(array_keys($data))) {
            foreach ($data as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $this->allowedResourceTypes($types, $item);
            }

            return;
        }

        if (empty($data['type']) || !in_array($data['type'], $types)) {
            throw new ConflictException('Unsupported resource type');
        }
    }

    /**
     * Check that no resource includes a client-generated ID, if this feature is unsupported.
     *
     * @param bool $allow Should client-generated IDs be allowed?
     * @param array|null $data Data to be checked. By default, this is taken from the request.
     * @return void
     * @throws \Cake\Network\Exception\ForbiddenException Throws an exception if a resource has a client-generated
     *      ID, but this feature is not supported.
     */
    protected function allowClientGeneratedIds($allow = true, array $data = null)
    {
        $data = ($data === null) ? $this->getController()->request->getData() : $data;
        if (!$data || $allow) {
            return;
        }
        $data = (array)$data;

        if (Hash::numeric(array_keys($data))) {
            foreach ($data as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $this->allowClientGeneratedIds($allow, $item);
            }

            return;
        }

        if (!empty($data['id'])) {
            throw new ForbiddenException('Client-generated IDs are not supported');
        }
    }

    /**
     * Perform preliminary checks and operations.
     *
     * @param \Cake\Event\Event $event Triggered event.
     * @return void
     * @throws \BEdita\API\Network\Exception\UnsupportedMediaTypeException Throws an exception if the `Accept` header
     *      does not comply to JSON API specifications and `checkMediaType` configuration is enabled.
     * @throws \Cake\Network\Exception\ConflictException Throws an exception if a resource in the payload has a
     *      non-supported `type`.
     * @throws \Cake\Network\Exception\ForbiddenException Throws an exception if a resource in the payload includes a
     *      client-generated ID, but the feature is not supported.
     */
    public function startup(Event $event)
    {
        $controller = $event->getSubject();
        if (!($controller instanceof Controller)) {
            return;
        }

        $this->RequestHandler->renderAs($controller, 'jsonapi');

        if ($this->getConfig('checkMediaType') && trim($controller->request->getHeaderLine('accept')) != self::CONTENT_TYPE) {
            // http://jsonapi.org/format/#content-negotiation-servers
            throw new UnsupportedMediaTypeException('Bad request content type "' . implode('" "', $controller->request->accepts()) . '"');
        }

        if ($controller->request->is(['post', 'patch'])) {
            $this->allowedResourceTypes($this->getConfig('resourceTypes'));
        }

        if ($controller->request->is('post') && !$this->getConfig('clientGeneratedIds')) {
            $this->allowClientGeneratedIds(false);
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
        $controller = $event->getSubject();
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
    }
}

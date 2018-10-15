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

use BadMethodCallException;
use BEdita\API\Datasource\JsonApiPaginator;
use BEdita\Core\State\CurrentApplication;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ForbiddenException;
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
    public $paginate = [
        'maxLimit' => 100,
        'order' => [
            'id' => 'asc',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->response = $this->response->withHeader('X-BEdita-Version', Configure::read('BEdita.version'));

        $this->getApplication();

        $this->loadComponent('Paginator', (array)Configure::read('Pagination'));
        $this->loadComponent('RequestHandler');
        if ($this->request->is(['json', 'jsonapi'])) {
            $this->loadComponent('BEdita/API.JsonApi', [
                'contentType' => $this->request->is('json') ? 'json' : null,
                'checkMediaType' => $this->request->is('jsonapi'),
            ]);
            $this->Paginator->setPaginator((new JsonApiPaginator())->setConfig($this->Paginator->getConfig()));

            $this->RequestHandler->setConfig('inputTypeMap.json', [[$this->JsonApi, 'parseInput']], false);
            $this->RequestHandler->setConfig('viewClassMap.json', 'BEdita/API.JsonApi');
        }

        $this->loadComponent('Auth', [
            'authenticate' => ['BEdita/API.Jwt', 'BEdita/API.Anonymous'],
            'authorize' => [
                'BEdita/API.Endpoint' => [
                    'blockAnonymousUsers' => Configure::read('Security.blockAnonymousUsers'),
                ],
            ],
            'loginAction' => ['_name' => 'api:login'],
            'loginRedirect' => ['_name' => 'api:login'],
            'unauthorizedRedirect' => false,
            'storage' => 'Memory',
        ]);

        if (empty(Router::fullBaseUrl())) {
            Router::fullBaseUrl(
                rtrim(
                    sprintf('%s://%s/%s', $this->request->scheme(), $this->request->host(), $this->request->getAttribute('base')),
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
        if (!$this->request->is(['json', 'jsonapi'])) {
            throw new NotAcceptableException(
                __d('bedita', 'Bad request content type "{0}"', $this->request->getHeaderLine('Accept'))
            );
        }

        return null;
    }

    /**
     * Get application from request.
     * This is done primarily with an API_KEY header like 'X-Api-Key',
     * alternatively `api_key` query string is used (not recommended)
     *
     * @return void
     * @throws \Cake\Network\Exception\ForbiddenException Throws an exception if API key is missing or invalid.
     */
    protected function getApplication()
    {
        if (CurrentApplication::getApplication() === null) {
            $apiKey = $this->request->getHeaderLine('X-Api-Key');
            if (empty($apiKey)) {
                $apiKey = (string)$this->request->getQuery('api_key');
            }
            if (empty($apiKey) && empty(Configure::read('Security.blockAnonymousApps'))) {
                return;
            }

            try {
                CurrentApplication::setFromApiKey($apiKey);
            } catch (BadMethodCallException $e) {
                throw new ForbiddenException(__d('bedita', 'Missing API key'));
            } catch (RecordNotFoundException $e) {
                throw new ForbiddenException(__d('bedita', 'Invalid API key'));
            }
        }
    }

    /**
     * Prepare a list of associations to be contained from `?include` query parameter.
     *
     * @param string|array|null $include Association(s) to be included.
     * @return array
     * @throws \Cake\Network\Exception\BadRequestException Throws an exception if a
     */
    protected function prepareInclude($include)
    {
        if ($include === null) {
            return [];
        }
        if (!is_string($include)) {
            throw new BadRequestException(
                __d('bedita', 'Invalid "{0}" query parameter ({1})', 'include', __d('bedita', 'Must be a comma-separated string'))
            );
        }

        $contain = [];
        $include = array_filter(array_map('trim', explode(',', $include)));
        foreach ($include as $relationship) {
            if (strpos($relationship, '.') !== false) {
                throw new BadRequestException(__d('bedita', 'Inclusion of nested resources is not yet supported'));
            }

            try {
                $association = $this->findAssociation($relationship);
            } catch (NotFoundException $e) {
                throw new BadRequestException(
                    __d('bedita', 'Invalid "{0}" query parameter ({1})', 'include', __d('bedita', 'Relationship "{0}" does not exist', $relationship))
                );
            }

            $contain[] = $association->getName();
        }

        return $contain;
    }

    /**
     * Find the association corresponding to the relationship name.
     * Subclasses need to override this method.
     *
     * @param string $relationship Relationship name.
     * @return \Cake\ORM\Association|void
     * @throws \Cake\Network\Exception\NotFoundException Throws an exception if no suitable association could be found.
     * @codeCoverageIgnore
     */
    protected function findAssociation($relationship)
    {
        throw new NotFoundException(__d('bedita', 'Relationship "{0}" does not exist', $relationship));
    }
}

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

use BEdita\API\Datasource\JsonApiPaginator;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotAcceptableException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Association;
use Cake\ORM\Table;
use Cake\Routing\Router;

/**
 * Base class for all API Controller endpoints.
 *
 * @since 4.0.0
 * @property \BEdita\API\Controller\Component\JsonApiComponent $JsonApi
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 */
class AppController extends Controller
{
    /**
     * @inheritDoc
     */
    public $paginate = [
        'order' => [
            'id' => 'asc',
        ],
        'limit' => 20,
        'maxLimit' => 100,
    ];

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->response = $this->response->withHeader('X-BEdita-Version', Configure::read('BEdita.version'));

        $this->paginate = (array)Configure::read('Pagination') + $this->paginate;
        $this->loadComponent('RequestHandler');
        if ($this->request->is(['json', 'jsonapi'])) {
            $this->loadComponent('BEdita/API.JsonApi', [
                'contentType' => $this->request->is('json') ? 'json' : null,
                'checkMediaType' => $this->request->is('jsonapi'),
            ]);

            $this->paginate['className'] = JsonApiPaginator::class;
        }

        $this->loadComponent('Authentication.Authentication', [
            'requireIdentity' => $this->isIdentityRequired(),
        ]);

        $this->loadComponent('Authorization.Authorization');

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
     * Is identity required?
     *
     * @return bool
     */
    protected function isIdentityRequired(): bool
    {
        if (in_array($this->request->getMethod(), ['GET', 'HEAD'])) {
            return (bool)Configure::read('Security.blockAnonymousUsers', false);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->checkAcceptable();

        // Internally it may throw an `UnauthorizedException` for anonymous users
        $this->Authorization->authorize($this->request, 'access');

        return null;
    }

    /**
     * Perform HTTP Content Negotiation using `Accept` header.
     *
     * @return void
     * @throws \Cake\Http\Exception\NotAcceptableException If request isn't accetable
     */
    protected function checkAcceptable(): void
    {
        if (!$this->request->is(['json', 'jsonapi'])) {
            throw new NotAcceptableException(
                __d('bedita', 'Bad request content type "{0}"', $this->request->getHeaderLine('Accept'))
            );
        }
    }

    /**
     * Prepare a list of associations to be contained from `?include` query parameter.
     *
     * @param string|array|null $include Association(s) to be included.
     * @param \Cake\ORM\Table|null $table Table to consider.
     * @return array
     * @throws \Cake\Http\Exception\BadRequestException Throws an exception if a
     */
    protected function prepareInclude($include, ?Table $table = null): array
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
                $association = $this->findAssociation($relationship, $table);
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
     * @param \Cake\ORM\Table|null $table Table to consider.
     * @return \Cake\ORM\Association
     * @throws \Cake\Http\Exception\NotFoundException Throws an exception if no suitable association could be found.
     * @codeCoverageIgnore
     */
    protected function findAssociation(string $relationship, ?Table $table = null): Association
    {
        throw new NotFoundException(__d('bedita', 'Relationship "{0}" does not exist', $relationship));
    }

    /**
     * Cake 4 compatibility wrapper method: set items to serialize for the view
     *
     * In Cake 3 => $this->set('_serialize', ['data']);
     * In Cake 4 => $this->viewBuilder()->setOption('serialize', ['data'])
     *
     * @param array $items Items to serialize
     * @return void
     * @codeCoverageIgnore
     */
    protected function setSerialize(array $items): void
    {
        $this->viewBuilder()->setOption('serialize', $items);
    }
}

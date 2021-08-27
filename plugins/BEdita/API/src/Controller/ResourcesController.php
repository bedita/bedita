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

use BEdita\API\Model\Action\UpdateAssociatedAction;
use BEdita\Core\Model\Action\AddAssociatedAction;
use BEdita\Core\Model\Action\DeleteEntityAction;
use BEdita\Core\Model\Action\GetEntityAction;
use BEdita\Core\Model\Action\ListAssociatedAction;
use BEdita\Core\Model\Action\ListEntitiesAction;
use BEdita\Core\Model\Action\RemoveAssociatedAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use BEdita\Core\Model\Action\SetAssociatedAction;
use BEdita\Core\Utility\JsonApiSerializable;
use Cake\Core\InstanceConfigTrait;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\ConflictException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Association;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\HasOne;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;

/**
 * Base controller for CRUD actions on generic resources.
 *
 * @since 4.0.0
 */
abstract class ResourcesController extends AppController
{
    use InstanceConfigTrait;

    /**
     * Configuration.
     *
     * Available configurations are:
     *  - `allowedAssociations`: an associative array of relationships names, and
     *      an array of allowed resource types for that relationship.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [],
    ];

    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        if (isset($this->JsonApi)) {
            $this->JsonApi->setConfig('resourceTypes', [Inflector::underscore($this->name)]);

            if ($this->request->getParam('action') === 'relationships') {
                $this->JsonApi->setConfig(
                    'resourceTypes',
                    $this->getConfig(sprintf('allowedAssociations.%s', $this->request->getParam('relationship'))),
                    false
                );
                $this->JsonApi->setConfig('clientGeneratedIds', true);
            }
        }

        $this->Table = TableRegistry::getTableLocator()->get($this->modelClass);
    }

    /**
     * Find the association corresponding to the relationship name.
     *
     * @param string $relationship Relationship name.
     * @param \Cake\ORM\Table|null $table Table to consider.
     * @return \Cake\ORM\Association
     * @throws \Cake\Http\Exception\NotFoundException Throws an exception if no association could be found.
     */
    protected function findAssociation(string $relationship, ?Table $table = null): Association
    {
        $relationship = Inflector::underscore($relationship);
        $allowed = true;
        if ($table === null) {
            $table = $this->Table;
            $allowed = array_key_exists($relationship, $this->getConfig('allowedAssociations'));
        }

        if ($allowed) {
            $association = $table->associations()->getByProperty($relationship);
            if ($association !== null) {
                return $association;
            }
        }

        throw new NotFoundException(__d('bedita', 'Relationship "{0}" does not exist', $relationship));
    }

    /**
     * List and add entities.
     *
     * This action represents a collection of resources.
     * If the request is a `POST` request, this action creates a new resource.
     *
     * @return void
     */
    public function index()
    {
        $this->request->allowMethod(['get', 'post']);

        if ($this->request->is('post')) {
            // Add a new entity.
            $entity = $this->Table->newEntity();
            $action = new SaveEntityAction(['table' => $this->Table]);

            $data = $this->request->getData();
            $data = $action(compact('entity', 'data'));

            $action = new GetEntityAction(['table' => $this->Table]);
            $primaryKey = $this->Table->getPrimaryKey();
            $data = $action(['primaryKey' => $data->get($primaryKey)]);

            $this->response = $this->response
                ->withStatus(201)
                ->withHeader(
                    'Location',
                    $this->resourceUrl($data, $primaryKey)
                );
        } else {
            // List existing entities.
            $filter = (array)$this->request->getQuery('filter') + array_filter(['query' => $this->request->getQuery('q')]);
            $contain = $this->prepareInclude($this->request->getQuery('include'));

            $action = new ListEntitiesAction(['table' => $this->Table]);
            $query = $action(compact('filter', 'contain'));

            $this->set('_fields', $this->request->getQuery('fields', []));
            $data = $this->paginate($query);
        }

        $this->set(compact('data'));
        $this->set('_serialize', ['data']);
    }

    /**
     * Resource URL of a newly created entity
     *
     * @param \Cake\Datasource\EntityInterface $entity Resource entity
     * @param string $primaryKey Primary key name
     * @return string Requested URL
     */
    protected function resourceUrl(EntityInterface $entity, $primaryKey)
    {
        $prefix = 'api:resources';
        if ($entity instanceof JsonApiSerializable) {
            $prefix = $entity->routeNamePrefix();
        }

        return Router::url(
            [
                '_name' => $prefix . ':resource',
                'controller' => $this->name,
                'id' => $entity->get($primaryKey),
            ],
            true
        );
    }

    /**
     * View and manage single entities.
     *
     * This action represents a single resource.
     * If the request is a `PATCH` request, this action updates an existing resource.
     * If the request is a `DELETE` request, this action deletes an existing resource.
     *
     * @param mixed $id Entity ID.
     * @return \Cake\Http\Response|null
     */
    public function resource($id)
    {
        $this->request->allowMethod(['get', 'patch', 'delete']);

        $contain = $this->prepareInclude($this->request->getQuery('include'));

        $action = new GetEntityAction(['table' => $this->Table]);
        $entity = $action(['primaryKey' => $id, 'contain' => $contain]);

        if ($this->request->is('delete')) {
            // Delete an entity.
            $action = new DeleteEntityAction(['table' => $this->Table]);

            if (!$action(compact('entity'))) {
                throw new InternalErrorException(__d('bedita', 'Delete failed'));
            }

            return $this->response
                ->withStatus(204);
        }

        if ($this->request->is('patch')) {
            // Patch an existing entity.
            if ($this->request->getData('id') !== $id) {
                throw new ConflictException(__d('bedita', 'IDs don\'t match'));
            }

            $action = new SaveEntityAction(['table' => $this->Table]);

            $data = $this->request->getData();
            $entity = $action(compact('entity', 'data'));
        }

        $this->set('_fields', $this->request->getQuery('fields', []));
        $this->set(compact('entity'));
        $this->set('_serialize', ['entity']);

        return null;
    }

    /**
     * Paginated list of related resources.
     *
     * This action represents a collection of related resources.
     *
     * @return void
     */
    public function related()
    {
        $this->request->allowMethod(['get']);

        $relationship = $this->request->getParam('relationship');
        $relatedId = $this->request->getParam('related_id');

        $association = $this->findAssociation($relationship);
        $filter = (array)$this->request->getQuery('filter') + array_filter(['query' => $this->request->getQuery('q')]);
        $contain = $this->prepareInclude($this->request->getQuery('include'), $association->getTarget());

        $action = $this->getAssociatedAction($association);
        $data = $action->execute(['primaryKey' => $relatedId] + compact('filter', 'contain'));

        if ($data instanceof Query) {
            $data = $this->paginate($data);
        }

        $this->set('_fields', $this->request->getQuery('fields', []));
        $this->set(compact('data'));
        $this->set('_serialize', ['data']);

        $available = $this->getAvailableUrl($relationship);
        $this->set('_links', compact('available'));
    }

    /**
     * Get the action instance to get list of associated entities.
     *
     * @param \Cake\ORM\Association $association The association to use.
     * @return \BEdita\Core\Model\Action\ListAssociatedAction
     */
    protected function getAssociatedAction(Association $association)
    {
        return new ListAssociatedAction(compact('association'));
    }

    /**
     * Set allowed methods for relationships
     *
     * @param \Cake\ORM\Association $association The association.
     * @return void
     */
    protected function setRelationshipsAllowedMethods(Association $association)
    {
        $allowedMethods = ['get', 'post', 'patch', 'delete'];
        if ($association instanceof BelongsTo || $association instanceof HasOne) {
            // For to-one relationship, POST and DELETE are not implemented.
            $allowedMethods = ['get', 'patch'];
        }
        $this->request->allowMethod($allowedMethods);
    }

    /**
     * View and manage relationships.
     *
     * This action represents a collection of relationships.
     * If the request is a `PATCH` request, this action completely replaces the set of existing relationships.
     * If the request is a `POST` request, this action adds new relationships.
     * If the request is a `DELETE` request, this action deletes existing relationships.
     *
     * @return \Cake\Http\Response|null
     */
    public function relationships()
    {
        $id = $this->request->getParam('id');
        $relationship = $this->request->getParam('relationship');

        $association = $this->findAssociation($relationship);
        $this->setRelationshipsAllowedMethods($association);

        switch ($this->request->getMethod()) {
            case 'PATCH':
                $action = new SetAssociatedAction(compact('association'));
                break;

            case 'POST':
                $action = new AddAssociatedAction(compact('association'));
                break;

            case 'DELETE':
                $action = new RemoveAssociatedAction(compact('association'));
                break;

            case 'GET':
            default:
                $filter = (array)$this->request->getQuery('filter') + array_filter(['query' => $this->request->getQuery('q')]);

                $action = $this->getAssociatedAction($association);
                $data = $action(['primaryKey' => $id, 'list' => true, 'filter' => $filter]);

                if ($data instanceof Query) {
                    $data = $this->paginate($data);
                }

                $this->set(compact('data'));
                $this->set([
                    '_serialize' => ['data'],
                    '_jsonApiOptions' => JsonApiSerializable::JSONAPIOPT_EXCLUDE_ATTRIBUTES | JsonApiSerializable::JSONAPIOPT_EXCLUDE_META
                ]);

                $available = $this->getAvailableUrl($relationship);
                $this->set('_links', compact('available'));

                return null;
        }

        $action = new UpdateAssociatedAction(compact('action') + ['request' => $this->request]);
        $count = $action(['primaryKey' => $id]);

        if ($count === false) {
            throw new InternalErrorException(__d('bedita', 'Could not update relationship "{0}"', $relationship));
        }

        if ($count === 0) {
            return $this->response
                ->withStatus(204);
        }

        $this->set(['_serialize' => []]);

        return null;
    }

    /**
     * Return link to available objects by relationship.
     *
     * @param string $relationship Relationship name.
     * @return string|null
     */
    protected function getAvailableUrl($relationship)
    {
        $destinationEntity = $this->findAssociation($relationship)->getTarget()->newEntity();
        if (!($destinationEntity instanceof JsonApiSerializable)) {
            return null;
        }

        $jsonApiData = $destinationEntity->jsonApiSerialize(JsonApiSerializable::JSONAPIOPT_BASIC);
        if (empty($jsonApiData['type'])) {
            return null;
        }

        return Router::url(
            [
                '_name' => $destinationEntity->routeNamePrefix() . ':index',
                'controller' => Inflector::camelize($jsonApiData['type']),
            ],
            true
        );
    }
}

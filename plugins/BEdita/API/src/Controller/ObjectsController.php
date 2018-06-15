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
use BEdita\Core\Model\Action\AddRelatedObjectsAction;
use BEdita\Core\Model\Action\DeleteObjectAction;
use BEdita\Core\Model\Action\GetObjectAction;
use BEdita\Core\Model\Action\ListObjectsAction;
use BEdita\Core\Model\Action\ListRelatedObjectsAction;
use BEdita\Core\Model\Action\RemoveRelatedObjectsAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use BEdita\Core\Model\Action\SetRelatedObjectsAction;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Network\Exception\ConflictException;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Association;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Controller for `/objects` endpoint.
 *
 * @since 4.0.0
 */
class ObjectsController extends ResourcesController
{

    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Objects';

    /**
     * The referred object type entity filled when `object_type` request param is set and valid
     *
     * @var \BEdita\Core\Model\Entity\ObjectType
     */
    protected $objectType = null;

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'parents' => ['folders'],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        if (in_array($this->request->getParam('action'), ['related', 'relationships'])) {
            $name = $this->request->getParam('relationship');
            $allowedTypes = TableRegistry::get('ObjectTypes')
                ->find('list')
                ->find('byRelation', compact('name') + ['descendants' => true])
                ->toArray();

            $this->setConfig(sprintf('allowedAssociations.%s', $name), $allowedTypes);
        }

        $this->initObjectModel();

        $behaviorRegistry = $this->Table->behaviors();
        if ($behaviorRegistry->hasMethod('objectType')) {
            /** @var \BEdita\Core\Model\Entity\ObjectType $objectType */
            $objectType = $behaviorRegistry->call('objectType');
            $this->setConfig('allowedAssociations', array_fill_keys($objectType->relations, []));
        }

        // Requested object type endpoint MUST be `enabled`
        if (!$this->objectType->get('enabled')) {
            throw new MissingRouteException(['url' => $this->request->getRequestTarget()]);
        }

        parent::initialize();

        if (isset($this->JsonApi) && $this->request->getParam('action') !== 'relationships') {
            $this->JsonApi->setConfig('resourceTypes', [$this->objectType->name], false);
        }
    }

    /**
     * Init model related attributes:
     *  - $this->objectType
     *  - $this->modelClass
     *  - $this->Table
     *
     * @return void
     * @throws \Cake\Routing\Exception\MissingRouteException If `object_type` param is not valid
     */
    protected function initObjectModel()
    {
        $type = $this->request->getParam('object_type', Inflector::underscore($this->request->getParam('controller')));
        try {
            $this->objectType = TableRegistry::get('ObjectTypes')->get($type);
            if ($type !== $this->objectType->name) {
                $this->log(
                    sprintf('Bad object type name "%s", could be "%s"', $type, $this->objectType->name),
                    'warning',
                    ['request' => $this->request]
                );

                throw new MissingRouteException(__d(
                    'bedita',
                    'A route matching "{0}" could not be found. Did you mean "{1}"?',
                    $this->request->getRequestTarget(),
                    $this->objectType->name
                ));
            }
            $this->modelClass = $this->objectType->alias;
            $this->Table = TableRegistry::get($this->modelClass);
        } catch (RecordNotFoundException $e) {
            $this->log(sprintf('Object type "%s" does not exist', $type), 'warning', ['request' => $this->request]);

            throw new MissingRouteException(['url' => $this->request->getRequestTarget()]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        if ($this->request->getParam('action') === 'relationships'
            && $this->request->getParam('relationship') === 'streams'
            && !$this->request->is('get')
        ) {
            throw new ForbiddenException(__d(
                'bedita',
                'You are not authorized to manage an object relationship to streams, please update stream relationship to objects instead'
            ));
        }

        return parent::beforeFilter($event);
    }

    /**
     * {@inheritDoc}
     */
    public function index()
    {
        $this->request->allowMethod(['get', 'post']);

        if ($this->request->is('post')) {
            // Add a new entity.
            if ($this->objectType->is_abstract) {
                // Refuse to save an abstract object type.
                throw new ForbiddenException(__d('bedita', 'Abstract object types cannot be instantiated'));
            }

            $entity = $this->Table->newEntity();
            $entity->set('type', $this->request->getData('type'));
            $action = new SaveEntityAction(['table' => $this->Table, 'objectType' => $this->objectType]);

            $data = $this->request->getData();
            $data = $action(compact('entity', 'data'));

            $action = new GetObjectAction(['table' => $this->Table, 'objectType' => $this->objectType]);
            $data = $action(['primaryKey' => $data->id]);

            $this->response = $this->response
                ->withStatus(201)
                ->withHeader(
                    'Location',
                    $this->resourceUrl($data, 'id')
                );
        } else {
            // List existing entities.
            $filter = (array)$this->request->getQuery('filter') + array_filter(['query' => $this->request->getQuery('q')]);
            $contain = $this->prepareInclude($this->request->getQuery('include'));

            $action = new ListObjectsAction(['table' => $this->Table, 'objectType' => $this->objectType]);
            $query = $action(compact('filter', 'contain'));

            $this->set('_fields', $this->request->getQuery('fields', []));
            $data = $this->paginate($query);
        }

        $this->set(compact('data'));
        $this->set('_serialize', ['data']);
    }

    /**
     * {@inheritDoc}
     */
    protected function resourceUrl(EntityInterface $entity, $primaryKey)
    {
        return Router::url(
            [
                '_name' => 'api:objects:resource',
                'object_type' => $this->objectType->name,
                'id' => $entity->get($primaryKey),
            ],
            true
        );
    }

    /**
     * {@inheritDoc}
     */
    public function resource($id)
    {
        $this->request->allowMethod(['get', 'patch', 'delete']);

        $id = TableRegistry::get('Objects')->getId($id);
        $contain = $this->prepareInclude($this->request->getQuery('include'));

        $action = new GetObjectAction(['table' => $this->Table, 'objectType' => $this->objectType]);
        $entity = $action(['primaryKey' => $id, 'contain' => $contain]);

        if ($this->request->is('delete')) {
            // Delete an entity.
            $action = new DeleteObjectAction(['table' => $this->Table]);

            if (!$action(compact('entity'))) {
                throw new InternalErrorException(__d('bedita', 'Delete failed'));
            }

            return $this->response
                ->withStatus(204);
        }

        if ($this->request->is('patch')) {
            // Patch an existing entity.
            if ($this->request->getData('id') !== (string)$id) {
                throw new ConflictException(__d('bedita', 'IDs don\'t match'));
            }

            $action = new SaveEntityAction(['table' => $this->Table, 'objectType' => $this->objectType]);

            $data = $this->request->getData();
            $entity = $action(compact('entity', 'data'));
        }

        $this->set('_fields', $this->request->getQuery('fields', []));
        $this->set(compact('entity'));
        $this->set('_serialize', ['entity']);

        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @return \BEdita\Core\Model\Action\ListRelatedObjectsAction
     */
    protected function getAssociatedAction(Association $association)
    {
        return new ListRelatedObjectsAction(compact('association'));
    }

    /**
     * {@inheritDoc}
     */
    public function related()
    {
        $this->request->allowMethod(['get']);

        $relationship = $this->request->getParam('relationship');
        $relatedId = TableRegistry::get('Objects')->getId($this->request->getParam('related_id'));

        $association = $this->findAssociation($relationship);
        $filter = (array)$this->request->getQuery('filter') + array_filter(['query' => $this->request->getQuery('q')]);
        $contain = $this->prepareInclude($this->request->getQuery('include'));

        $action = $this->getAssociatedAction($association);
        $objects = $action(['primaryKey' => $relatedId] + compact('filter', 'contain'));

        if ($objects instanceof Query) {
            $objects = $this->paginate($objects);
        }

        $this->set('_fields', $this->request->getQuery('fields', []));
        $this->set(compact('objects'));
        $this->set('_serialize', ['objects']);

        $available = $this->getAvailableUrl($relationship);
        $this->set('_links', compact('available'));
    }

    /**
     * {@inheritDoc}
     */
    public function relationships()
    {
        $id = TableRegistry::get('Objects')->getId($this->request->getParam('id'));
        $relationship = $this->request->getParam('relationship');

        $association = $this->findAssociation($relationship);
        $this->setRelationshipsAllowedMethods($association);

        switch ($this->request->getMethod()) {
            case 'PATCH':
                $action = new SetRelatedObjectsAction(compact('association'));
                break;

            case 'POST':
                $action = new AddRelatedObjectsAction(compact('association'));
                break;

            case 'DELETE':
                $action = new RemoveRelatedObjectsAction(compact('association'));
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

        if (is_array($count)) {
            $action = $this->getAssociatedAction($association);
            $data = $action(['primaryKey' => $id, 'list' => true, 'only' => $count]);

            $count = count($count);
        }

        if ($count === 0) {
            return $this->response
                ->withStatus(204);
        }

        $this->set(compact('data'));
        $this->set([
            '_serialize' => isset($data) ? ['data'] : [],
        ]);

        return null;
    }

    /**
     * Return link to available objects by relationship
     *
     * @param string $relationship relation name
     * @return string|null
     */
    protected function getAvailableUrl($relationship)
    {
        $available = parent::getAvailableUrl($relationship);
        if ($available !== null) {
            return $available;
        }

        $types = $this->getAvailableTypes($relationship);
        if (empty($types)) {
            return null;
        }

        $url = [
            '_name' => 'api:objects:index',
            'object_type' => 'objects'
        ];
        if (count(array_diff($types, ['objects'])) > 0) {
            natsort($types);
            $url['filter'] = ['type' => array_values($types)];
        }

        return Router::url($url, true);
    }

    /**
     * Return available object types for a relationship
     *
     * @param string $relationship relation name
     * @return array List of available types
     */
    protected function getAvailableTypes($relationship)
    {
        foreach ($this->objectType->getRelations('right') as $relation) {
            if ($relation->inverse_name !== $relationship) {
                continue;
            }

            return array_values(Hash::extract($relation->left_object_types, '{n}.name'));
        }
        foreach ($this->objectType->getRelations('left') as $relation) {
            if ($relation->name !== $relationship) {
                continue;
            }

            return array_values(Hash::extract($relation->right_object_types, '{n}.name'));
        }

        return (array)$this->getConfig(sprintf('allowedAssociations.%s', $relationship), []);
    }
}

<?php
declare(strict_types=1);

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

use BEdita\API\Model\Action\UpdateRelatedAction;
use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Action\ActionTrait;
use BEdita\Core\Model\Action\AddRelatedObjectsAction;
use BEdita\Core\Model\Action\CloneAction;
use BEdita\Core\Model\Action\DeleteObjectAction;
use BEdita\Core\Model\Action\DeleteObjectsAction;
use BEdita\Core\Model\Action\GetObjectAction;
use BEdita\Core\Model\Action\ListEntitiesAction;
use BEdita\Core\Model\Action\ListObjectsAction;
use BEdita\Core\Model\Action\ListRelatedObjectsAction;
use BEdita\Core\Model\Action\RemoveRelatedObjectsAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use BEdita\Core\Model\Action\SetRelatedObjectsAction;
use BEdita\Core\Model\Action\SortRelatedObjectsAction;
use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\Model\Table\ObjectsTable;
use BEdita\Core\Model\Table\RolesTable;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ConflictException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Response;
use Cake\ORM\Association;
use Cake\ORM\Query;
use Cake\ORM\Table;
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
    use ActionTrait;

    /**
     * @inheritDoc
     */
    public $defaultTable = 'Objects';

    /**
     * The referred object type entity filled when `object_type` request param is set and valid
     *
     * @var \BEdita\Core\Model\Entity\ObjectType
     */
    protected $objectType = null;

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'parents' => ['folders'],
        ],
    ];

    /**
     * Meta properties accessible for admins
     *
     * @var array
     */
    protected const ADMIN_META_ACCESSIBLE = ['locked'];

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        if (in_array($this->request->getParam('action'), ['related', 'relationships'])) {
            $name = $this->request->getParam('relationship');
            $allowedTypes = TableRegistry::getTableLocator()->get('ObjectTypes')
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
        if (isset($this->JsonApi) && $this->request->getParam('action') === 'relationshipsSort') {
            $this->JsonApi->setConfig('parseJson', false);
        }
    }

    /**
     * Init model related attributes:
     *  - $this->objectType
     *  - $this->defaultTable
     *  - $this->Table
     *
     * @return void
     * @throws \Cake\Routing\Exception\MissingRouteException If `object_type` param is not valid
     */
    protected function initObjectModel()
    {
        $type = $this->request->getParam('object_type', Inflector::underscore((string)$this->request->getParam('controller')));
        try {
            $this->objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get($type);
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
            $this->defaultTable = $this->objectType->alias;
            $this->Table = $this->fetchTable();
        } catch (RecordNotFoundException $e) {
            $this->log(sprintf('Object type "%s" does not exist', $type), 'warning', ['request' => $this->request]);

            throw new MissingRouteException(['url' => $this->request->getRequestTarget()]);
        }
    }

    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event)
    {
        if (
            $this->request->getParam('action') === 'relationships'
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
     * @inheritDoc
     */
    protected function prepareInclude($include, ?Table $table = null): array
    {
        $contain = parent::prepareInclude($include, $table);

        $objectType = null;
        if ($table === null) {
            $objectType = $this->objectType;
        } elseif ($table->hasBehavior('ObjectType')) {
            $objectType = $table->objectType();
        }

        if ($objectType instanceof ObjectType && $objectType->hasAssoc('Permissions')) {
            $contain[] = 'Permissions.Roles';
        }

        return $contain;
    }

    /**
     * @inheritDoc
     */
    public function index()
    {
        $this->request->allowMethod(['get', 'post', 'delete']);

        if ($this->request->is('delete')) {
            $ids = (string)$this->request->getQuery('ids');
            if (empty($ids)) {
                throw new BadFilterException(__d('bedita', 'Missing required parameter "{0}"', 'ids'));
            }
            $filter = ['id' => explode(',', $ids), 'deleted' => false];
            $action = new ListEntitiesAction(['table' => $this->Table]);
            $entities = $action(compact('filter'));
            $action = new DeleteObjectsAction();
            if (!$action(compact('entities'))) {
                throw new InternalErrorException(__d('bedita', 'Delete failed'));
            }

            return $this->response->withStatus(204);
        } elseif ($this->request->is('post')) {
            // Add a new entity.
            if ($this->objectType->is_abstract) {
                // Refuse to save an abstract object type.
                throw new ForbiddenException(__d('bedita', 'Abstract object types cannot be instantiated'));
            }

            $entity = $this->Table->newEmptyEntity();
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
            $filter = $this->prepareFilter();
            $contain = $this->prepareInclude($this->request->getQuery('include'));
            $lang = $this->request->getQuery('lang');

            $action = new ListObjectsAction(['table' => $this->Table, 'objectType' => $this->objectType]);
            $query = $action(compact('filter', 'contain', 'lang'));

            $this->set('_fields', $this->request->getQuery('fields', []));
            $data = $this->paginate($query);
            $this->addCount($data->toArray());
        }

        $this->set(compact('data'));
        $this->setSerialize(['data']);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function resource($id)
    {
        $this->request->allowMethod(['get', 'patch', 'delete']);

        $id = TableRegistry::getTableLocator()->get('Objects')->getId($id);
        $contain = $this->prepareInclude($this->request->getQuery('include'));

        $action = new GetObjectAction(['table' => $this->Table, 'objectType' => $this->objectType]);
        $entity = $action([
            'primaryKey' => $id,
            'contain' => $contain,
            'lang' => $this->request->getQuery('lang'),
        ]);

        $this->addCount([$entity]);

        $this->authorizeResource($entity);
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

            $data = (array)$this->request->getData();
            $entityOptions = $this->saveEntityOptions($data);
            $entity = $action(compact('entity', 'data', 'entityOptions'));
        }

        $this->set('_fields', $this->request->getQuery('fields', []));
        $this->set(compact('entity'));
        $this->setSerialize(['entity']);

        return null;
    }

    /**
     * Authorize resource.
     *
     * @param \Cake\Datasource\EntityInterface $entity The resource entity
     * @return void
     */
    protected function authorizeResource(EntityInterface $entity): void
    {
        if ($this->request->is('delete')) {
            $this->Authorization->authorize($entity, 'update');
            $this->Authorization->authorize($entity, 'updateParents'); // can't delete object if some parent is protected

            return;
        }

        if (!$this->request->is('patch')) {
            return;
        }

        $this->Authorization->authorize($entity, 'update');

        $data = (array)$this->request->getData();
        $protectedFieldsChanged = array_filter(
            ['uname', 'status'],
            fn (string $field): bool => Hash::check($data, $field) && $entity->get($field) !== Hash::get($data, $field),
        );

        if (empty($protectedFieldsChanged) || $this->Authorization->can($entity, 'updateParents')) {
            return;
        }

        throw new ForbiddenException(
            __d(
                'bedita',
                'Cannot change {0} field{1} since object {2} is locked by parent',
                [
                    implode(', ', $protectedFieldsChanged),
                    count($protectedFieldsChanged) > 1 ? 's' : '',
                    $entity->id,
                ]
            )
        );
    }

    /**
     * Retrieve entity options to handle meta property changes by admin users.
     * Add these authorized meta to entity data.
     *
     * @param array $data Entity data
     * @return array
     */
    protected function saveEntityOptions(array &$data): array
    {
        $roles = (array)$this->Authentication->getIdentityData('roles');
        $meta = (array)Hash::get($data, '_meta');
        $meta = array_intersect_key($meta, array_flip(static::ADMIN_META_ACCESSIBLE));
        if (!in_array(RolesTable::ADMIN_ROLE, (array)Hash::extract($roles, '{n}.id')) || empty($meta)) {
            return [];
        }
        $data = array_merge($data, $meta);

        return ['accessibleFields' => array_fill_keys(array_keys($meta), true)];
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
     * @inheritDoc
     */
    public function related()
    {
        $this->request->allowMethod(['get']);

        $relationship = $this->request->getParam('relationship');
        $relatedId = TableRegistry::getTableLocator()->get('Objects')->getId($this->request->getParam('related_id'));

        $association = $this->findAssociation($relationship);
        $filter = $this->prepareFilter();
        $contain = $this->prepareInclude($this->request->getQuery('include'), $association->getTarget());
        $lang = $this->request->getQuery('lang');

        $action = $this->getAssociatedAction($association);
        $objects = $action(['primaryKey' => $relatedId] + compact('filter', 'contain', 'lang'));

        if ($objects instanceof Query) {
            $objects = $this->paginate($objects);
            $this->addCount($objects->toArray());
        }

        $this->set('_fields', $this->request->getQuery('fields', []));
        $this->set(compact('objects'));
        $this->setSerialize(['objects']);

        $available = $this->getAvailableUrl($relationship);
        $this->set('_links', compact('available'));
    }

    /**
     * @inheritDoc
     */
    public function relationships()
    {
        $id = TableRegistry::getTableLocator()->get('Objects')->getId($this->request->getParam('id'));
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
                $filter = $this->prepareFilter();

                $action = $this->getAssociatedAction($association);
                $data = $action(['primaryKey' => $id, 'list' => true, 'filter' => $filter]);

                if ($data instanceof Query) {
                    $data = $this->paginate($data);
                }

                $this->set(compact('data'));
                $this->setSerialize(['data']);

                $available = $this->getAvailableUrl($relationship);
                $this->set('_links', compact('available'));

                return null;
        }

        $action = new UpdateRelatedAction(compact('action') + ['request' => $this->request]);
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

        $serialize = [];
        if (isset($data)) {
            $this->set(compact('data'));
            $serialize = ['data'];
        }
        $this->setSerialize($serialize);

        return null;
    }

    /**
     * Sort relationships data for an object.
     * This action replaces the related data with a sorted set of data.
     * Data is sort by a field in ascending or descending order.
     * The field and the direction are passed in the request meta data.
     *
     * Example:
     * ```
     * PATCH /{object_type}/{id}/relationships/{relationship}/sort
     * {
     *     "meta": {
     *         "field": "{{ field }}",
     *         "direction": "{{ direction }}"
     *     }
     * }
     * ```
     *
     * @return \Cake\Http\Response|null
     */
    public function relationshipsSort(): ?Response
    {
        $this->request->allowMethod(['patch']);
        $id = $this->request->getParam('id');
        $relationship = $this->request->getParam('relationship');
        $payload = json_decode((string)$this->request->getBody(), true);
        $field = (string)Hash::get($payload, 'meta.field');
        $direction = (string)Hash::get($payload, 'meta.direction');
        $association = $this->findAssociation($relationship);
        $this->setRelationshipsAllowedMethods($association);
        $action = new GetObjectAction(['table' => $this->Table, 'objectType' => $this->objectType]);
        $entity = $action(['primaryKey' => $id]);
        $action = new SortRelatedObjectsAction(compact('association'));
        $count = $action(['entity' => $entity, 'field' => $field, 'direction' => $direction]);
        if ($count === 0) {
            return $this->response->withStatus(204);
        }
        $this->setSerialize([]);

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
            'object_type' => 'objects',
        ];
        if (count(array_diff($types, ['objects'])) > 0) {
            natsort($types);
            $url['?']['filter'] = ['type' => array_values($types)];
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

    /**
     * Add count data to the entities when query string `count` is present.
     *
     * @param array|\Cake\Collection\CollectionInterface $entities List of entities
     * @return void
     */
    protected function addCount($entities): void
    {
        $count = $this->request->getQuery('count');
        if (empty($count)) {
            return;
        }

        /** @var \BEdita\Core\Model\Action\CountRelatedObjectsAction $action */
        $action = $this->createAction('CountRelatedObjectsAction');
        $action(compact('entities', 'count'));
    }

    /**
     * Prepare filter array from request.
     *
     * @return array
     */
    protected function prepareFilter(): array
    {
        $filter = (array)$this->request->getQuery('filter') +
            array_filter(['query' => $this->request->getQuery('q')]);
        $sort = $this->request->getQuery('sort');
        if (empty($sort)) {
            return $filter;
        }
        // Add date ranges special sort field to filter if found
        // It will be used in `ObjectsTable::findDateRanges`
        $sort = str_replace('-', '', $sort);
        if (in_array($sort, ObjectsTable::DATERANGES_SORT_FIELDS)) {
            $filter['date_ranges'][$sort] = true;
        }

        return $filter;
    }

    /**
     * Clone object
     *
     * @param int $id The ID
     * @param string $title The new title
     * @return \Cake\Http\Response|null
     */
    public function clone(int $id, string $title): ?Response
    {
        $this->request->allowMethod(['post']);
        $status = (string)$this->getRequest()->getData('status');
        $include = (array)$this->getRequest()->getData('_meta.include');
        $action = new CloneAction(['table' => $this->Table]);
        $entity = $action(compact('id', 'title', 'status', 'include'));
        $this->set(compact('entity'));
        $this->setSerialize(['entity']);

        return null;
    }
}

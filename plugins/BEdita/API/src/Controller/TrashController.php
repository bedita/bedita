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
namespace BEdita\API\Controller;

use BEdita\Core\Model\Action\DeleteObjectAction;
use BEdita\Core\Model\Action\GetObjectAction;
use BEdita\Core\Model\Action\ListObjectsAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use Cake\Http\Exception\ConflictException;
use Cake\ORM\TableRegistry;

/**
 * Controller for `/trash` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable $Objects
 */
class TrashController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Objects';

    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();

        $id = $this->request->getParam('id');
        if ($id) {
            /** @var \BEdita\Core\Model\Entity\ObjectType $objectType */
            $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->find('objectId', compact('id'))
                ->firstOrFail();
            $this->modelClass = $objectType->alias;
            $this->Table = TableRegistry::getTableLocator()->get($this->modelClass);
        }
    }

    /**
     * Paginated objects list.
     *
     * @return void
     */
    public function index()
    {
        $filter = (array)$this->request->getQuery('filter') + array_filter(['query' => $this->request->getQuery('q')]);
        $filter['locked'] = false;
        $action = new ListObjectsAction(['table' => $this->Objects]);
        $deleted = true;
        $query = $action(compact('filter', 'deleted'));
        $trash = $this->paginate($query);

        $this->set(compact('trash'));
        $this->viewBuilder()->setOption('serialize', ['trash']);
    }

    /**
     * Get single object data from trashcan.
     *
     * @param int $id Object ID.
     * @return void
     */
    public function view($id)
    {
        $action = new GetObjectAction(['table' => $this->Table]);
        $trash = $action(['primaryKey' => $id, 'deleted' => true, 'locked' => false]);

        $this->set(compact('trash'));
        $this->viewBuilder()->setOption('serialize', ['trash']);
    }

    /**
     * Restore object, remove from trashcan
     *
     * @param int $id Object ID.
     * @return \Cake\Http\Response
     * @throws \Cake\Http\Exception\ConflictException Throws an exception if object ID in the payload doesn't match
     *      the object ID in the URL.
     * @throws \Cake\Http\Exception\InternalErrorException Throws an exception if an error occurs during restore.
     */
    public function restore($id)
    {
        $this->request->allowMethod('patch');

        if ($this->request->getData('id') != $id) {
            throw new ConflictException('IDs don\'t match');
        }

        $action = new GetObjectAction(['table' => $this->Table]);
        $trash = $action(['primaryKey' => $id, 'deleted' => true, 'locked' => false]);

        $trash->deleted = false;
        $action = new SaveEntityAction(['table' => $this->Table]);
        $action(['entity' => $trash, 'data' => []]);

        return $this->response
            ->withStatus(204);
    }

    /**
     * Delete object permanently, remove from trashcan
     *
     * @param int $id Object ID.
     * @return \Cake\Http\Response
     * @throws \Cake\ORM\Exception\PersistenceFailedException Throws an exception if an error occurs during deletion.
     */
    public function delete($id)
    {
        $this->request->allowMethod('delete');

        $action = new GetObjectAction(['table' => $this->Table]);
        $trash = $action(['primaryKey' => $id, 'deleted' => true, 'locked' => false]);

        $action = new DeleteObjectAction(['table' => $this->Table]);
        $action(['entity' => $trash, 'hard' => true]);

        return $this->response
            ->withStatus(204);
    }
}

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
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->set('_type', 'trash');
    }

    /**
     * Paginated objects list.
     *
     * @return void
     */
    public function index()
    {
        $query = $this->Objects->find('all')
            ->where(['deleted' => 1])
            ->contain(['ObjectTypes']);

        $trash = $this->paginate($query);

        $this->set(compact('trash'));
        $this->set('_serialize', ['trash']);
    }

    /**
     * Get single object data from trashcan.
     *
     * @param int $id Object ID.
     * @return void
     */
    public function view($id)
    {
        $trash = $this->Objects->get($id, [
            'contain' => ['ObjectTypes'],
            'conditions' => ['deleted' => 1]
        ]);

        $this->set(compact('trash'));
        $this->set('_serialize', ['trash']);
    }

    /**
     * Restore object, remove from trashcan
     *
     * @param int $id Object ID.
     * @return void
     * @throws \Cake\Network\Exception\ConflictException Throws an exception if role ID in the payload doesn't match
     *      the role ID in the URL.
     * @throws \Cake\Network\Exception\InternalErrorException Throws an exception if an error occurs during deletion.
     */
    public function restore($id)
    {
        $this->request->allowMethod('patch');

        if ($this->request->data('id') != $id) {
            throw new ConflictException('IDs don\' match');
        }

        $trash = $this->Objects->get($id, [
            'conditions' => ['deleted' => 1]
        ]);
        if (empty($trash)) {
            throw new InternalErrorException('Object ' . $id . ' not found in trash');
        }

        $trash->deleted = false;
        if (!$this->Objects->save($trash)) {
            throw new InternalErrorException('Could not restore object');
        }

        $this->noContentResponse();
    }

    /**
     * Delete object, remove from trashcan
     *
     * @param int $id Object ID.
     * @return void
     * @throws \Cake\Network\Exception\InternalErrorException Throws an exception if an error occurs during deletion.
     */
    public function delete($id)
    {
        $this->request->allowMethod('delete');

        $object = $this->Objects->get($id);
        if (!$this->Objects->delete($object)) {
            throw new InternalErrorException('Could not delete object');
        }

        $this->noContentResponse();
    }
}

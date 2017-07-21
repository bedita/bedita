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

use BEdita\Core\Model\Action\GetEntityAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use Cake\Network\Exception\ForbiddenException;
use Cake\Routing\Router;

/**
 * Controller for `/streams` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\StreamsTable $Table
 */
class StreamsController extends ResourcesController
{

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'object' => ['media', 'images'],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Streams';

    /**
     * Upload a new stream.
     *
     * @param string $fileName Original file name.
     * @return void
     */
    public function upload($fileName)
    {
        $this->request->allowMethod(['post']);

        // Add a new entity.
        $entity = $this->Table->newEntity();
        $action = new SaveEntityAction(['table' => $this->Table]);

        $data = [
            'file_name' => $fileName,
            'mime_type' => $this->request->contentType(),
            'contents' => $this->request->getBody(),
        ];
        $data = $action(compact('entity', 'data'));

        $action = new GetEntityAction(['table' => $this->Table]);
        $data = $action(['primaryKey' => $data->get($this->Table->getPrimaryKey())]);

        $this->response = $this->response
            ->withStatus(201)
            ->withHeader(
                'Location',
                Router::url(
                    [
                        '_name' => 'api:resources:resource',
                        'controller' => $this->name,
                        'id' => $data->uuid,
                    ],
                    true
                )
            );

        $this->set(compact('data'));
        $this->set('_serialize', ['data']);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Cake\Network\Exception\ForbiddenException An exception is thrown on attempts to update existing streams.
     */
    public function resource($id)
    {
        if ($this->request->is('patch')) {
            throw new ForbiddenException(__d(
                'bedita',
                'You are not allowed to update existing streams, please delete and re-upload'
            ));
        }

        return parent::resource($id);
    }
}

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

use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Controller for `/streams` endpoint.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\StreamsTable $Table
 * @property \BEdita\API\Controller\Component\UploadComponent $Upload
 */
class StreamsController extends ResourcesController
{

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'object' => [], // Descendant types of `media` are automatically added in controller initialization.
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Streams';

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        /** @var \BEdita\Core\Model\Table\ObjectTypesTable $ObjectTypes */
        $ObjectTypes = TableRegistry::getTableLocator()->get('ObjectTypes');
        $allowed = $ObjectTypes->find('list')
            ->where(['parent_id' => $ObjectTypes->get('media')->id])
            ->toList();
        $this->setConfig('allowedAssociations.object', $allowed);

        if ($this->request->getParam('action') === 'upload') {
            $this->loadComponent('BEdita/API.Upload');
        }

        parent::initialize();
    }

    /**
     * Upload a new stream.
     *
     * @param string $fileName Original file name.
     * @return void
     */
    public function upload($fileName): void
    {
        $data = $this->Upload->upload($fileName);

        $this->set(compact('data'));
        $this->set('_serialize', ['data']);

        $this->response = $this->response
            ->withStatus(201)
            ->withHeader(
                'Location',
                Router::url(
                    [
                        '_name' => 'api:resources:resource',
                        'controller' => $this->name,
                        'id' => $data->get('uuid'),
                    ],
                    true
                )
            );
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Cake\Http\Exception\ForbiddenException An exception is thrown on attempts to update existing streams.
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

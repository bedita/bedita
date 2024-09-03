<?php
declare(strict_types=1);

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
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Controller for `/streams` endpoint.
 *
 * @since 4.0.0
 * @property \BEdita\Core\Model\Table\StreamsTable $Table
 * @property \BEdita\API\Controller\Component\UploadComponent $Upload
 */
class StreamsController extends ResourcesController
{
    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'object' => [], // Descendant types of `media` are automatically added in controller initialization.
        ],
    ];

    /**
     * @inheritDoc
     */
    public $defaultTable = 'Streams';

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        /** @var \BEdita\Core\Model\Table\ObjectTypesTable $ObjectTypes */
        $ObjectTypes = TableRegistry::getTableLocator()->get('ObjectTypes');
        $allowed = $ObjectTypes->find('list')
            ->where(['parent_id' => $ObjectTypes->get('media')->id])
            ->all()
            ->toList();
        $this->setConfig('allowedAssociations.object', $allowed);

        parent::initialize();

        if ($this->request->getParam('action') === 'upload') {
            $this->loadComponent('BEdita/API.Upload');
            if (isset($this->JsonApi)) {
                $this->JsonApi->setConfig('parseJson', false);
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function checkAcceptable(): void
    {
        if ($this->request->getParam('action') === 'download') {
            return;
        }

        parent::checkAcceptable();
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
        $this->setSerialize(['data']);

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
     * Clone a Stream by its UUID.
     *
     * @param string $uuid ID of the Stream to clone.
     * @return void
     */
    public function clone(string $uuid): void
    {
        $data = $this->Table->clone($this->Table->get($uuid));

        $this->set(compact('data'));
        $this->setSerialize(['data']);

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
     * Download a stream.
     *
     * @param string $uuid Stream UUID.
     * @return \Cake\Http\Response
     * @throws \Cake\Http\Exception\NotFoundException
     */
    public function download(string $uuid): Response
    {
        $stream = $this->Table->get($uuid);
        $filename = Hash::get($stream, 'file_name', sprintf('stream-%s', $uuid));

        $response = $this->response->withType($stream->get('mime_type'));

        /** @var \Psr\Http\Message\StreamInterface $content */
        $content = $stream->get('contents');
        if ($content !== null) {
            $response = $response->withStringBody($content->getContents());
        }

        return $response->withDownload($filename);
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

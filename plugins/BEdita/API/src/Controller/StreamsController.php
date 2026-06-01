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

use BEdita\Core\Model\Action\GetEntityAction;
use Cake\Http\Exception\ConflictException;
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
    protected array $_defaultConfig = [
        'allowedAssociations' => [
            'object' => [], // Descendant types of `media` are automatically added in controller initialization.
        ],
    ];

    /**
     * @inheritDoc
     */
    public ?string $defaultTable = 'Streams';

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

        if (in_array($this->request->getParam('action'), ['upload', 'uploadNewVersion'])) {
            $this->loadComponent('BEdita/API.Upload');
            if ($this->components()->has('JsonApi')) {
                $this->JsonApi->setConfig('parseJson', false);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function viewClasses(): array
    {
        if ($this->request->getParam('action') === 'download') {
            return $this->viewClasses;
        }

        return parent::viewClasses();
    }

    /**
     * Upload a new stream.
     *
     * @param string $fileName Original file name.
     * @return void
     */
    public function upload(string $fileName): void
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
                    true,
                ),
            );
    }

    /**
     * Upload a new version of a stream for an existing object.
     *
     * @param string $objectId Object ID (integer).
     * @param string $fileName Original file name.
     * @return void
     */
    public function uploadNewVersion(string $objectId, string $fileName): void
    {
        $this->request->allowMethod(['post', 'patch']);
        $data = $this->Upload->uploadNewVersion($fileName, (int)$objectId);

        $this->set(compact('data'));
        $this->setSerialize(['data']);

        $resourceUrl = Router::url(
            [
                '_name' => 'api:resources:resource',
                'controller' => $this->name,
                'id' => $data->get('uuid'),
            ],
            true,
        );

        $this->response = $this->response->withStatus($this->request->is('patch') ? 200 : 201);
        if ($this->request->is('post')) {
            $this->response = $this->response->withHeader('Location', $resourceUrl);
        }
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
                    true,
                ),
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
     * @throws \Cake\Http\Exception\ConflictException An exception is thrown on attempts to delete a non-latest versioned stream.
     */
    public function resource(string $id): ?Response
    {
        if ($this->request->is('patch')) {
            throw new ForbiddenException(__d(
                'bedita',
                'You are not allowed to update existing streams, please delete and re-upload',
            ));
        }

        if ($this->request->is('delete')) {
            $entityId = $this->getResourceId($id);
            $action = new GetEntityAction(['table' => $this->Table]);
            $entity = $action(['primaryKey' => $entityId]);
            $version = $entity->get('version');
            $objectId = $entity->get('object_id');
            if ($version !== null && $objectId !== null) {
                $latestVersion = $this->Table->nextVersion($objectId) - 1;
                if ((int)$version !== $latestVersion) {
                    throw new ConflictException(__d(
                        'bedita',
                        'You can only delete the latest version of a stream',
                    ));
                }
            }
        }

        return parent::resource($id);
    }
}

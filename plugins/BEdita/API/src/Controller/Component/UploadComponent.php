<?php

declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller\Component;

use BEdita\Core\Model\Action\GetEntityAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use Cake\Controller\Component;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use Cake\Http\Exception\ConflictException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Exception;
use Laminas\Diactoros\Stream;

/**
 * Handles file upload actions
 *
 * @since 4.2.0
 * @property \BEdita\Core\Model\Table\StreamsTable $Streams
 */
class UploadComponent extends Component
{
    use LocatorAwareTrait;

    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event): void
    {
        $request = $this->getController()->getRequest();
        // Decode base64-encoded body.
        if ($request->getHeaderLine('Content-Transfer-Encoding') === 'base64') {
            // Append filter to stream.
            $body = $request->getBody();

            $stream = $body->detach();
            stream_filter_append($stream, 'convert.base64-decode', STREAM_FILTER_READ);

            $body = new Stream($stream, 'r');
            $this->getController()->setRequest($request->withBody($body));
        }
    }

    /**
     * Upload a new stream and return entity.
     *
     * @param string $fileName Original file name.
     * @param int|null $objectId Object id.
     * @return \Cake\Datasource\EntityInterface
     */
    public function upload(string $fileName, ?int $objectId = null): EntityInterface
    {
        $request = $this->getController()->getRequest();
        $request->allowMethod(['post']);

        $this->Streams = $this->fetchTable('Streams');
        // Add a new entity.
        $entity = $this->Streams->newEmptyEntity();
        $action = new SaveEntityAction(['table' => $this->Streams]);

        $data = [
            'file_name' => $fileName,
            'mime_type' => $request->contentType(),
            'contents' => $request->getBody(),
        ];
        $entity->set('object_id', $objectId);
        $private = filter_var($request->getQuery('private_url', false), FILTER_VALIDATE_BOOLEAN);
        $entity->set('private_url', $private);
        $stream = $action(compact('entity', 'data'));
        $this->dispatchThumbnailsEvent($stream);
        $action = new GetEntityAction(['table' => $this->Streams]);

        return $action(['primaryKey' => $stream->get($this->Streams->getPrimaryKey())]);
    }

    /**
     * Upload a new version of a stream for an existing object.
     *
     * Creates a new stream with version = max(existing versions) + 1.
     * Rejects the upload with a 409 Conflict if the incoming file hash matches
     * any stream already associated with the object.
     *
     * @param string $fileName Original file name.
     * @param int $objectId Object ID the new version belongs to.
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\Http\Exception\ConflictException When the file is identical to an existing version.
     */
    public function uploadNewVersion(string $fileName, int $objectId): EntityInterface
    {
        $request = $this->getController()->getRequest();
        $request->allowMethod(['post']);

        $this->Streams = $this->fetchTable('Streams');

        $bodyContents = (string)$request->getBody();

        // Reject if the file is byte-for-byte identical to any existing version.
        $duplicate = $this->Streams->find()
            ->where(['object_id' => $objectId, 'hash_sha1' => sha1($bodyContents)])
            ->first();
        if ($duplicate !== null) {
            throw new ConflictException(__(
                'File is identical to existing version {0}',
                $duplicate->get('version'),
            ));
        }

        $entity = $this->Streams->newEmptyEntity();
        $entity->set('object_id', $objectId);
        $entity->set('version', $this->Streams->nextVersion($objectId));
        $private = filter_var($request->getQuery('private_url', false), FILTER_VALIDATE_BOOLEAN);
        $entity->set('private_url', $private);

        $contents = new Stream('php://temp', 'r+b');
        $contents->write($bodyContents);
        $contents->rewind();

        $action = new SaveEntityAction(['table' => $this->Streams]);
        $stream = $action([
            'entity' => $entity,
            'data' => [
                'file_name' => $fileName,
                'mime_type' => $request->contentType(),
                'contents' => $contents,
            ],
        ]);

        $this->dispatchThumbnailsEvent($stream);

        $action = new GetEntityAction(['table' => $this->Streams]);

        return $action(['primaryKey' => $stream->get($this->Streams->getPrimaryKey())]);
    }

    /**
     * Dispatch Thumbnails.update event to create/update thumbnails for uploaded image.
     *
     * @param \Cake\Datasource\EntityInterface $stream Stream entity.
     * @return void
     * @codeCoverageIgnore
     */
    protected function dispatchThumbnailsEvent(EntityInterface $stream): void
    {
        try {
            EventManager::instance()->dispatch(new Event('Thumbnails.update', $this, ['stream' => $stream]));
        } catch (Exception $e) {
            // Log exception but do not block upload
            $this->log($e->getMessage(), 'error');
        }
    }
}

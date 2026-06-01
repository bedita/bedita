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
use Cake\Database\Exception\QueryException;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use Cake\Http\Exception\ConflictException;
use Cake\Http\Exception\NotFoundException;
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
     * `POST` creates version N + 1.
     * `PATCH` replaces the latest existing version in place.
     * In both cases, the upload is rejected with a 409 Conflict if the
     * incoming file hash matches the latest existing version.
     *
     * @param string $fileName Original file name.
     * @param int $objectId Object ID the new version belongs to.
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\Http\Exception\ConflictException When the file is identical to an existing version.
     */
    public function uploadNewVersion(string $fileName, int $objectId): EntityInterface
    {
        $request = $this->getController()->getRequest();
        $request->allowMethod(['post', 'patch']);

        $this->Streams = $this->fetchTable('Streams');
        $replace = $request->is('patch');

        // Hash the request body as a stream to avoid loading the entire file into a PHP string.
        $bodyResource = $request->getBody()->detach();
        $hashContext = hash_init('sha1');
        hash_update_stream($hashContext, $bodyResource);
        $bodySha1 = hash_final($hashContext);
        rewind($bodyResource);

        // Get the latest existing stream — used for version calculation, duplicate detection,
        // and latest-version replacement.
        $latestStream = $this->Streams->find()
            ->where(['object_id' => $objectId])
            ->orderByDesc('version')
            ->first();

        if ($replace && $latestStream === null) {
            throw new NotFoundException(__d('bedita', 'Resource not found.'));
        }

        // Reject only if the file is identical to the latest version.
        if ($latestStream !== null && $latestStream->get('hash_sha1') === $bodySha1) {
            throw new ConflictException(__(
                'File is identical to latest version {0}',
                $latestStream->get('version'),
            ));
        }

        $entity = $replace ? $latestStream : $this->Streams->newEmptyEntity();
        if (!$replace) {
            $nextVersion = $latestStream !== null ? (int)$latestStream->get('version') + 1 : 1;
            $entity->set('object_id', $objectId);
            $entity->set('version', $nextVersion);
            $private = filter_var($request->getQuery('private_url', false), FILTER_VALIDATE_BOOLEAN);
            $entity->set('private_url', $private);
        } elseif ($request->getQuery('private_url') !== null) {
            $private = filter_var($request->getQuery('private_url'), FILTER_VALIDATE_BOOLEAN);
            $entity->set('private_url', $private);
        }

        $action = new SaveEntityAction(['table' => $this->Streams]);
        try {
            $stream = $action([
                'entity' => $entity,
                'data' => [
                    'file_name' => $fileName,
                    'mime_type' => $request->contentType(),
                    'contents' => $bodyResource,
                ],
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new ConflictException(__('Stream version conflict, please retry.'));
            }
            throw $e;
        }

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

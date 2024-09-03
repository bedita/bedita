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
use Cake\Event\EventInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
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
        // avoid that RequestHandler tries to parse body
        $this->getController()->RequestHandler->setConfig('inputTypeMap', [], false);

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
    public function upload($fileName, ?int $objectId = null): EntityInterface
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
        $data = $action(compact('entity', 'data'));
        $action = new GetEntityAction(['table' => $this->Streams]);

        return $action(['primaryKey' => $data->get($this->Streams->getPrimaryKey())]);
    }
}

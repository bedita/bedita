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

namespace BEdita\API\Controller\Model;

use BEdita\API\Controller\JsonBaseController;
use BEdita\Core\Utility\JsonSchema;
use Cake\Event\Event;
use Cake\Http\Response;

/**
 * Controller for `/model/schema/{type}` endpoint.
 *
 * @since 4.0.0
 */
class SchemaController extends JsonBaseController
{
    /**
     * JSON Schema content type.
     *
     * @var string
     */
    public const CONTENT_TYPE = 'application/schema+json';

    /**
     * {@inheritDoc}
     *
     * Intentionally left blank to override parent method.
     * Avoid content-type negotiation checks based on `Accept` header.
     *
     * @codeCoverageIgnore
     */
    public function beforeFilter(Event $event)
    {
    }

    /**
     * Get JSON-SCHEMA of a type.
     *
     * @param string $typeName Name of an object type or of a resource type.
     * @return \Cake\Http\Response
     */
    public function jsonSchema($typeName): Response
    {
        $this->request->allowMethod(['get']);

        $response = $this->response->withEtag((string)JsonSchema::schemaRevision($typeName));
        if ($response->checkNotModified($this->request)) {
            return $response;
        }
        $this->response = $response;

        $url = (string)$this->request->getUri();
        $schema = JsonSchema::generate($typeName, $url);

        $this->set($schema);
        $this->setSerialize(array_keys((array)$schema));

        $response = $this->render()
            ->withType(static::CONTENT_TYPE);
        if (!is_array($schema)) {
            $response = $response->withStringBody(json_encode($schema));
        }

        return $response;
    }
}

<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\View;

use Cake\View\JsonView;

/**
 * A view class that is used for API JSON sche,aresponse.
 *
 * @since 6.0.0
 */
class JsonSchemaView extends JsonView
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $response = $this->getResponse();
        $this->setResponse($response->withType(static::contentType()));
    }

    /**
     * @inheritDoc
     */
    public static function contentType(): string
    {
        return 'application/schema+json';
    }
}

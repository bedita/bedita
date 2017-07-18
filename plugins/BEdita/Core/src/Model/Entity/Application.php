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

namespace BEdita\Core\Model\Entity;

use BEdita\Core\Utility\JsonApiSerializable;
use Cake\ORM\Entity;
use Cake\Routing\Router;

/**
 * Application Entity
 *
 * @property int $id
 * @property string $api_key
 * @property string $name
 * @property string $description
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property bool $enabled
 *
 * @property \BEdita\Core\Model\Entity\EndpointPermission[] $endpoint_permissions
 */
class Application extends Entity implements JsonApiSerializable
{

    use JsonApiTrait;

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'api_key' => false,
        'created' => false,
        'modified' => false,
    ];

    /**
     * {@inheritDoc}
     */
    protected function getLinks()
    {
        $options = [
            '_name' => 'api:admin:resource',
            'item' => 'applications',
            'id' => $this->id,
        ];

        return [
            'self' => Router::url($options, true),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getRelationships()
    {
         return [[], []];
    }
}

<?php
declare(strict_types=1);

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
use Cake\ORM\TableRegistry;

/**
 * Endpoint Entity
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property bool $enabled
 * @property int $object_type_id
 * @property string $object_type_name (virtual prop)
 *
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 * @property \BEdita\Core\Model\Entity\EndpointPermission[] $endpoint_permissions
 * @since 4.0.0
 */
class Endpoint extends Entity implements JsonApiSerializable
{
    use JsonApiAdminTrait;

    /**
     * @inheritDoc
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'created' => false,
        'modified' => false,
    ];

    /**
     * Setter for `object_type_name` virtual property.
     *
     * @param string $name The object type name
     * @return string|null
     */
    protected function _setObjectTypeName(?string $name): ?string
    {
        if ($name === null) {
            $this->object_type_id = $this->object_type = null;

            return null;
        }

        $this->object_type = TableRegistry::getTableLocator()->get('ObjectTypes')->get($name);

        return $name;
    }
}

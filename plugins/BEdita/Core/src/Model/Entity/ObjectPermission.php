<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
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

/**
 * ObjectPermission Entity
 *
 * @property int $id
 * @property int $object_id
 * @property int $role_id
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 * @property \BEdita\Core\Model\Entity\Role $role
 * @property \BEdita\Core\Model\Entity\User $created_by_user
 */
class ObjectPermission extends Entity implements JsonApiSerializable
{
    use JsonApiTrait;

    /**
     * @inheritDoc
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'created' => false,
        'created_by' => false,
    ];

    /**
     * @inheritDoc
     */
    protected $_hidden = [
        'created_by_user',
    ];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function getRelationships()
    {
         return [[], []];
    }
}

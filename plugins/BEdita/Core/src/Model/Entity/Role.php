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

use Cake\ORM\Entity;

/**
 * Role Entity.
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $unchangeable
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \BEdita\Core\Model\Entity\User[] $users
 * @property \BEdita\Core\Model\Entity\EndpointPermission[] $endpoint_permissions
 *
 * @since 4.0.0
 */
class Role extends Entity
{

    use JsonApiTrait;

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'unchangeable' => false,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'endpoint_permissions',
    ];
}

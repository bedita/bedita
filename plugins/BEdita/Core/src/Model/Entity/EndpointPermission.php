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
 * EndpointPermission Entity
 *
 * @property int $id
 * @property int $endpoint_id
 * @property int $application_id
 * @property int $role_id
 * @property int $permission
 *
 * @property \BEdita\Core\Model\Entity\Endpoint $endpoint
 * @property \BEdita\Core\Model\Entity\Application $application
 * @property \BEdita\Core\Model\Entity\Role $role
 *
 * @since 4.0.0
 */
class EndpointPermission extends Entity
{
    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}

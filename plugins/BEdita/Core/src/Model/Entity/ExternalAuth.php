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
 * ExternalAuth Entity.
 *
 * @property int $id
 * @property int $user_id
 * @property \BEdita\Core\Model\Entity\User $user
 * @property int $auth_provider_id
 * @property \BEdita\Core\Model\Entity\AuthProvider $auth_provider
 * @property string $provider_username
 * @property string $params
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @since 4.0.0
 */
class ExternalAuth extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

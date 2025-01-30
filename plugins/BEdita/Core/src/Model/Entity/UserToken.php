<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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
 * UserToken Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $application_id
 * @property string $client_token
 * @property string|null $secret_token
 * @property string $token_type
 * @property \Cake\I18n\DateTime|null $expires
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $used
 * @property \BEdita\Core\Model\Entity\User $user
 * @property \BEdita\Core\Model\Entity\Application|null $application
 */
class UserToken extends Entity
{
    /**
     * @inheritDoc
     */
    protected array $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

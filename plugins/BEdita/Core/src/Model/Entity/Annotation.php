<?php
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

use BEdita\Core\Model\Entity\JsonApiTrait;
use BEdita\Core\Utility\JsonApiSerializable;
use Cake\ORM\Entity;

/**
 * Annotation Entity
 *
 * @property int $id
 * @property int $object_id
 * @property string $description
 * @property int $user_id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property string $params
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 * @property \BEdita\Core\Model\Entity\User $user
 */
class Annotation extends Entity implements JsonApiSerializable
{
    use JsonApiTrait;

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => false,
        'object_id' => true,
        'description' => true,
        'params' => true,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'user',
    ];
}

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

use BEdita\Core\Utility\JsonApiSerializable;
use Cake\ORM\Entity;

/**
 * Translation Entity
 *
 * @property int $id
 * @property int $object_id
 * @property string $lang
 * @property string $status
 * @property \Cake\I18n\Time|\Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\Time|\Cake\I18n\FrozenTime $modified
 * @property int $created_by
 * @property int $modified_by
 * @property array $translated_fields
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 * @property \BEdita\Core\Model\Entity\User $created_by_user
 * @property \BEdita\Core\Model\Entity\User $modified_by_user
 */
class Translation extends Entity implements JsonApiSerializable
{

    use JsonApiTrait;

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        'object_id' => true,
        'lang' => true,
        'status' => true,
        'translated_fields' => true,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'created_by_user',
        'modified_by_user',
    ];
}

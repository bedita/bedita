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

use Cake\ORM\Entity;

/**
 * Caption Entity
 *
 * @property int $id
 * @property int $object_id
 * @property string|null $lang
 * @property string|null $content
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 */
class Caption extends Entity
{
    /**
     * @inheritDoc
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    /**
     * @inheritDoc
     */
    protected $_hidden = [
        'id',
        'object_id',
    ];
}

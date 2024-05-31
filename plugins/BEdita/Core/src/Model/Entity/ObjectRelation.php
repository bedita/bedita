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
 * ObjectRelation Entity
 *
 * @property int $left_id
 * @property int $relation_id
 * @property int $right_id
 * @property int $priority
 * @property int $inv_priority
 * @property array $params
 *
 * @property \BEdita\Core\Model\Entity\ObjectEntity $object
 * @property \BEdita\Core\Model\Entity\Relation $relation
 */
class ObjectRelation extends Entity
{
    /**
     * @inheritDoc
     */
    protected $_accessible = [
        '*' => true,
        'left_id' => false,
        'relation_id' => false,
        'right_id' => false,
    ];

    /**
     * @inheritDoc
     */
    protected $_hidden = [
        'left_id',
        'right_id',
        'relation_id',
    ];
}

<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
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
 * RelationType Entity
 *
 * @property int $relation_id
 * @property int $object_type_id
 * @property string $side
 *
 * @property \BEdita\Core\Model\Entity\Relation $relation
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 */
class RelationType extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
    ];
}

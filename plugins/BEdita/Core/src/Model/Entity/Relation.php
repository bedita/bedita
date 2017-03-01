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
 * Relation Entity
 *
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string $inverse_name
 * @property string $inverse_label
 * @property string $description
 * @property string $params
 *
 * @property \BEdita\Core\Model\Entity\ObjectRelation[] $object_relations
 * @property \BEdita\Core\Model\Entity\ObjectType[] $left_object_types
 * @property \BEdita\Core\Model\Entity\ObjectType[] $right_object_types
 */
class Relation extends Entity
{

    use JsonApiTrait;

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => false,
        'name' => true,
        'label' => true,
        'inverse_name' => true,
        'inverse_label' => true,
        'description' => true,
        'params' => true,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'left_object_types',
        'right_object_types',
    ];
}

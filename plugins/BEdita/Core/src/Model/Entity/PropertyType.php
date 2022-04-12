<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
 * Property Type Entity.
 *
 * @property string $name
 * @property mixed $params
 * @since 4.0.0
 */
class PropertyType extends Entity implements JsonApiSerializable
{
    use JsonApiModelTrait;

    /**
     * @inheritDoc
     */
    protected $_accessible = [
        '*' => false,
        'name' => true,
        'params' => true,
        'created' => false,
        'modified' => false,
        'core_type' => false,
    ];
}

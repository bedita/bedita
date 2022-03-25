<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
 * Tag Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $label
 * @property bool $enabled
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \BEdita\Core\Model\Entity\ObjectTag[] $object_tags
 */
class Tag extends Entity implements JsonApiSerializable
{
    use JsonApiModelTrait;

    /**
     * @inheritDoc
     */
    protected $_accessible = [
        '*' => true,
        'created' => false,
        'modified' => false,
    ];

    /**
     * @inheritDoc
     */
    protected $_hidden = [
        '_joinData',
    ];
}

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
 * Category Entity
 *
 * @property int $id
 * @property int|null $object_type_id
 * @property string $name
 * @property string|null $label
 * @property int|null $parent_id
 * @property int|null $tree_left
 * @property int|null $tree_right
 * @property bool $enabled
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 * @property \BEdita\Core\Model\Entity\Category $parent_category
 * @property \BEdita\Core\Model\Entity\Category[] $child_categories
 * @property \BEdita\Core\Model\Entity\ObjectCategory[] $object_categories
 */
class Category extends Entity implements JsonApiSerializable
{
    use JsonApiModelTrait;
    use ObjectTypeNameTrait;

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
        'object_type_id',
        'object_type',
        '_joinData',
    ];

    /**
     * @inheritDoc
     */
    protected $_virtual = [
        'object_type_name',
    ];
}

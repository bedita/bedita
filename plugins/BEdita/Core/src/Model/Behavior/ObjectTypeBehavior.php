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

/**
 * Created by PhpStorm.
 * User: paolo
 * Date: 02/09/17
 * Time: 12:02
 */

namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Model\Entity\ObjectType;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;

/**
 * Object type behavior.
 *
 * @since 4.0.0
 */
class ObjectTypeBehavior extends Behavior
{

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'table' => 'ObjectTypes',
        'implementedMethods' => [
            'objectType' => 'objectType',
        ],
    ];

    /**
     * Object type instance.
     *
     * @var \BEdita\Core\Model\Entity\ObjectType
     */
    protected $objectType;

    /**
     * Getter/setter for object type.
     *
     * @param \BEdita\Core\Model\Entity\ObjectType|string|int|null $objectType Object type entity, name or ID.
     * @return \BEdita\Core\Model\Entity\ObjectType|null
     */
    public function objectType($objectType = null)
    {
        if ($objectType === null) {
            return $this->objectType;
        }

        if (!($objectType instanceof ObjectType)) {
            $objectType = TableRegistry::get($this->getConfig('table'))
                ->get($objectType);
        }

        return $this->objectType = $objectType;
    }
}

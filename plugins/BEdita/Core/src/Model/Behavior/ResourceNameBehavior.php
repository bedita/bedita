<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Model\Behavior;

use Cake\ORM\Behavior;

/**
 * ResourceName behavior - find resource or object id using a non numeric identifier
 */
class ResourceNameBehavior extends Behavior
{
    /**
     * Default configuration.
     * - 'field' column used as string identifier, default is 'name`
     *
     * @var array<string, mixed>
     */
    protected $_defaultConfig = [
        'field' => 'name',
    ];

    /**
     * Try to get the resource `id` from a non-numeric string identifier as `name` or `uname`.
     *
     * If `$name` is numeric it returns immediately.
     * else try to find it from the string identifier field.
     *
     * @param int|string $name Unique string identifier for the object.
     * @return int
     */
    public function getId($name): int
    {
        if (is_numeric($name)) {
            return (int)$name;
        }

        $result = $this->table()->find()
            ->select($this->table()->aliasField('id'))
            ->where([$this->table()->aliasField($this->getConfig('field')) => $name])
            ->enableHydration(false)
            ->firstOrFail();

        return $result['id'];
    }
}

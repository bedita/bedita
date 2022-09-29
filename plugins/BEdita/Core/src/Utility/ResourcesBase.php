<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Utility;

use BEdita\Core\ORM\Locator\TableLocator;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Table;
use Cake\Utility\Inflector;

/**
 * Resources utility base class
 *
 * @since 4.4.0
 */
abstract class ResourcesBase
{
    /**
     * Allowed resource types
     *
     * @var array
     */
    protected static $allowed = [];

    /**
     * Get resource table, removing type from registry to force new options.
     *
     * @param string $type Resource type name
     * @param array $options Table locator options
     * @return \Cake\ORM\Table
     */
    protected static function getTable(string $type, array $options = []): Table
    {
        if (!empty(static::$allowed) && !in_array($type, static::$allowed)) {
            throw new BadRequestException(
                __d('bedita', 'Resource type not allowed "{0}"', $type)
            );
        }
        $type = Inflector::camelize($type);
        $tl = new TableLocator();

        return $tl->get($type, $options);
    }
}

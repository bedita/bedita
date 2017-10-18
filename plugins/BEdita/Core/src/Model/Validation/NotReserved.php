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

namespace BEdita\Core\Model\Validation;

use Cake\Core\Configure;

/**
 * Reusable class to check for reserved names.
 * Used for object types and properties.
 *
 * @since 4.0.0
 */
class NotReserved
{
    /**
     * The list of reserved names
     *
     * @var array
     */
    protected static $reserved = [];

    /**
     * Clear reserved names list
     *
     * @return void
     */
    public function clear()
    {
        static::$reserved = [];
        Configure::write('Reserved', null);
    }

    /**
     * Load list of reserved names in `$reserved`
     *
     * @return void
     */
    protected function loadReserved()
    {
        if (!empty(static::$reserved)) {
            return;
        }
        Configure::load('BEdita/Core.reserved', 'default');
        static::$reserved = Configure::read('Reserved');
    }

    /**
     * Check if a value is not reserved
     *
     * @param mixed $value Value to check
     * @param array $context Validation context data
     */
    public function allowed($value, $context = [])
    {
        $this->loadReserved();
        if ($value && in_array($value, static::$reserved)) {
            return false;
        }

        return true;
    }
}

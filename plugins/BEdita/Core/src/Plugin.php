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

namespace BEdita\Core;

use Cake\Core\Configure;
use Cake\Core\Plugin as CakePlugin;

/**
 * {@inheritDoc}
 *
 * Extended with BEdita plugins utilities
 */
class Plugin extends CakePlugin
{

    /**
     * Plugins load defaults
     *
     * @return void
     */
    protected static $_defaults = [
        'debugOnly' => false,
        'autoload' => false,
        'bootstrap' => false,
        'routes' => false,
        'ignoreMissing' => false
    ];

    /**
     * Load plugins from 'Plugins' configuration
     *
     * @return void
     */
    public static function loadFromConfig()
    {
        $plugins = Configure::read('Plugins');
        if ($plugins) {
            foreach ($plugins as $plugin => $options) {
                $options = array_merge(self::$_defaults, $options);
                if (!$options['debugOnly'] || ($options['debugOnly'] && Configure::read('debug'))) {
                    Plugin::load($plugin, $options);
                }
            }
        }
    }
}

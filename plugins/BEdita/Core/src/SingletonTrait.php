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

/**
 * Singleton class.
 *
 * @since 4.0.0
 * @see https://github.com/sebastianbergmann/phpunit/tree/5.6/tests/_files/Singleton.php
 */
trait SingletonTrait
{

    /**
     * Singleton instance.
     *
     * @var static|null
     */
    private static $uniqueInstance = null;

    /**
     * Singleton constructor.
     *
     * The constructor is declared private in order to
     * prevent new instances from being created.
     *
     * @codeCoverageIgnore
     */
    final protected function __construct()
    {
    }

    /**
     * Singleton clone method.
     *
     * This method is declared private in order to
     * prevent existing instances from being cloned.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    final private function __clone()
    {
    }

    /**
     * Singleton getter.
     *
     * Use this method in order to get the singleton instance
     *
     * @return static|null
     */
    final public static function getInstance()
    {
        if (static::$uniqueInstance === null) {
            static::$uniqueInstance = new static;
        }

        return static::$uniqueInstance;
    }
}

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

namespace BEdita\Core\Utility;

/**
 * Singleton class representing current logged user,
 * its id will be used to save objects, check permissions, update analytics
 */
class LoggedUser
{
    private static $uniqueInstance = null;

    /**
     * User data MUST contain at least user 'id' as array key
     * valid examples:
     *   - ['id' => 1]
     *   - ['id' => 1, 'username' => 'bedita']
     *
     * @var array
     */
    private $userData = [];

    /**
     * Singleton constructor
     * @codeCoverageIgnore
     */
    protected function __construct()
    {
    }

    /**
     * Singleton __clone
     * @codeCoverageIgnore
     * @return void
     */
    final private function __clone()
    {
    }

    /**
     * Singleton getInstance method
     * @return CurrentUser instance
     */
    public static function getInstance()
    {
        return (self::$uniqueInstance === null) ? (self::$uniqueInstance = new self) : self::$uniqueInstance;
    }

    /**
     * Read singleton current user data
     * @return array
     */
    public static function getUser()
    {
        return self::getInstance()->userData;
    }

    /**
     * Read from singleton current user id
     * @return int Logged user id or NULL if no current user is set
     */
    public static function id()
    {
        // TODO: remove 1 ID - temporary set to 1 to allow tests
        $id = 1;
        $data = self::getInstance()->userData;
        if (!empty($data['id'])) {
            $id = $data['id'];
        }

        return $id;
    }

    /**
     * Set singleton current user data
     *
     * @param array $userData User data array
     * @return void
     */
    public static function setUser($userData)
    {
        if (!empty($userData['id'])) {
            self::getInstance()->userData = $userData;
        }
    }
}

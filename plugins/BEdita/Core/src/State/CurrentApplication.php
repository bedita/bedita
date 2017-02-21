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

namespace BEdita\Core\State;

use BEdita\Core\Model\Entity\Application;
use BEdita\Core\SingletonTrait;
use Cake\ORM\TableRegistry;

/**
 * Singleton class to store current application.
 *
 * @since 4.0.0
 */
class CurrentApplication
{

    use SingletonTrait;

    /**
     * Current application entity.
     *
     * @var \BEdita\Core\Model\Entity\Application|null
     */
    protected $application = null;

    /**
     * Set current application.
     *
     * @return \BEdita\Core\Model\Entity\Application|null
     */
    public function get()
    {
        return $this->application;
    }

    /**
     * Static wrapper around {@see self::get()}.
     *
     * @return \BEdita\Core\Model\Entity\Application|null
     */
    public static function getApplication()
    {
        return static::getInstance()->get();
    }

    /**
     * Set current application.
     *
     * @param \BEdita\Core\Model\Entity\Application|null $application Application instance.
     * @return self
     */
    public function set(Application $application = null)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Static wrapper around {@see self::set()}.
     *
     * @param \BEdita\Core\Model\Entity\Application $application Application instance.
     * @return void
     */
    public static function setApplication(Application $application)
    {
        static::getInstance()->set($application);
    }

    /**
     * Set current application via request's API key.
     *
     * @param string $apiKey API key.
     * @return void
     */
    public static function setFromApiKey($apiKey)
    {
        static::getInstance()->set(
            TableRegistry::get('Applications')->find('apiKey', compact('apiKey'))->firstOrFail()
        );
    }
}

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

namespace BEdita\Core\Job;

use Cake\Core\App;
use Cake\Utility\Inflector;

/**
 * Utility class to run async job services.
 *
 * @since 4.0.0
 */
class ServiceRegistry
{

    /**
     * Registered instances.
     *
     * @var \BEdita\Core\Job\JobService[]
     */
    protected static $instances = [];

    /**
     * Get a service class instance for a given name.
     *
     * If no service with supplied name was registered a corresponding class in plugins and core in 'Service' namespace
     * is searched.
     * Example:
     * - service $name = 'example', look for class \MyPlugin\Service\Example and then for \BEdita\Core\Service\Example
     *
     * @param string $name The service name you want to get.
     * @return \BEdita\Core\Job\JobService Service instance found
     * @throws \LogicException Throws an exception if no suitable class for that service could be found.
     */
    public static function get($name)
    {
        if (!empty(static::$instances[$name])) {
            return static::$instances[$name];
        }

        $plugin = 'BEdita/Core.';
        if (strpos($name, '.') !== false) {
            list($plugin, $name) = explode('.', $name);
            $plugin = Inflector::camelize($plugin) . '.';
        }

        $className = $plugin . Inflector::camelize($name);
        $fullClassName = App::className($className, 'Job/Service', 'Service');

        if ($fullClassName === false) {
            throw new \LogicException(__d('bedita', 'Unknown service "{0}"', [$name]));
        }

        if (!(new \ReflectionClass($fullClassName))->implementsInterface(JobService::class)) {
            throw new \LogicException(__d('bedita', 'Bad service class "{0}"', [$fullClassName]));
        }

        $instance = new $fullClassName();
        static::set($name, $instance);

        return $instance;
    }

    /**
     * Set service class instance for a service.
     *
     * @param string $name The service name you want to register.
     * @param \BEdita\Core\Job\JobService $instance The instance object to be registered.
     * @return void
     */
    public static function set($name, JobService $instance)
    {
        static::$instances[$name] = $instance;
    }

    /**
     * Reset registered service instances
     *
     * @return void
     */
    public static function reset()
    {
        static::$instances = [];
    }
}

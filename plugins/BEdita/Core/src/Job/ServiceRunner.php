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

use BEdita\Core\Job\JobService;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Utility class to run async job services.
 *
 * @since 4.0.0
 */
class ServiceRunner
{
    /**
     * Registered instances.
     *
     * @var \BEdita\Core\Job\JobService[]
     */
    protected static $instances = [];

    /**
     * Get a service class instance for a given $name.
     * If no service with a given $name was registered a corresponding class in plugins and core in 'Service' namespace
     * is searched.
     * Example:
     * - service $name = 'example', look for class \MyPlugin\Service\Example and then for \BEdita\Core\Service\Example
     *
     * @param string $name The service name you want to get.
     * @return \BEdita\Core\Job\JobService Service instance found
     * @throws \LogicException
     */
    public static function getService($name)
    {
        if (!empty(static::$instances[$name])) {
            return static::$instances[$name];
        }
        $className = Inflector::camelize($name);
        $plugins = array_keys(Configure::read('Plugins'));
        $plugins[] = 'BEdita\Core';
        $classFound = null;
        foreach ($plugins as $plugin) {
            $fullName = '\\' . $plugin . '\Service\\' . $className;
            if (class_exists($fullName)) {
                $classFound = $fullName;
                break;
            }
        }
        if (!$classFound) {
            Log::write('error', 'service not found: ' . $name);
            throw new \LogicException(__d('bedita', 'Unknown service "{0}"', [$name]));
        }
        $instance = new $classFound();
        static::register($name, $instance);

        return $instance;
    }

    /**
     * Register service class instance for a $name service.
     * Instance MUST implement JobService
     *
     * @param string $name The service name you want to register.
     * @param mixed $instance The instance object to be registered, MUST implement JobService
     * @return void
     * @throws \LogicException
     */
    public static function register($name, $instance)
    {
        if (!($instance instanceof JobService)) {
            Log::write('error', 'bad service class: ' . get_class($instance));
            throw new \LogicException(__d('bedita', 'Bad service class "{0}"', [get_class($instance)]));
        }
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

    /**
     * Run a service with $payload data read from async jobs table
     *
     * In $options array this parameters are available:
     *  - 'lockPeriod' : lock time duration, specify in the form '+5 minutes'
     *
     * @param string $uuid UUID of the job to run.
     * @param array $options Options for running this job.
     * @return bool True on success, false on failure
     */
    public static function run($uuid, $options = [])
    {
        $locked = false;
        $AsyncJobs = TableRegistry::get('AsyncJobs');
        try {
            $job = $AsyncJobs->lock($uuid, Hash::get($options, 'lockPeriod', '+5 minutes'));
            $locked = true;
            $service = static::getService($job->service);
            $success = $service->run($job->payload, $options);
            $AsyncJobs->unlock($uuid, $success);

            return $success;
        } catch (\Exception $e) {
            Log::write('error', 'job run failed: ' . $e->getMessage());
            if ($locked) {
                // locked job failed, unlock
                $AsyncJobs->unlock($uuid, false);
            }

            return false;
        }
    }

    /**
     * Run pending jobs reading from async jobs table.
     * Using an optional max number of jobs as 'limit'
     * Resulting array will contain
     *  - 'count' number oj jobs executed
     *  - 'success' uuids of jobs successfully executed
     *  - 'success' uuids of failed jobs
     *
     * @param int $limit Max number of pending jobs to run.
     * @return array Result details
     */
    public static function runPending($limit = 0)
    {
        $results = ['success' => [], 'failure' => []];
        $pending = TableRegistry::get('AsyncJobs')->find('pending')->select(['uuid']);
        if ($limit) {
            $pending->limit($limit);
        }
        $count = 0;
        foreach ($pending as $job) {
            if (static::run($job->uuid)) {
                $results['success'][] = $job->uuid;
            } else {
                $results['failure'][] = $job->uuid;
            }
            $count++;
        }
        $results['count'] = $count;

        return $results;
    }
}

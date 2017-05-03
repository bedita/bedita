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
use Cake\Log\LogTrait;
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
    use LogTrait;

    /**
     * Async Jobs table.
     *
     * @var \BEdita\Core\Model\Table\AsyncJobs
     */
    protected $AsyncJobs = null;

    /**
     * Registered instances.
     *
     * @var \BEdita\Core\Job\JobService[]
     */
    protected $instances = [];

    /**
     * Default constructor
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->AsyncJobs = TableRegistry::get('AsyncJobs');
    }

    /**
     * Get a service class instance for a given $name.
     * If no service with a given $name was registered a corresponding class in plugins and core namespaces
     * is searched.
     * Example:
     * - service $name = 'example', look for \MyPlugin\Service\Example and then for \BEdita\Core\Service\Example
     *
     * @param string $name The service name you want to get.
     * @return \BEdita\Core\Job\JobService Service instance found
     * @throws \LogicException
     */
    public function getService($name)
    {
        if (!empty($this->instances[$name])) {
            return $this->instances[$name];
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
            $this->log('service not found: ' . $name, 'error');
            throw new \LogicException(__d('bedita', 'Unknown service'));
        }
        $instance = new $classFound();
        $this->register($name, $instance);

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
    public function register($name, $instance)
    {
        if (!($instance instanceof JobService)) {
            $this->log('bad service class: ' . get_class($instance), 'error');
            throw new \LogicException(__d('bedita', 'Bad service instance'));
        }
        $this->instances[$name] = $instance;
    }

    /**
     * Reset registered service instances
     *
     * @return void
     */
    public function reset()
    {
        $this->instances = [];
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
    public function run($uuid, $options = [])
    {
        $locked = $run = false;
        try {
            $asyncJob = $this->AsyncJobs->lock($uuid, Hash::get($options, 'lockPeriod', '+5 minutes'));
            $locked = true;
            $service = $this->getService($asyncJob->service);
            $success = $service->run($asyncJob->payload, $options);
            $run = true;
            $this->AsyncJobs->unlock($uuid, $success);

            return $success;
        } catch (\Exception $e) {
            $this->log('job run failed: ' . $e->getMessage(), 'error');
            if ($locked) {
                // locked job failed, unlock
                $this->AsyncJobs->unlock($uuid, false);
            }

            return false;
        }
    }

    /**
     * Run pending jobs reading from async jobs table.
     * Using an optional max number of jobs as 'limit'
     *
     * @param int $limit Max number of pending jobs to run.
     * @return array Result details array with boolean flag for every uuid
     */
    public function runPending($limit = 0)
    {
        $results = [];
        $pending = $this->AsyncJobs->find('pending')->select(['uuid']);
        if ($limit) {
            $pending->limit($limit);
        }
        foreach ($pending as $job) {
            $success = $this->run($job->uuid);
            $results[$job->uuid] = $success;
        }

        return $results;
    }
}

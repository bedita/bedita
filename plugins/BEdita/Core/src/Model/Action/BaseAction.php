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

namespace BEdita\Core\Model\Action;

use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;

/**
 * Base class for actions.
 *
 * @since 4.0.0
 */
abstract class BaseAction
{

    use InstanceConfigTrait;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Base action constructor.
     *
     * @param array $config Configuration.
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);

        $this->initialize($config);
    }

    /**
     * Command initialization.
     *
     * @param array $config Configuration.
     * @return void
     */
    protected function initialize(array $config)
    {
    }

    /**
     * Command execution.
     *
     * @param array $data Data.
     * @return mixed
     */
    abstract public function execute(array $data = []);

    /**
     * Magic method to make action object invokable.
     *
     * @param array $data Data.
     * @return mixed
     */
    final public function __invoke(array $data = [])
    {
        return $this->execute($data);
    }

    /**
     * Allowed object `status` condition
     *
     * @return array Empty array if all `status` are allowed otherwise a list of allowed values
     */
    protected function statusCondition()
    {
        $filter = [
            'on' => ['status' => 'on'],
            'draft' => ['status IN' => ['on', 'draft']],
        ];
        $level = Configure::read('Status.level');
        if ($level && isset($filter[$level])) {
            return $filter[$level];
        }

        return [];
    }
}

<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Auth\TestSuite\Fixture;

use Cake\Core\Configure;
use Cake\TestSuite\Fixture\TestFixture as CakeFixture;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventListenerInterface;

/**
 * BEdita TestFixture loads DDL for fixtures from configuration, if present.
 *
 * @since 4.0.0
 */
class TestFixture extends CakeFixture implements EventListenerInterface, EventDispatcherInterface
{
    use EventDispatcherTrait;

    /**
     * {@inheritDoc}
     *
     * If self::$fields is empty trying to use table schema loaded in configuration
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function init()
    {
        $this->eventManager()->on($this);

        if ($this->table === null) {
            $this->table = $this->_tableFromClass();
        }

        if (empty($this->fields)) {
            $this->fields = $this->fieldsFromConf();
        }

        $this->dispatchEvent('TestFixure.beforeBuildSchema');

        parent::init();
    }

    /**
     * Return fields for table defined in configuration.
     *
     * Configuration has to be in 'schema.table_name' key defined as:
     * - 'schema.table_name.columns' (required)
     * - 'schema.table_name.constraints' (optional)
     * - 'schema.table_name.indexes' (optional)
     * - 'schema.table_name.options' (optional)
     *
     * @return array
     */
    protected function fieldsFromConf()
    {
        if (!Configure::check("schema.{$this->table}.columns")) {
            return [];
        }

        $fields = Configure::read("schema.{$this->table}.columns");
        $fields += [
            '_constraints' => Configure::read("schema.{$this->table}.constraints") ?: [],
            '_indexes' => Configure::read("schema.{$this->table}.indexes") ?: [],
            '_options' => Configure::read("schema.{$this->table}.options") ?: [
                'engine' => 'InnoDB',
                'collation' => 'utf8_general_ci',
            ],
        ];
        return $fields;
    }

    /**
     * Return an array of conventional TestFixture callbacks
     *
     * By implementing the conventional methods a TestFixture class is assumed
     * to be interested in the related event.
     *
     * Override this method if you need to add non-conventional event listeners.
     *
     * The conventional method map is:
     *
     * - TestFixure.beforeBuildSchema => beforeBuildSchema
     *
     * @return array
     */
    public function implementedEvents()
    {
        $eventMap = ['TestFixure.beforeBuildSchema' => 'beforeBuildSchema'];
        $events = [];

        foreach ($eventMap as $event => $method) {
            if (!method_exists($this, $method)) {
                continue;
            }
            $events[$event] = $method;
        }
        return $events;
    }
}

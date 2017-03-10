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

namespace BEdita\Core\TestSuite\Fixture;

use Cake\Core\Plugin;
use Cake\Database\Schema\Table as Schema;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventListenerInterface;
use Cake\TestSuite\Fixture\TestFixture as CakeFixture;

/**
 * BEdita TestFixture loads DDL for fixtures from configuration, if present.
 *
 * @since 4.0.0
 */
class TestFixture extends CakeFixture implements EventListenerInterface, EventDispatcherInterface
{
    use EventDispatcherTrait;

    /**
     * Plugin used for tables and fields metadata.
     *
     * These metadata are read from schema file in its `config/Migrations` folder.
     * Defaults to: `BEdita/Core`.
     *
     * @var string
     */
    protected $schemaPlugin = 'BEdita/Core';

    /**
     * {@inheritDoc}
     *
     * If `self::$fields` is empty trying to use table schema loaded in configuration
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

        $this->dispatchEvent('TestFixture.beforeBuildSchema');

        parent::init();
    }

    /**
     * Get table schema reading from configuration.
     *
     * Configuration is retrieved from `config/Migrations/schema-dump-default.lock` file.
     * Return false when it fails to load `TableSchema` from configuration.
     *
     * @return \Cake\Database\Schema\TableSchema|false
     */
    protected function getTableSchemaFromConf()
    {
        if (!Plugin::loaded($this->schemaPlugin)) {
            return false;
        }

        $source = Plugin::configPath($this->schemaPlugin) . DS . 'Migrations' . DS . 'schema-dump-default.lock';
        if (!file_exists($source) || !is_readable($source)) {
            return false;
        }

        $schema = unserialize(file_get_contents($source));
        if (empty($schema[$this->table])) {
            return false;
        }

        return $schema[$this->table];
    }

    /**
     * Return fields for table defined in configuration.
     *
     * @return array
     */
    protected function fieldsFromConf()
    {
        $table = $this->getTableSchemaFromConf();
        if (!($table instanceof Schema)) {
            return [];
        }

        $fields = array_flip($table->columns());
        array_walk(
            $fields,
            function (&$column, $columnName) use ($table) {
                $column = $table->column($columnName);
                unset($column['collate']);
            }
        );

        $fields['_constraints'] = array_flip($table->constraints());
        array_walk(
            $fields['_constraints'],
            function (&$constraint, $constraintName) use ($table) {
                $constraint = $table->constraint($constraintName);
            }
        );

        $fields['_indexes'] = array_flip($table->indexes());
        array_walk(
            $fields['_indexes'],
            function (&$index, $indexName) use ($table) {
                $index = $table->index($indexName);
            }
        );

        $fields['_options'] = $table->getOptions();

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
     * - TestFixture.beforeBuildSchema => beforeBuildSchema
     *
     * @return array
     */
    public function implementedEvents()
    {
        $eventMap = ['TestFixture.beforeBuildSchema' => 'beforeBuildSchema'];
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

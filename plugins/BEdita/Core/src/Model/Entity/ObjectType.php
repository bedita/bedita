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

namespace BEdita\Core\Model\Entity;

use Cake\ORM\Entity;
use Cake\Utility\Inflector;

/**
 * ObjectType Entity.
 *
 * @property int $id
 * @property string $name
 * @property string $pluralized
 * @property string $alias
 * @property string $description
 * @property string $plugin
 * @property string $model
 * @property string $table
 * @property \BEdita\Core\Model\Entity\Object[] $objects
 */
class ObjectType extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => false,
        'name' => true,
        'pluralized' => true,
        'description' => true,
        'plugin' => true,
        'model' => true,
        'table' => true,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_virtual = [
        'alias',
        'table',
    ];

    /**
     * Setter for property `name`.
     *
     * Force `name` field to be underscored via inflector.
     *
     * @param string $name Object type name.
     * @return string
     */
    protected function _setName($name)
    {
        return Inflector::underscore($name);
    }

    /**
     * Getter for property `pluralized`.
     *
     * If `pluralized` field is not set or empty, use inflected form of `name`.
     *
     * @return string
     */
    protected function _getPluralized()
    {
        if (!empty($this->_properties['pluralized'])) {
            return $this->_properties['pluralized'];
        }

        return Inflector::pluralize($this->name);
    }

    /**
     * Setter for property `pluralized`.
     *
     * Force `pluralized` field to be underscored via inflector.
     *
     * @param string|null $pluralized Object type pluralized name.
     * @return string
     */
    protected function _setPluralized($pluralized)
    {
        return Inflector::underscore($pluralized);
    }

    /**
     * Getter for virtual property `alias`.
     *
     * @return string
     */
    protected function _getAlias()
    {
        return Inflector::camelize($this->pluralized);
    }

    /**
     * Getter for virtual property `table`.
     *
     * @return string
     */
    protected function _getTable()
    {
        $table = $this->plugin . '.';
        if ($table == '.') {
            $table = '';
        }

        $table .= $this->model;

        return $table;
    }

    /**
     * Setter for virtual property `table`.
     *
     * @param string $table Full table name.
     * @return void
     */
    protected function _setTable($table)
    {
        list($plugin, $model) = pluginSplit($table);

        $this->plugin = $plugin;
        $this->model = $model;
    }
}

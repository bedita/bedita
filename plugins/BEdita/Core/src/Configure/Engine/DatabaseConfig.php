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
namespace BEdita\Core\Configure\Engine;

use Cake\Core\Configure\ConfigEngineInterface;
use Cake\Database\Exception;
use Cake\ORM\TableRegistry;

/**
 * BEdita database configuration engine.
 *
 * Configuration parameters are stored in `config` table
 *
 * Each `config.name` in database should be a first level key in Configure (without dots)
 * Some keys like 'Datasources', 'Cache' and 'EmailTransport' are reserved and may not be stored in database
 * Instead each corresponding `config.content` field should be either a string or JSON
 * `config.context` represents a group of parameters loaded via Configure::load($key, 'database')
 *
 * DatabaseConfig also manipulates how 'null', 'true', 'false' are handled.
 * These values will be converted to their boolean equivalents or to null.
 */
class DatabaseConfig implements ConfigEngineInterface
{

    /**
     * Reserved keys not storable in database
     *
     * @var array
     */
    protected $reservedKeys = ['Datasources', 'Cache', 'EmailTransport', 'Session', 'Error', 'App'];

    /**
     * Read from DB (or cache) $key group of paramenters (see database `config.context`)
     * and return the results as an array.
     *
     * @param string $key The group of parameters to read from database (see `config.context`).
     * @return array Parsed configuration values.
     * @throws \Cake\Core\Exception\Exception when parameter group is not found
     */
    public function read($key = null)
    {
        $values = [];
        $config = TableRegistry::get('Config');
        $query = $config->find()->select(['name', 'context', 'content']);
        $query->where(function ($exp, $q) {
            return $exp->notIn('name', $this->reservedKeys);
        });
        if ($key) {
            $query->andWhere(['context' => $key]);
        }
        $results = $query->all();
        foreach ($results as $data) {
            $cfgKey = $data['name'];
            $cfgValue = json_decode($data['content'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $cfgValue = $this->valueFromString($data['content']);
            }
            $values[$cfgKey] = $cfgValue;
        }

        return $values;
    }

    /**
     * Dumps Configure data array to Database.
     *
     * @param string $key The group parameter to write to.
     * @param array $data The data to write.
     * @return bool Success.
     */
    public function dump($key, array $data)
    {
        $context = $key;
        $entities = [];
        $table = TableRegistry::get('Config');
        foreach ($data as $name => $content) {
            if (in_array($name, $this->reservedKeys)) {
                continue;
            }
            $content = is_array($content) ? json_encode($content) : $this->valueToString($content);
            $entities[] = $table->newEntity(compact('name', 'context', 'content'));
        }
        $table->connection()->transactional(function () use ($table, $entities) {
            foreach ($entities as $entity) {
                if (!$table->save($entity, ['atomic' => false])) {
                    throw new Exception(sprintf('Config save failed: %s', print_r($entity->errors(), true)));
                }
            }
        });

        return true;
    }

    /**
     * Converts a value into its string equivalent
     *
     * @param mixed $value Value to export.
     * @return string String value for database.
     */
    protected function valueToString($value)
    {
        if ($value === null) {
            return 'null';
        }
        if ($value === true) {
            return 'true';
        }
        if ($value === false) {
            return 'false';
        }

        return (string)$value;
    }

    /**
     * Converts a value fomr its string value to its 'natural' form
     *
     * @param string $value Value to convert, if necessary.
     * @return mixed String Natural form value.
     */
    protected function valueFromString($value)
    {
        if ($value === 'null') {
            return null;
        }
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }

        return $value;
    }
}

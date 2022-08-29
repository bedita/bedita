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
use Cake\Database\Exception\DatabaseException;
use Cake\ORM\Locator\LocatorAwareTrait;

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
 *
 * @since 4.0.0
 * @property \BEdita\Core\Model\Table\ConfigTable $Config
 */
class DatabaseConfig implements ConfigEngineInterface
{
    use LocatorAwareTrait;

    /**
     * Application id
     *
     * @var int
     */
    protected $applicationId = null;

    /**
     * Reserved keys not storable in database
     *
     * @var array
     */
    public const RESERVED_KEYS = ['Datasources', 'Cache', 'EmailTransport', 'Session', 'Error', 'App'];

    /**
     * Setup application `id` if provided.
     *
     * @param int $applicationId Application id
     */
    public function __construct($applicationId = null)
    {
        $this->applicationId = $applicationId;
        $this->Config = $this->fetchTable('Config');
    }

    /**
     * Read configuration from database of `$key` parameters group
     * and return the results as an array.
     * Parameter group is mapped to database column `config.context`.
     *
     * @param string|null $key The group of parameters to read from database (see `config.context`).
     * @return array Parsed configuration values.
     */
    public function read($key): array
    {
        return $this->Config->fetchConfig($this->applicationId, $key)
            ->all()
            ->filter(function (array $item): bool {
                return !in_array($item['name'], self::RESERVED_KEYS);
            })
            ->map(function (array $item): array {
                $content = json_decode($item['content'], true);
                if ($content === null && json_last_error() !== JSON_ERROR_NONE) {
                    $content = static::valueFromString($item['content']);
                }
                $item['content'] = $content;

                return $item;
            })
            ->combine('name', 'content')
            ->toArray();
    }

    /**
     * Dumps Configure data array to Database.
     *
     * @param string $key The group parameter to write to.
     * @param array $data The data to write.
     * @return bool Success.
     */
    public function dump($key, array $data): bool
    {
        $context = $key;
        $entities = [];
        foreach ($data as $name => $content) {
            if (in_array($name, self::RESERVED_KEYS)) {
                continue;
            }
            $content = is_array($content) ? json_encode($content) : static::valueToString($content);
            $entities[] = $this->Config->newEntity(compact('name', 'context', 'content'));
        }
        $this->Config->getConnection()->transactional(function () use ($entities) {
            foreach ($entities as $entity) {
                if (!$this->Config->save($entity, ['atomic' => false])) {
                    throw new DatabaseException(sprintf('Config save failed: %s', print_r($entity->getErrors(), true)));
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
    protected static function valueToString($value): string
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
     * Converts a value from its string value to its 'natural' form.
     *
     * Note that for lowercased variant of 'true', 'false' and 'null'
     * are converted without errors from `json_decode()`.
     *
     * @param string $value Value to convert, if necessary.
     * @return mixed String Natural form value.
     */
    protected static function valueFromString($value)
    {
        if ($value === 'NULL') {
            return null;
        }
        if ($value === 'TRUE') {
            return true;
        }
        if ($value === 'FALSE') {
            return false;
        }

        return $value;
    }
}

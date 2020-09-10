<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Migration;

use BEdita\Core\Utility\Resources;
use Cake\Database\Connection;
use Cake\Utility\Hash;
use Migrations\AbstractMigration;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

abstract class ResourcesMigration extends AbstractMigration
{
    /**
     * Read YAML migration data
     *
     * @param bool $up Up direction
     * @return array
     */
    protected function readData(bool $up = true): array
    {
        $path = (new ReflectionClass($this))->getFileName();
        $file = str_replace('.php', '.yml', $path);
        if (!file_exists($file)) {
            throw new RuntimeException(__d('bedita', 'YAML file not found'));
        }

        $data = (array)Yaml::parse(file_get_contents($file));
        if ($up) {
            return array_intersect_key($data, array_flip(['create', 'update', 'remove']));
        }

        return array_filter([
            'create' => array_reverse((array)Hash::get($data, 'remove')),
            'remove' => array_reverse((array)Hash::get($data, 'create')),
            'update' => (array)Hash::get($data, 'restore'),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function up(): void
    {
        Resources::save(
            $this->readData(),
            ['connection' => $this->getConnection()]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function down(): void
    {
        Resources::save(
            $this->readData(false),
            ['connection' => $this->getConnection()]
        );
    }

    /**
     * Retrieve Db Connection
     *
     * @return Connection
     *
     * @codeCoverageIgnore
     */
    protected function getConnection(): Connection
    {
        return $this->getAdapter()->getCakeConnection();
    }
}

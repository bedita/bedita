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
use Cake\Utility\Hash;
use Migrations\AbstractMigration;
use ReflectionClass;
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
            return [];
        }

        $data = (array)Yaml::parse(file_get_contents($file));
        if ($up) {
            return $data;
        }

        return array_filter([
            'remove' => array_reverse(Hash::get($data, 'create')),
            'update' => (array)Hash::get($data, 'restore'),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        Resources::save(
            $this->readData(),
            ['connection' => $this->getAdapter()->getCakeConnection()]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        Resources::save(
            $this->readData(false),
            ['connection' => $this->getAdapter()->getCakeConnection()]
        );
    }
}

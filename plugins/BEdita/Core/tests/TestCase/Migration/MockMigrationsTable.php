<?php
declare(strict_types=1);

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
namespace BEdita\Core\Test\TestCase\Migration;

use Migrations\Table;

class MockMigrationsTable extends Table
{
    public static $calls = [];

    /**
     * @inheritDoc
     */
    public function addColumn($columnName, $type = null, $options = [])
    {
        static::$calls['addColumn'][] = func_get_args();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeColumn($columnName)
    {
        static::$calls['removeColumn'][] = func_get_args();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeColumn($columnName, $newColumnType, array $options = [])
    {
        static::$calls['changeColumn'][] = func_get_args();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function update()
    {
    }
}

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

/**
 * BEdita TestFixture loads DDL for fixtures from configuration, if present.
 *
 * @since 4.0.0
 */
class TestFixture extends CakeFixture
{

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function init()
    {
        parent::init();

        if (!Configure::check("schema.{$this->table}.columns")) {
            return;
        }

        // #820 Temporarily use `ROOT/plugins/BEdita/Core/config/be4-schema.json` to obtain schema.
        $this->fields = Configure::read("schema.{$this->table}.columns");
        $this->fields += [
            '_constraints' => Configure::read("schema.{$this->table}.constraints") ?: [],
            '_indexes' => Configure::read("schema.{$this->table}.indexes") ?: [],
            '_options' => Configure::read("schema.{$this->table}.options") ?: [
                'engine' => 'InnoDB',
                'collation' => 'utf8_general_ci',
            ],
        ];
    }
}

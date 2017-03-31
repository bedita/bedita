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

use Migrations\AbstractMigration;

class LocationsTable extends AbstractMigration
{

    public $autoId = false;

    public function up()
    {

        $this->table('locations')
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('coords', 'string', [
                'after' => 'id',
                'comment' => 'geometry coordinates, like points or poligons',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->addColumn('address', 'text', [
                'comment' => 'generic address, street name and number or other format',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('locality', 'string', [
                'comment' => 'city/town/village or generic settlement',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('postal_code', 'string', [
                'comment' => 'postal code or ZIP code',
                'default' => null,
                'limit' => 12,
                'null' => true,
            ])
            ->addColumn('country_name', 'string', [
                'comment' => 'country name',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('region', 'string', [
                'comment' => 'region, state or province inside a country',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $this->table('locations')
            ->addForeignKey(
                'id',
                'objects',
                'id',
                [
                    'constraint' => 'locations_id_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('object_types')
            ->insert([
                [
                    'name' => 'location',
                    'pluralized' => 'locations',
                    'description' => 'Location model with coords',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Locations',
                ],
            ])
            ->save();
    }

    public function down()
    {
        $this->table('locations')
            ->dropForeignKey(
                'id'
            );

        $this->dropTable('locations');
    }
}


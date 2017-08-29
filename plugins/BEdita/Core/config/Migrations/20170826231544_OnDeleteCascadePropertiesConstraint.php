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

/**
 * Change constraints of `properties` table enabling ON DELETE CASCADE
 *
 * @since 4.0.0
 */
class OnDeleteCascadePropertiesConstraint extends AbstractMigration
{

    public function up()
    {
        $this->table('properties')
            ->dropForeignKey([], 'properties_objtype_fk')
            ->dropForeignKey([], 'properties_proptype_fk')
            ->update();

        $this->table('properties')
            ->addForeignKey(
                'object_type_id',
                'object_types',
                'id',
                [
                    'constraint' => 'properties_objtype_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'property_type_id',
                'property_types',
                'id',
                [
                    'constraint' => 'properties_proptype_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('properties')
            ->dropForeignKey(
                'object_type_id'
            )
            ->dropForeignKey(
                'property_type_id'
            );

        $this->table('properties')
            ->addForeignKey(
                'object_type_id',
                'object_types',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'property_type_id',
                'property_types',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();
    }
}


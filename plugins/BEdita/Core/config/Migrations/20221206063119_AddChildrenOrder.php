<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

use Migrations\AbstractMigration;

class AddChildrenOrder extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('property_types')
            ->insert([
                'name' => 'children_order',
                'params' => json_encode([
                    'type' => 'string',
                    'enum' => [
                        'position',
                        '-position',
                        'title',
                        '-title',
                        'modified',
                        '-modified',
                    ],
                ]),
                'core_type' => 1,
            ])
            ->save();
        $objectTypeId = (int)$this->getQueryBuilder()
            ->select(['id'])
            ->from(['object_types'])
            ->where(['name' => 'folders'])
            ->execute()
            ->fetch()[0];
        $propertyTypeId = (int)$this->getQueryBuilder()
            ->select(['id'])
            ->from(['property_types'])
            ->where(['name' => 'children_order'])
            ->execute()
            ->fetch()[0];
        $this->table('properties')
            ->insert([
                'name' => 'children_order',
                'object_type_id' => $objectTypeId,
                'property_type_id' => $propertyTypeId,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
                'enabled' => 1,
                'is_nullable' => 1,
                'is_static' => 0,
            ])
            ->save();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->getQueryBuilder()
            ->delete('properties')
            ->where(['name' => 'children_order'])
            ->limit(1)
            ->execute();

        $this->getQueryBuilder()
            ->delete('property_types')
            ->where(['name' => 'children_order'])
            ->limit(1)
            ->execute();
    }
}

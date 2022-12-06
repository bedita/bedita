<?php
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

use BEdita\Core\Utility\Resources;
use Migrations\AbstractMigration;

class AddChildrenOrder extends AbstractMigration
{
    protected $create = [
        'property_types' => [
            [
                'name' => 'children_order',
                'params' => [
                    'type' => 'string',
                    'enum' => [
                        'position',
                        '-position',
                        'title',
                        '-title',
                        'modified',
                        '-modified',
                    ],
                ],
                'core_type' => 1,
            ],
        ],
        // this does not work in tests, for an issue with static_properties temporary table
        // we instead use a sql insert/delete query
        // 'properties' => [
        //     [
        //         'name' => 'children_order',
        //         'object' => 'folders',
        //         'property' => 'children_order',
        //         'description' => 'Folders children order',
        //         'enabled' => 1,
        //         'is_nullable' => 1,
        //     ],
        // ],
    ];

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        Resources::save(
            ['create' => $this->create],
            ['connection' => $this->getAdapter()->getCakeConnection()]
        );
        $objectTypeId = (int)$this->getQueryBuilder()
            ->select(['id'])
            ->from(['object_types'])
            ->where(['name' => 'folders'])
            ->execute()
            ->fetch()[0];
        $propertyTypesId = (int)$this->getQueryBuilder()
            ->select(['id'])
            ->from(['property_types'])
            ->where(['name' => 'children_order'])
            ->execute()
            ->fetch()[0];
        $fields = [
            'name' => 'string',
            'object_type_id' => 'int',
            'property_type_id' => 'int',
            'created' => 'datetime',
            'modified' => 'datetime',
            'enabled' => 'boolean',
            'is_nullable' => 'boolean',
            'is_static' => 'boolean',
        ];
        $this->getQueryBuilder()
            ->insert(array_keys($fields), array_values($fields))
            ->into('properties')
            ->values([
                'name' => 'children_order',
                'object_types_id' => $objectTypeId,
                'property_types_id' => $propertyTypesId,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
                'enabled' => 1,
                'is_nullable' => 1,
                'is_static' => 0,
            ])
            ->execute();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->getQueryBuilder()
            ->delete('properties')
            ->innerJoin('object_types', [
                'object_types.id = objects.object_type_id',
                'object_types.name' => 'folders',
            ])
            ->where(['name' => 'children_order'])
            ->limit(1)
            ->execute();
        Resources::save(
            ['remove' => ['property_types' => $this->create['property_types']]],
            ['connection' => $this->getAdapter()->getCakeConnection()]
        );
    }
}

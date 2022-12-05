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
use Cake\ORM\TableRegistry;
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
        $objectTypeId = TableRegistry::getTableLocator()->get('ObjectTypes')->get('folders')->id;
        $propertyTypeId = TableRegistry::getTableLocator()->get('PropertyTypes')
            ->find()
            ->select(['id'])
            ->where(['name' => 'children_order'])
            ->firstOrFail()
            ->id;
        $d = new \DateTime();
        $d = $d->format('Y-m-d\TH:i:s+00:00');
        $sql = 'INSERT INTO properties (';
        $sql .= 'name, object_type_id, property_type_id, created, modified,';
        $sql .= 'description, enabled, is_nullable, is_static';
        $sql .= ') VALUES (';
        $sql .= sprintf(
            '"children_order", %d, %d, "%s", "%s", "Folders children order", 1, 1, 0)',
            $objectTypeId,
            $propertyTypeId,
            $d,
            $d
        );
        $this->query($sql);
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        // Resources::save(
        //     ['remove' => ['properties' => $this->create['properties']]],
        //     ['connection' => $this->getAdapter()->getCakeConnection()]
        // );
        $sql = 'DELETE FROM properties WHERE name = "children_order"';
        $this->query($sql);
        Resources::save(
            ['remove' => ['property_types' => $this->create['property_types']]],
            ['connection' => $this->getAdapter()->getCakeConnection()]
        );
    }
}

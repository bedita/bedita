<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

use Cake\ORM\Table;
use Migrations\AbstractMigration;

class UpdateChildrenOrder extends AbstractMigration
{
    /**
     * Data for migration.
     *
     * @var array
     */
    protected array $data = [
        'new' => [
            'type' => 'string',
            'enum' => [
                'position',
                '-position',
                'title',
                '-title',
                'created',
                '-created',
                'modified',
                '-modified',
                'publish_start',
                '-publish_start',
            ],
        ],
        'old' => [
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
    ];

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        /* @var \Migrations\CakeAdapter $adapter */
        $adapter = $this->getAdapter();
        $table = new Table([
            'table' => 'property_types',
            'connection' => $adapter->getCakeConnection(),
        ]);
        $table->updateAll(
            [
                'params' => json_encode($this->data['new']),
            ],
            [
                'id' => $table->find()->where(['name' => 'children_order'])->firstOrFail()->id,
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        /* @var \Migrations\CakeAdapter $adapter */
        $adapter = $this->getAdapter();
        $table = new Table([
            'table' => 'property_types',
            'connection' => $adapter->getCakeConnection(),
        ]);
        $table->updateAll(
            [
                'params' => json_encode($this->data['old']),
            ],
            [
                'id' => $table->find()->where(['name' => 'children_order'])->firstOrFail()->id,
            ]
        );
    }
}

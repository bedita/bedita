<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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

/**
 * Add `folders` core media type.
 *
 * @since 4.0.0
 */
class FoldersType extends AbstractMigration
{

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->table('object_types')
            ->insert([
                [
                    'name' => 'folders',
                    'singular' => 'folder',
                    'description' => 'Folder model',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Folders',
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s'),
                    'parent_id' => 1,
                    'core_type' => 1,
                    'enabled' => 1,
                ],
            ])
            ->save();

        $adapter = $this->getAdapter();
        $table = new Table([
            'table' => 'object_types',
            'connection' => $adapter->getCakeConnection(),
        ]);
        // Now let's fix NSM (nested-set model) left and right indexes from tree data.
        $table->addBehavior('Tree', [
            'left' => 'tree_left',
            'right' => 'tree_right',
        ]);
        /* @var \Cake\ORM\Behavior\TreeBehavior $tree */
        $tree = $table->behaviors()->get('Tree');
        $tree->recover();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $adapter = $this->getAdapter();
        $table = new Table([
            'table' => 'object_types',
            'connection' => $adapter->getCakeConnection(),
        ]);
        $table->addBehavior('Tree', [
            'left' => 'tree_left',
            'right' => 'tree_right',
        ]);
        $table->delete($table->find()->where(['name' => 'folders'])->firstOrFail());
    }
}

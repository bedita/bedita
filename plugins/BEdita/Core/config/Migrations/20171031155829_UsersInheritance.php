<?php

use Cake\ORM\Table;
use Migrations\AbstractMigration;

/**
 * Fix `users` inheritance => from `objects` instead of `profiles`.
 */
class UsersInheritance extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        // Using a clean CakePHP table object to use Tree behavior.
        $table = new Table([
            'table' => 'object_types',
            'connection' => $this->getAdapter()->getCakeConnection(),
        ]);

        $table->updateAll(
            [
                'parent_id' => 1,
            ],
            [
                'name' => 'users',
            ]
        );

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
     * {@inheritDoc}
     */
    public function down()
    {
    }
}

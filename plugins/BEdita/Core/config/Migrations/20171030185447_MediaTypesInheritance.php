<?php

use Cake\ORM\Table;
use Migrations\AbstractMigration;

/**
 * Add inheritance info to `videos`, `audio` and `files`.
 *
 */
class MediaTypesInheritance extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        // Populate tree data. We'll be using a clean CakePHP table object to be able to use Tree behavior.
        /* @var \Migrations\CakeAdapter $adapter */
        $adapter = $this->getAdapter();
        $table = new Table([
            'table' => 'object_types',
            'connection' => $adapter->getCakeConnection(),
        ]);

        $table->updateAll( // `videos`, `audio` and `files` inherit from `media`.
            [
                'parent_id' => $table->find()->where(['name' => 'media'])->firstOrFail()->id,
            ],
            [
                'name IN' => ['videos', 'audio', 'files'],
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

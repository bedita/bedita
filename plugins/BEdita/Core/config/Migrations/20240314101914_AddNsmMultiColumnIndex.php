<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddNsmMultiColumnIndex extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function change()
    {
        $this->table('trees')
            ->addIndex(
                ['tree_left', 'tree_right'],
                [
                    'name' => 'trees_nsm_idx',
                    'order' => [
                        'tree_left' => 'ASC',
                        'tree_right' => 'DESC',
                    ],
                ],
            )
            ->update();
    }
}

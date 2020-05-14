<?php
use Migrations\AbstractMigration;

/**
 * Add `trees.canonical` https://github.com/bedita/bedita/issues/1690
 */
class AddTreesCanonical extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('trees')
            ->addColumn('canonical', 'boolean', [
                'comment' => 'canonical path flag',
                'default' => false,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('trees')
            ->removeColumn('canonical')
            ->update();
    }
}

<?php
use Migrations\AbstractMigration;

/**
 * Add `hidden` column to `object_types` table.
 *
 * @see https://github.com/bedita/bedita/1328
 */
class AddHidden extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
    public function up()
    {

        $this->table('object_types')
            ->addColumn('hidden', 'text', [
                'after' => 'associations',
                'comment' => 'hidden attributes, never displayed and ignored',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {

        $this->table('object_types')
            ->removeColumn('hidden')
            ->update();
    }
}


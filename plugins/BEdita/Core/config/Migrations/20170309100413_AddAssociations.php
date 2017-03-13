<?php
use Migrations\AbstractMigration;

/**
 * Add `associations` column to `object_types` table.
 *
 * @see https://github.com/bedita/bedita/pull/1138
 */
class AddAssociations extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('object_types')
            ->addColumn('associations', 'text', [
                'after' => 'model',
                'comment' => 'entities associated with this object type',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();

        $this->table('object_types')
            ->insert([
                [
                    'name' => 'events',
                    'singular' => 'event',
                    'description' => 'Event model, with date ranges',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'associations' => '["DateRanges"]'
                ],
            ])
            ->save();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('object_types')
            ->removeColumn('associations')
            ->update();
    }
}

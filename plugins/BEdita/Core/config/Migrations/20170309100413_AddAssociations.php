<?php
use Migrations\AbstractMigration;

class AddAssociations extends AbstractMigration
{

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

    public function down()
    {
        $this->table('object_types')
            ->removeColumn('associations')
            ->update();
    }
}

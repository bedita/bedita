<?php
use Migrations\AbstractMigration;

class NameSingularComments extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('object_types')
            ->changeColumn('name', 'string', [
                'comment' => 'object type name, plural form',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->changeColumn('singular', 'string', [
                'comment' => 'object type name, singular form',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->update();
    }
}

<?php
use Migrations\AbstractMigration;

class AddFolderCategories extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->query('UPDATE object_types SET associations = \'["Categories"]\' WHERE name = \'folders\'');
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->query("UPDATE object_types SET associations = '' WHERE name = 'folders'");
    }
}

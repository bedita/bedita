<?php
use Migrations\AbstractMigration;

class AddStreamMetadata extends AbstractMigration
{
    /**
    * {@inheritDoc}
    */
    public function up()
    {
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $json = in_array('json', $columnTypes) ? 'json' : 'text';

        $this->table('streams')
            ->addColumn('file_metadata', $json, [
                'comment' => 'json metadata of streams',
                'default' => null,
                'null' => true
            ])
            ->update();
    }

    /**
    * {@inheritDoc}
    */
    public function down()
    {
        $this->table('streams')
            ->removeColumn('file_metadata')
            ->update();
    }
}
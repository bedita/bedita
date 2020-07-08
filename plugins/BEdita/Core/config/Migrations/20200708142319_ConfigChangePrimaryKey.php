<?php
use Migrations\AbstractMigration;

class ConfigChangePrimaryKey extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        // store current rows and restore them at the end
        $configRows = $this->fetchAll("SELECT name, content, context, created, modified, application_id FROM config");
        // remove unwanted array items
        foreach ($configRows as &$row) {
            unset($row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
        }
        $this->table('config')->truncate();

        $this->table('config')
            ->addColumn('id', 'integer', [
                'after' => 'name',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->changePrimaryKey('id')
            ->addIndex(
                [
                    'name',
                    'application_id',
                ],
                [
                    'name' => 'config_nameapplicationid_uq',
                    'unique' => true,
                ]
            )
            ->update();

        $this->table('config')
            ->changeColumn('id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'autoIncrement' => true,
                'signed' => false,
            ])
            ->update();

        // restore config values
        $this->table('config')->insert($configRows)->save();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('config')
            ->changeColumn('id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->update();

        $this->table('config')
            ->changePrimaryKey('name')
            ->update();

        $this->table('config')
            ->removeIndexByName('config_nameapplicationid_uq')
            ->removeColumn('id')
            ->update();
    }
}

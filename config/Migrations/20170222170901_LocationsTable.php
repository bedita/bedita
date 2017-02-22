<?php
use Migrations\AbstractMigration;

class LocationsTable extends AbstractMigration
{

    public $autoId = false;

    public function up()
    {

        $this->table('locations')
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('coords_system', 'text', [
                'comment' => 'coordinates system used',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('address', 'text', [
                'comment' => 'generic address, street name and number or other format',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('locality', 'text', [
                'comment' => 'city/town/village or generic settlement',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('postal_code', 'string', [
                'comment' => 'postal code or ZIP code',
                'default' => null,
                'limit' => 12,
                'null' => true,
            ])
            ->addColumn('country_name', 'text', [
                'comment' => 'country name',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('region', 'text', [
                'comment' => 'region, state or province inside a country',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->create();

        $this->table('locations')
            ->addForeignKey(
                'id',
                'objects',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('locations')
            ->dropForeignKey(
                'id'
            );

        $this->dropTable('locations');
    }
}


<?php
use Migrations\AbstractMigration;

class AddIdVatNumbers extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->table('profiles')
            ->addColumn('national_id_number', 'string', [
                'comment' => '',
                'null' => true,
                'default' => null,
                'limit' => 32,
            ])
            ->addColumn('vat_number', 'string', [
                'comment' => '',
                'null' => true,
                'default' => null,
                'limit' => 32,
            ])
            ->addIndex(
                [
                    'national_id_number',
                ],
                [
                    'name' => 'profiles_nationalidnumber_idx',
                ]
            )
            ->addIndex(
                [
                    'vat_number',
                ],
                [
                    'name' => 'profiles_vatnumber_idx',
                ]
            )
            ->update();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->table('profiles')
            ->removeIndexByName('profiles_nationalidnumber_idx')
            ->removeIndexByName('profiles_vatnumber_idx')
            ->removeColumn('national_id_number')
            ->removeColumn('vat_number')
            ->update();
    }
}

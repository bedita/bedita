<?php
use Migrations\AbstractMigration;

/**
 * Add `config.application_id`.
 *
 * @see https://github.com/bedita/bedita/1486
 */
class AddConfigApplicationId extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('config')
            ->addColumn('application_id', 'integer', [
                'comment' => 'link to applications.id - may be null',
                'default' => null,
                'limit' => 5,
                'null' => true,
                'signed' => false,
            ])
            ->addIndex(
                [
                    'application_id',
                ],
                [
                    'name' => 'config_applicationid_idx',
                ]
            )
            ->addForeignKey(
                'application_id',
                'applications',
                'id',
                [
                    'constraint' => 'config_applicationid_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('config')
            ->dropForeignKey('application_id')
            ->removeColumn('application_id')
            ->update();
    }
}

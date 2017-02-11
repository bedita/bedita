<?php
use Migrations\AbstractMigration;

/**
 * Allow `null` value in column `endpoint_permissions.endpoint_id`.
 *
 * @see https://github.com/bedita/bedita/issues/968
 */
class AllowPermissionsOnAllEndpoints extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('endpoint_permissions')
            ->changeColumn('endpoint_id', 'integer', [
                'comment' => 'link to endpoints.id',
                'default' => null,
                'limit' => 5,
                'null' => true,
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('endpoint_permissions')
            ->changeColumn('endpoint_id', 'integer', [
                'comment' => 'link to endpoints.id',
                'default' => null,
                'length' => 5,
                'null' => false,
            ])
            ->update();
    }
}


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
            ->dropForeignKey(
                'endpoint_id'
            );

        $this->table('endpoint_permissions')
            ->changeColumn('endpoint_id', 'integer', [
                'comment' => 'link to endpoints.id',
                'default' => null,
                'limit' => 5,
                'null' => true,
                'signed' => false,
            ])
            ->update();

        $this->table('endpoint_permissions')
            ->addForeignKey(
                'endpoint_id',
                'endpoints',
                'id',
                [
                    'constraint' => 'endpointpermissions_endpointid_fk',
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
        $this->table('endpoint_permissions')
            ->dropForeignKey(
                'endpoint_id'
            );

        $this->table('endpoint_permissions')
            ->changeColumn('endpoint_id', 'integer', [
                'comment' => 'link to endpoints.id',
                'default' => null,
                'length' => 5,
                'null' => false,
                'signed' => false,
            ])
            ->update();

        $this->table('endpoint_permissions')
            ->addForeignKey(
                'endpoint_id',
                'endpoints',
                'id',
                [
                    'constraint' => 'endpointpermissions_endpointid_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();
    }
}


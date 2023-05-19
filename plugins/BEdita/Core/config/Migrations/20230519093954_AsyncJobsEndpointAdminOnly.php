<?php
declare(strict_types=1);

use BEdita\Core\Model\Table\RolesTable;
use Migrations\AbstractMigration;

class AsyncJobsEndpointAdminOnly extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->table('endpoints')
            ->insert([
                [
                    'name' => 'async_jobs',
                    'description' => 'Async jobs endpoint',
                    'created' => '2023-05-19 12:06:03',
                    'modified' => '2023-05-19 12:06:03',
                ],
            ])
            ->save();
        $endpointId = (int)$this->getQueryBuilder()
            ->select(['id'])
            ->from(['endpoints'])
            ->where(['name' => 'async_jobs'])
            ->execute()
            ->fetch()[0];
        $this->table('endpoint_permissions')
            ->insert([
                [
                    'endpoint_id' => $endpointId,
                    'role_id' => RolesTable::ADMIN_ROLE,
                    'application_id' => null,
                    'permission' => 15, // 1111
                ],
            ])
            ->save();
    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        $endpointId = (int)$this->getQueryBuilder()
            ->select(['id'])
            ->from(['endpoints'])
            ->where(['name' => 'async_jobs'])
            ->execute()
            ->fetch()[0];
        $endpointPermissionId = (int)$this->getQueryBuilder()
            ->select(['id'])
            ->from(['endpoint_permissions'])
            ->where([
                'endpoint_id' => $endpointId,
                'role_id' => RolesTable::ADMIN_ROLE,
                'application_id IS NULL',
                'permission' => 15,
            ])
            ->execute()
            ->fetch()[0];
        $this->getQueryBuilder()
            ->delete('endpoint_permissions')
            ->where(['id' => $endpointPermissionId])
            ->execute();
        $this->getQueryBuilder()
            ->delete('endpoints')
            ->where(['id' => $endpointId])
            ->execute();
    }
}

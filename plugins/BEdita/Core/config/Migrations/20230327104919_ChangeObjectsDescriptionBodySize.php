<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class ChangeObjectsDescriptionBodySize extends AbstractMigration
{
    /**
     * Up Method.
     *
     * @return void
     */
    public function up()
    {
        $limit = null;
        if ($this->getAdapter()->getAdapterType() === 'mysql') {
            $limit = MysqlAdapter::TEXT_MEDIUM;
        }

        $this->table('objects')
            ->changeColumn('description', 'text', [
                'default' => null,
                'limit' => $limit,
                'null' => true,
            ])
            ->changeColumn('body', 'text', [
                'default' => null,
                'limit' => $limit,
                'null' => true,
            ])
            ->update();
    }

    /**
     * Down Method.
     *
     * @return void
     */
    public function down()
    {
        $this->table('objects')
            ->changeColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->changeColumn('body', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }
}

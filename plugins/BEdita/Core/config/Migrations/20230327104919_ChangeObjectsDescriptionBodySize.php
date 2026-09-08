<?php
declare(strict_types=1);

use Migrations\BaseMigration;
use Migrations\Db\Adapter\MysqlAdapter;

class ChangeObjectsDescriptionBodySize extends BaseMigration
{
    /**
     * Up Method.
     *
     * @return void
     */
    public function up()
    {
        $this->table('objects')
            ->changeColumn('description', 'text', [
                'default' => null,
                'limit' => MysqlAdapter::TEXT_MEDIUM,
                'null' => true,
            ])
            ->changeColumn('body', 'text', [
                'default' => null,
                'limit' => MysqlAdapter::TEXT_MEDIUM,
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

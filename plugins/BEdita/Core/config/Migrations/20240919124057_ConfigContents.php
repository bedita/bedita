<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class ConfigContents extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $limit = null;
        if ($this->adapter->getAdapterType() === 'mysql') {
            $limit = MysqlAdapter::TEXT_MEDIUM;
        }
        $this->table('config')
            ->changeColumn('content', 'text', [
                'comment' => 'configuration data as string or JSON',
                'default' => null,
                'limit' => $limit,
                'null' => false,
            ])
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->table('config')
            ->changeColumn('content', 'text', [
                'comment' => 'configuration data as string or JSON',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->update();
    }
}

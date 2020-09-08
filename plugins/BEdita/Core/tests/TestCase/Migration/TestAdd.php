<?php
namespace BEdita\Core\Test\TestCase\Migration;

use BEdita\Core\Migration\ResourcesMigration;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;

class TestAdd extends ResourcesMigration
{
    protected function getConnection(): Connection
    {
        return ConnectionManager::get('default');
    }
}

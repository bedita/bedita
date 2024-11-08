<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class TreesAddParams extends AbstractMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function change()
    {
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $type = in_array('json', $columnTypes) ? 'json' : 'text';
        $this->table('trees')
            ->addColumn('params', $type, [
                'comment' => 'Parameters for the position on tree (JSON)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }
}

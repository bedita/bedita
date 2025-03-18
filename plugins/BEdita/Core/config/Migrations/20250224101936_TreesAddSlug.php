<?php
declare(strict_types=1);

use Cake\Database\Expression\QueryExpression;
use Migrations\AbstractMigration;

class TreesAddSlug extends AbstractMigration
{
    /**
     * @return void
     */
    public function up()
    {
        $this->table('trees')
            ->addColumn('slug', 'string', [
                'comment' => 'URL-friendly name of an object',
                'default' => null,
                'null' => true,
            ])
            ->addIndex(
                ['slug', 'parent_id'],
                [
                    'name' => 'trees_slugparentid_uq',
                    'unique' => true,
                ],
            )
            ->update();
    }

    /**
     * @return void
     */
    public function down()
    {
        $this->table('trees')
            ->removeIndexByName('trees_slugparentid_uq')
            ->removeColumn('slug')
            ->update();
    }
}

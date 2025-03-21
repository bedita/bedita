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

        $this->getQueryBuilder()
            ->update('trees')
            ->set(
                'slug',
                $this->getQueryBuilder()
                    ->select('objects.uname')
                    ->from('objects')
                    ->where(
                        fn (QueryExpression $exp): QueryExpression => $exp
                            ->equalFields('objects.id', 'trees.object_id')
                    ),
            )
            ->where(fn (QueryExpression $exp): QueryExpression => $exp->isNull('slug'))
            ->execute();

        $this->table('trees')
            ->changeColumn('slug', 'string', [
                'comment' => 'URL-friendly name of an object',
                'default' => null,
                'null' => true,
            ])
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

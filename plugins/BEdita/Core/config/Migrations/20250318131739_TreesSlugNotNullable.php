<?php
declare(strict_types=1);

use Cake\Database\Expression\QueryExpression;
use Cake\Database\Query;
use Migrations\AbstractMigration;

class TreesSlugNotNullable extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getQueryBuilder(Query::TYPE_UPDATE)
            ->update('trees')
            ->set(
                'slug',
                $this->getQueryBuilder(Query::TYPE_SELECT)
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
                'null' => false,
            ])
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        $this->table('trees')
            ->changeColumn('slug', 'string', [
                'comment' => 'URL-friendly name of an object',
                'default' => null,
                'null' => true,
            ])
            ->update();
    }
}

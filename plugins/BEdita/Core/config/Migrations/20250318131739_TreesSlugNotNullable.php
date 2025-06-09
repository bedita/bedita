<?php
declare(strict_types=1);

use Cake\Database\Expression\QueryExpression;
use Migrations\AbstractMigration;

class TreesSlugNotNullable extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
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

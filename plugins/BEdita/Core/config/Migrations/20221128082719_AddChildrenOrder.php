<?php
use Migrations\AbstractMigration;

/**
 * Use dedicated column for children order.
 *
 * @see https://github.com/bedita/bedita/issues/1954
 */
class AddChildrenOrder extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('trees')
            ->addColumn('children_order', 'string', [
                'comment' => 'children order for folders',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('trees')
            ->removeColumn('children_order')
            ->update();
    }
}

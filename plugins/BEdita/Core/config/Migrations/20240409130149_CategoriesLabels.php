<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CategoriesLabels extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        // create field labels
        $this->table('categories')
            ->addColumn('labels', 'json', [
                'default' => null,
                'null' => true,
                'after' => 'name',
            ])
            ->update();
        // copy label into labels.default
        $this->execute(
            'UPDATE categories SET labels = JSON_OBJECT("default", label)'
        );
        // drop field label
        $this->table('categories')
            ->removeColumn('label')
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        // create field label
        $this->table('categories')
            ->addColumn('label', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
                'after' => 'name',
            ])
            ->update();
        // copy labels.default into label
        $this->execute(
            'UPDATE categories SET label = JSON_UNQUOTE(labels->>"$.default")'
        );
        // drop field labels
        $this->table('categories')
            ->removeColumn('labels')
            ->update();
    }
}

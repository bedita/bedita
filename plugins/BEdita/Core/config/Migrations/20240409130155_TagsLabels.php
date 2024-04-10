<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class TagsLabels extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        // create field labels
        $this->table('tags')
            ->addColumn('labels', 'json', [
                'default' => null,
                'null' => true,
                'after' => 'name',
            ])
            ->update();
        // TODO: copy labels.default into label
        // drop field label
        $this->table('tags')
            ->removeColumn('label')
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        // create field label
        $this->table('tags')
            ->addColumn('label', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
                'after' => 'name',
            ])
            ->update();
        // copy labels.default into label
        $this->query(
            'UPDATE tags SET label = JSON_UNQUOTE(labels->>"$.default")'
        );
        // drop field labels
        $this->table('tags')
            ->removeColumn('labels')
            ->update();
    }
}

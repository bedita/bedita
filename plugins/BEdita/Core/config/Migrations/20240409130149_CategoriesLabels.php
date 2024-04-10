<?php
declare(strict_types=1);

use Cake\ORM\Locator\LocatorAwareTrait;
use Migrations\AbstractMigration;

class CategoriesLabels extends AbstractMigration
{
    use LocatorAwareTrait;

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
        $this->fetchTable('categories')
            ->updateAll(
                ['labels' => json_encode(['default' => 'label'])],
                ['labels IS NULL']
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
        $this->query(
            'UPDATE categories SET label = JSON_UNQUOTE(labels->>"$.default")'
        );
        // drop field labels
        $this->table('categories')
            ->removeColumn('labels')
            ->update();
    }
}

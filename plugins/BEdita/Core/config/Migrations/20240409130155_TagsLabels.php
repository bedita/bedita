<?php
declare(strict_types=1);

use Cake\ORM\Locator\LocatorAwareTrait;
use Migrations\AbstractMigration;

class TagsLabels extends AbstractMigration
{
    use LocatorAwareTrait;

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
        // copy label into labels.default
        $this->fetchTable('tags')
            ->updateAll(
                ['labels' => json_encode(['default' => 'label'])],
                ['labels IS NULL']
            );
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

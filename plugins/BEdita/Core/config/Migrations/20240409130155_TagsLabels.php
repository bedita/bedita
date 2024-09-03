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
        $statement = [
            'mysql' => 'JSON_OBJECT("default", label)',
            'mariadb' => 'JSON_OBJECT("default", label)',
            'pgsql' => 'JSON_BUILD_OBJECT(\'default\', label)',
            'sqlite' => 'JSON(\'{"default": "\' || label || \'"}\')',
        ];
        $this->query(
            'UPDATE tags SET labels = ' . $statement[$this->getAdapter()->getAdapterType()]
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
        $statement = [
            'mysql' => 'JSON_UNQUOTE(labels->>"$.default")',
            'mariadb' => 'JSON_UNQUOTE(labels->>"$.default")',
            'pgsql' => 'labels->>\'default\'',
            'sqlite' => 'labels->>\'$.default\'',
        ];
        $this->query(
            'UPDATE tags SET label ' . $statement[$this->getAdapter()->getAdapterType()]
        );
        // drop field labels
        $this->table('tags')
            ->removeColumn('labels')
            ->update();
    }
}

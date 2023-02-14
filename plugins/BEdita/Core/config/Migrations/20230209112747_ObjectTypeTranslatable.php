<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class ObjectTypeTranslatable extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $json = in_array('json', $columnTypes) ? 'json' : 'text';

        $this->table('object_types')
            ->addColumn('translation_rules', $json, [
                'comment' => 'rules to use when translating an object: properties always and never translatable',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->addColumn('is_translatable', 'boolean', [
                'comment' => 'this object type is translatable?',
                'default' => true,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->table('object_types')
            ->removeColumn('translation_rules')
            ->removeColumn('is_translatable')
            ->update();
    }
}

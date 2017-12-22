<?php
use Migrations\AbstractMigration;

/**
 * Add default property types.
 */
class AddDefaultPropertyTypes extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('properties')
            ->removeColumn('multiple')
            ->removeColumn('options_list')
            ->removeColumn('list_view')
            ->update();

        $this->table('properties')
            ->addColumn('is_nullable', 'boolean', [
                'after' => 'label',
                'comment' => 'is property nullable?',
                'default' => '1',
                'length' => null,
                'null' => false,
            ])
            ->update();

        $this->table('property_types')
            ->insert([
                [
                    'name' => 'string',
                    'params' => '{"type":"string"}',
                ],
                [
                    'name' => 'text',
                    'params' => '{"type":"string","contentMediaType":"text/html"}',
                ],
                [
                    'name' => 'status',
                    'params' => '{"type":"string","enum":["on","off","draft"]}',
                ],
                [
                    'name' => 'email',
                    'params' => '{"type":"string","format":"email"}',
                ],
                [
                    'name' => 'url',
                    'params' => '{"type":"string","format":"uri"}',
                ],
                [
                    'name' => 'date',
                    'params' => '{"type":"string","format":"date-time"}',
                ],
                [
                    'name' => 'number',
                    'params' => '{"type":"number"}',
                ],
                [
                    'name' => 'integer',
                    'params' => '{"type":"integer"}',
                ],
                [
                    'name' => 'boolean',
                    'params' => '{"type":"boolean"}',
                ],
                [
                    'name' => 'json',
                    'params' => '{"type":"object"}',
                ],
            ])
            ->save();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('properties')
            ->addColumn('multiple', 'boolean', [
                'after' => 'property_type_id',
                'comment' => 'multiple values for this property?',
                'default' => '0',
                'length' => null,
                'null' => true,
            ])
            ->addColumn('options_list', 'text', [
                'after' => 'multiple',
                'comment' => 'property predefined options list',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->addColumn('list_view', 'boolean', [
                'after' => 'label',
                'comment' => 'property displayed in list view backend operations',
                'default' => '1',
                'length' => null,
                'null' => false,
            ])
            ->removeColumn('is_nullable')
            ->update();

        $this->execute('DELETE FROM property_types');
    }
}


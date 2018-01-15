<?php
use Migrations\AbstractMigration;

/**
 * Add `integer` to default property types.
 */
class AddIntegerPropertyType extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('property_types')
            ->insert([
                [
                    'name' => 'integer',
                    'params' => '{"type":"integer"}',
                ],
            ])
            ->save();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
    }
}


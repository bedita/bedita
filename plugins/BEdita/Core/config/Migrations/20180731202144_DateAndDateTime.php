<?php
use Migrations\AbstractMigration;

/**
 * Add `datetime` and update `date` property types.
 */
class DateAndDateTime extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('property_types')
            ->insert([
                [
                    'name' => 'datetime',
                    'params' => '{"type":"string","format":"date-time"}',
                ],
            ])
            ->save();

        $dateParams = '{"type":"string","format":"date"}';
        $this->query(sprintf("UPDATE property_types SET params = '%s' WHERE name = 'date'", $dateParams));
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->execute("DELETE FROM property_types where name = 'datetime'");
        $dateParams = '{"type":"string","format":"date-time"}';
        $this->query(sprintf("UPDATE property_types SET params = '%s' WHERE name = 'date'", $dateParams));
    }
}

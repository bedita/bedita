<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class FixPropertiesForeignKeys extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $missingForeignKeys = false;
        $table = $this->table('properties');
        if (!$table->hasForeignKey('object_type_id')) {
            $missingForeignKeys = true;
            $table->addForeignKey(
                'object_type_id',
                'object_types',
                'id',
                [
                    'constraint' => 'properties_objtype_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'CASCADE'
                ]
            );
        }

        if (!$table->hasForeignKey('property_type_id')) {
            $missingForeignKeys = true;
            $table->addForeignKey(
                'property_type_id',
                'property_types',
                'id',
                [
                    'constraint' => 'properties_proptype_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'CASCADE'
                ]
            );
        }

        if ($missingForeignKeys) {
            $table->update();
        }
    }
}

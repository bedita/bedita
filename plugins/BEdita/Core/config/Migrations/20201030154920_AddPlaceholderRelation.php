<?php

use Cake\ORM\Table;
use Migrations\AbstractMigration;

class AddPlaceholderRelation extends AbstractMigration
{

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->table('relations')
            ->insert([
                [
                    'name' => 'placeholder',
                    'label' => 'Placeholder',
                    'inverse_name' => 'placeholded',
                    'inverse_label' => 'Placeholded in',
                    'description' => 'Relation to link objects with other objects that appear in the body of the former.',
                    'params' => null,
                ],
            ])
            ->save();

        // Populate tree data. We'll be using a clean CakePHP table object to be able to use Tree behavior.
        /* @var \Migrations\CakeAdapter $adapter */
        $adapter = $this->getAdapter();
        $relationsTable = new Table([
            'table' => 'relations',
            'connection' => $adapter->getCakeConnection(),
        ]);
        $placeholderRID = $relationsTable->find()->where(['name' => 'placeholder'])->firstOrFail()->id;

        $objectTypesTable = new Table([
            'table' => 'object_types',
            'connection' => $adapter->getCakeConnection(),
        ]);
        $objectsOTID = $objectTypesTable->find()->where(['name' => 'objects'])->firstOrFail()->id;

        $this->table('relation_types')
            ->insert([
                [
                    'relation_id' => $placeholderRID,
                    'object_type_id' => $objectsOTID,
                    'side' => 'left',
                ],
                [
                    'relation_id' => $placeholderRID,
                    'object_type_id' => $objectsOTID,
                    'side' => 'right',
                ],
            ])
            ->save();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $adapter = $this->getAdapter();
        $table = new Table([
            'table' => 'relations',
            'connection' => $adapter->getCakeConnection(),
        ]);
        $table->delete($table->find()->where(['name' => 'placeholder'])->firstOrFail());
    }
}

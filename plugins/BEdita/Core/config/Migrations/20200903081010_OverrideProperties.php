<?php
use BEdita\Core\Utility\Resources;
use Migrations\AbstractMigration;

/**
 * New column `properties.is_static` to override property type of static property
 * New core property type `plain_text`
 */
class OverrideProperties extends AbstractMigration
{
    protected $create = [
        'property_types' => [
            [
                'name' => 'text_plain',
                'params' => [
                    'type' => 'string',
                    'contentMediaType' => 'text/plain',
                ],
                'core_type' => 1,
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('properties')
            ->addColumn('is_static', 'boolean', [
                'after' => 'is_nullable',
                'comment' => 'is property static? i.e. a table column',
                'default' => '0',
                'length' => null,
                'null' => false,
            ])
            ->update();

        Resources::save(
            ['create' => $this->create],
            ['connection' => $this->getAdapter()->getCakeConnection()]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('properties')
            ->removeColumn('is_static')
            ->update();

        Resources::save(
            ['remove' => $this->create],
            ['connection' => $this->getAdapter()->getCakeConnection()]
        );
    }
}

<?php
use BEdita\Core\Utility\Resources;
use Cake\ORM\Table;
use Migrations\AbstractMigration;

class PublicationsTable extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        // links
        $this->table('publications', ['id' => false])
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('public_name', 'string', [
                'comment' => 'the public name',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('public_url', 'string', [
                'comment' => 'the public url',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('staging_url', 'string', [
                'comment' => 'the staging url',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('stats_code', 'string', [
                'comment' => 'the code for statistics',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('publications')
            ->addForeignKey(
                'id',
                'objects',
                'id',
                [
                    'constraint' => 'publications_id_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

            $this->table('object_types')
                ->insert([
                    [
                        'name' => 'publications',
                        'singular' => 'publication',
                        'description' => 'Publications model',
                        'plugin' => 'BEdita/Core',
                        'model' => 'Publications',
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
                        'parent_id' => 1,
                        'core_type' => 1,
                    ],
                ])
                ->save();

            $this->recoverTree();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('publications')
            ->drop()
            ->save();

        $this->query("DELETE FROM object_types WHERE name = 'publications'");
        $this->recoverTree();
    }

    /**
     * Recover `object_types` tree
     *
     * @return void
     */
    protected function recoverTree(): void
    {
        $table = new Table([
            'table' => 'object_types',
            'connection' => $this->getAdapter()->getCakeConnection(),
        ]);
        $table->addBehavior('BEdita/Core.Tree', [
            'left' => 'tree_left',
            'right' => 'tree_right',
        ]);
        /* @var \BEdita\Core\Model\Behavior\TreeBehavior $tree */
        $tree = $table->behaviors()->get('Tree');
        $tree->nonAtomicRecover();
    }
}

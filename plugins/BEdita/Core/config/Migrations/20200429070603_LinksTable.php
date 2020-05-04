<?php
use BEdita\Core\Utility\Resources;
use Cake\ORM\Table;
use Migrations\AbstractMigration;

class LinksTable extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        // links
        $this->table('links', ['id' => false])
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('url', 'string', [
                'comment' => 'Url',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('http_status', 'string', [
                'comment' => 'HTTP status',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('last_update', 'timestamp', [
                'comment' => 'Last update date',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('links')
            ->addForeignKey(
                'id',
                'objects',
                'id',
                [
                    'constraint' => 'links_id_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

            $this->table('object_types')
                ->insert([
                    [
                        'name' => 'links',
                        'singular' => 'link',
                        'description' => 'Links model',
                        'plugin' => 'BEdita/Core',
                        'model' => 'Links',
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
        $this->table('links')
            ->drop()
            ->save();

        $this->query("DELETE FROM object_types WHERE name = 'links'");
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

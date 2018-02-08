<?php
use Migrations\AbstractMigration;

class RenameTreesParentNodeId extends AbstractMigration
{

    public function up()
    {
        $this->table('trees')
            ->dropForeignKey([], 'trees_parentid_fk')
            ->removeIndexByName('trees_parentid_idx')
            ->removeIndexByName('trees_rootleft_idx')
            ->removeIndexByName('trees_rootright_idx')
            ->update();

        $this->table('trees')
            ->renameColumn('parent_id', 'parent_node_id')
            ->changeColumn('menu', 'boolean', [
                'default' => '1',
                'limit' => null,
                'null' => false,
            ])
            ->update();

        $this->table('trees')
            ->addIndex(
                [
                    'tree_left',
                ],
                [
                    'name' => 'trees_left_idx',
                ]
            )
            ->addIndex(
                [
                    'tree_right',
                ],
                [
                    'name' => 'trees_right_idx',
                ]
            )
            ->addIndex(
                [
                    'parent_node_id',
                ],
                [
                    'name' => 'trees_parentnodeid_idx',
                ]
            )
            ->update();

        $this->table('trees')
            ->addForeignKey(
                'parent_node_id',
                'trees',
                'id',
                [
                    'constraint' => 'trees_parentnodeid_fk',
                    'update' => 'NO ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('trees')
            ->dropForeignKey(
                'parent_node_id'
            );

        $this->table('trees')
            ->removeIndexByName('trees_left_idx')
            ->removeIndexByName('trees_right_idx')
            ->removeIndexByName('trees_parentid_idx')
            ->update();

        $this->table('trees')
            ->renameColumn('parent_node_id', 'parent_id')
            ->changeColumn('menu', 'integer', [
                'comment' => 'menu on/off',
                'default' => '1',
                'length' => 10,
                'null' => false,
            ])
            ->addIndex(
                [
                    'parent_id',
                ],
                [
                    'name' => 'trees_parentid_idx',
                ]
            )
            ->addIndex(
                [
                    'root_id',
                    'tree_left',
                ],
                [
                    'name' => 'trees_rootleft_idx',
                ]
            )
            ->addIndex(
                [
                    'root_id',
                    'tree_right',
                ],
                [
                    'name' => 'trees_rootright_idx',
                ]
            )
            ->update();

        $this->table('trees')
            ->addForeignKey(
                'parent_id',
                'objects',
                'id',
                [
                    'constraint' => 'trees_parentid_fk',
                    'update' => 'NO ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }
}


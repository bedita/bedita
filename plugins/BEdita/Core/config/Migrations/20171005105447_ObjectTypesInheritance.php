<?php

use Cake\ORM\Table;
use Migrations\AbstractMigration;

/**
 * Add inheritance to object types.
 *
 * This migration class also fixes tree information (left and right indexes, as prescribed by Nested-Set Model)
 * in order to make things work as expected from the very beginning.
 */
class ObjectTypesInheritance extends AbstractMigration
{

    /**
     * [@inheritDoc}
     */
    public function up()
    {

        $this->table('object_types')
            ->addColumn('parent_id', 'integer', [
                'after' => 'id',
                'comment' => 'Parent object type ID',
                'default' => null,
                'length' => 5,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('tree_left', 'integer', [
                'after' => 'parent_id',
                'comment' => 'Left counter',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('tree_right', 'integer', [
                'after' => 'tree_left',
                'comment' => 'Right counter',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('is_abstract', 'boolean', [
                'after' => 'tree_right',
                'comment' => 'Is the type abstract?',
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'parent_id',
                ],
                [
                    'name' => 'objecttypes_parentid_idx',
                ]
            )
            ->addIndex(
                [
                    'tree_left',
                ],
                [
                    'name' => 'objecttypes_treeleft_idx',
                ]
            )
            ->addIndex(
                [
                    'tree_right',
                ],
                [
                    'name' => 'objecttypes_treeright_idx',
                ]
            )
            ->addIndex(
                [
                    'is_abstract',
                ],
                [
                    'name' => 'objecttypes_isabstract_idx',
                ]
            )
            ->update();

        $this->table('object_types')
            ->addForeignKey(
                'parent_id',
                'object_types',
                'id',
                [
                    'constraint' => 'objecttypes_parentid_fk',
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        // Populate tree data. We'll be using a clean CakePHP table object to be able to use Tree behavior.
        /* @var \Migrations\CakeAdapter $adapter */
        $adapter = $this->getAdapter();
        $table = new Table([
            'table' => 'object_types',
            'connection' => $adapter->getCakeConnection(),
        ]);

        $table->updateAll( // "objects" and "media" are abstract.
            [
                'is_abstract' => true,
            ],
            [
                'name IN' => ['objects', 'media'],
            ]
        );
        $table->updateAll( // Everything inherits from "object"...
            [
                'parent_id' => 1,
            ],
            [
                'id <>' => 1,
            ]
        );
        $table->updateAll( // ...except "users", that inherits from "profiles"...
            [
                'parent_id' => $table->find()->where(['name' => 'profiles'])->firstOrFail()->id,
            ],
            [
                'name' => 'users',
            ]
        );
        $table->updateAll( // ...and "images", that inherits from "media".
            [
                'parent_id' => $table->find()->where(['name' => 'media'])->firstOrFail()->id,
            ],
            [
                'name' => 'images',
            ]
        );

        // Now let's fix NSM (nested-set model) left and right indexes from tree data.
        $table->addBehavior('Tree', [
            'left' => 'tree_left',
            'right' => 'tree_right',
        ]);
        /* @var \Cake\ORM\Behavior\TreeBehavior $tree */
        $tree = $table->behaviors()->get('Tree');
        $tree->recover();
    }

    /**
     * [@inheritDoc}
     */
    public function down()
    {
        $this->table('object_types')
            ->dropForeignKey(
                'parent_id'
            );

        $this->table('object_types')
            ->removeIndexByName('objecttypes_parentid_idx')
            ->removeIndexByName('objecttypes_treeleft_idx')
            ->removeIndexByName('objecttypes_treeright_idx')
            ->removeIndexByName('objecttypes_isabstract_idx')
            ->update();

        $this->table('object_types')
            ->removeColumn('parent_id')
            ->removeColumn('tree_left')
            ->removeColumn('tree_right')
            ->removeColumn('is_abstract')
            ->update();
    }
}


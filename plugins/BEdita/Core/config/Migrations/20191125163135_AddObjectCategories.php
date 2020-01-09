<?php
use Migrations\AbstractMigration;

class AddObjectCategories extends AbstractMigration
{
    public $autoId = false;

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('categories')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('object_type_id', 'integer', [
                'comment' => 'Link to object type',
                'default' => null,
                'limit' => 10,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('name', 'string', [
                'comment' => 'category name, lower case and unique per object type',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('label', 'string', [
                'comment' => 'category label',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('parent_id', 'integer', [
                'comment' => 'parent category ID',
                'default' => null,
                'length' => 10,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('tree_left', 'integer', [
                'comment' => 'tree left counter (nested set model)',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('tree_right', 'integer', [
                'comment' => 'tree right counter (nested set model)',
                'default' => null,
                'length' => 11,
                'null' => true,
            ])
            ->addColumn('enabled', 'boolean', [
                'comment' => 'category active flag',
                'default' => true,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'object_type_id',
                ],
                [
                    'name' => 'categories_objecttypeid_idx',
                ]
            )
            ->addIndex(
                [
                    'object_type_id',
                    'name',
                ],
                [
                    'name' => 'categories_objecttypeidname_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'parent_id',
                ],
                [
                    'name' => 'categories_parentid_idx',
                ]
            )
            ->addIndex(
                [
                    'tree_left',
                ],
                [
                    'name' => 'categories_treeleft_idx',
                ]
            )
            ->addIndex(
                [
                    'tree_right',
                ],
                [
                    'name' => 'categories_treeright_idx',
                ]
            )
            ->addForeignKey(
                'object_type_id',
                'object_types',
                'id',
                [
                    'constraint' => 'categories_objecttypesid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE',
                ]
            )
            ->create();

        $this->table('object_categories')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('object_id', 'integer', [
                'comment' => 'object - link to objects.id',
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('category_id', 'integer', [
                'comment' => 'category - link to categories.id',
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('params', 'text', [
                'comment' => 'association parameters (JSON data)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'object_id',
                    'category_id',
                ],
                [
                    'name' => 'objectcategories_objectrole_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'object_id',
                ],
                [
                    'name' => 'objectcategories_objectid_idx',
                ]
            )
            ->addIndex(
                [
                    'category_id',
                ],
                [
                    'name' => 'objectcategories_categoryid_idx',
                ]
            )
            ->addForeignKey(
                'object_id',
                'objects',
                'id',
                [
                    'constraint' => 'objectcategories_objectid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE',
                ]
            )
            ->addForeignKey(
                'category_id',
                'categories',
                'id',
                [
                    'constraint' => 'objectcategories_categoryid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE',
                ]
            )
            ->create();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('object_categories')
            ->drop()
            ->save();
        $this->table('categories')
            ->drop()
            ->save();
    }
}

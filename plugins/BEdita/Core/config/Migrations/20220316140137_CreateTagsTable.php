<?php

use Cake\Database\Expression\QueryExpression;
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * Split tags and categories into their own tables.
 */
class CreateTagsTable extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public $autoId = false;

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        /*
         * Create tables `tags` and `object_tags`:
         */
        $this->table('tags')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'comment' => 'Tag name, URL-friendly and unique.',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('label', 'string', [
                'comment' => 'Tag label, human-friendly.',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('enabled', 'boolean', [
                'comment' => 'Tag enabled/disabled.',
                'default' => true,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'Creation date.',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'Last modification date.',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->addIndex(['name'], ['name' => 'tags_name_uq', 'unique' => true])
            ->create();

        $this->table('object_tags')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('object_id', 'integer', [
                'comment' => 'Object ID.',
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('tag_id', 'integer', [
                'comment' => 'Tag ID.',
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addIndex(['object_id', 'tag_id'], ['name' => 'objecttags_objectidtagid_uq', 'unique' => true])
            ->addIndex(['tag_id'], ['name' => 'objecttags_tagid_idx'])
            ->addForeignKey(
                'object_id',
                'objects',
                'id',
                [
                    'constraint' => 'objecttags_objectid_fk',
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                ]
            )
            ->addForeignKey(
                'tag_id',
                'tags',
                'id',
                [
                    'constraint' => 'objecttags_tagid_fk',
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                ]
            )
            ->create();

        /*
         * Move tags from `categories` to `tags`.
         */
        $this->transactional(function () {
            $this->getQueryBuilder()
                ->insert(['name', 'label', 'created', 'modified'])
                ->into('tags')
                ->values(
                    $this->getQueryBuilder()
                        ->select(['name', 'label', 'created', 'modified'])
                        ->from('categories')
                        ->where(function (QueryExpression $exp): QueryExpression {
                            return $exp->isNull('object_type_id');
                        })
                )
                ->execute();
            $this->getQueryBuilder()
                ->insert(['object_id', 'tag_id'])
                ->into('object_tags')
                ->values(
                    $this->getQueryBuilder()
                        ->select(['object_categories.object_id', 'tags.id'])
                        ->from('object_categories')
                        ->innerJoin('categories', function (QueryExpression $exp) {
                            return $exp->equalFields('categories.id', 'object_categories.category_id')
                                ->isNull('categories.object_type_id');
                        })
                        ->innerJoin('tags', function (QueryExpression $exp) {
                            return $exp->equalFields('tags.name', 'categories.name');
                        })
                )
                ->execute();
            $this->getQueryBuilder()
                ->delete('categories')
                ->where(function (QueryExpression $exp) {
                    return $exp->isNull('object_type_id');
                })
                ->execute();
        });

        /**
         * Drop foreign key categories_objecttypesid_fk, before updating
         */
        $this->table('categories')
            ->dropForeignKey('object_type_id')
            ->update();

        /*
         * Make `categories.object_type_id` not nullable.
         */
        $this->table('categories')
            ->changeColumn('object_type_id', 'integer', [
                'comment' => 'Link to object type',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->update();

        /**
         * Add categories_objecttypesid_fk foreign key
         */
        $this->table('categories')->addForeignKey(
                'object_type_id',
                'object_types',
                'id',
                [
                    'constraint' => 'categories_objecttypesid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE',
                ]
            )
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        /**
         * Drop foreign key categories_objecttypesid_fk, before updating
         */
        $this->table('categories')
            ->dropForeignKey('object_type_id')
            ->update();

        /*
         * Make `categories.object_type_id` nullable.
         */
        $this->table('categories')
            ->changeColumn('object_type_id', 'integer', [
                'comment' => 'Link to object type',
                'default' => null,
                'limit' => 10,
                'null' => true,
                'signed' => false,
            ])
            ->update();

        /**
         * Add categories_objecttypesid_fk foreign key
         */
        $this->table('categories')->addForeignKey(
            'object_type_id',
            'object_types',
            'id',
            [
                'constraint' => 'categories_objecttypesid_fk',
                'update' => 'NO_ACTION',
                'delete' => 'CASCADE',
            ]
        )
        ->update();

        /*
         * Move tags from `tags` to `categories`.
         */
        $this->transactional(function () {
            $this->getQueryBuilder()
                ->insert(['name', 'label', 'created', 'modified'])
                ->into('categories')
                ->values(
                    $this->getQueryBuilder()
                        ->select(['name', 'label', 'created', 'modified'])
                        ->from('tags')
                )
                ->execute();
            $this->getQueryBuilder()
                ->insert(['object_id', 'category_id'])
                ->into('object_categories')
                ->values(
                    $this->getQueryBuilder()
                        ->select(['object_tags.object_id', 'categories.id'])
                        ->from('object_tags')
                        ->innerJoin('tags', function (QueryExpression $exp) {
                            return $exp->equalFields('tags.id', 'object_tags.tag_id');
                        })
                        ->innerJoin('categories', function (QueryExpression $exp) {
                            return $exp->equalFields('categories.name', 'tags.name')
                                ->isNull('categories.object_type_id');
                        })
                )
                ->execute();
        });

        /*
         * Drop `tags` and `object_tags`.
         */
        $this->table('object_tags')
            ->drop()
            ->save();
        $this->table('tags')
            ->drop()
            ->save();
    }

    /**
     * Execute multiple queries wrapped within a transaction, if needed.
     *
     * @param callable $cb Callable.
     * @return void
     */
    protected function transactional(callable $cb): void
    {
        $adapter = $this->getAdapter();
        if (!$adapter instanceof MysqlAdapter) {
            // Only in MySQL DDL statements cause an implicit commit.
            // For other drivers, just work with the currently active transaction.
            $cb();

            return;
        }

        $adapter->beginTransaction();
        try {
            $cb();
            $adapter->commitTransaction();
        } catch (Throwable $e) {
            $adapter->rollbackTransaction();
        }
    }
}

<?php
declare(strict_types=1);

/**
 * Abstract schema for fake tables not actually present in BEdita migrations.
 *
 * This format resembles the existing fixture schema
 * and is converted to SQL via the Schema generation
 * features of the Database package.
 */
return [
    [
        'table' => 'fake_animals',
        'columns' => [
            'id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => null,
                'precision' => null,
                'autoIncrement' => true,
            ],
            'name' => [
                'type' => 'string',
                'length' => 255,
                'null' => true,
                'default' => null,
                'precision' => null,
            ],
            'legs' => [
                'type' => 'integer',
                'length' => 2,
                'null' => true,
                'default' => null,
                'precision' => null,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
                'default' => null,
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
            ],
        ],
    ],
    [
        'table' => 'fake_mammals',
        'columns' => [
            'id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => null,
                'precision' => null,
                'autoIncrement' => null,
            ],
            'subclass' => [
                'type' => 'string',
                'length' => 255,
                'null' => true,
                'default' => null,
                'precision' => null,
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
            ],
            'fakemammals_fk' => [
                'type' => 'foreign',
                'columns' => ['id'],
                'references' => ['fake_animals', 'id'],
                'update' => 'noAction',
                'delete' => 'noAction',
            ],
        ],
    ],
    [
        'table' => 'fake_felines',
        'columns' => [
            'id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => null,
                'precision' => null,
                'autoIncrement' => null,
            ],
            'family' => [
                'type' => 'string',
                'length' => 255,
                'null' => true,
                'default' => null,
                'precision' => null,
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
            ],
            'fakefelines_fk' => [
                'type' => 'foreign',
                'columns' => ['id'],
                'references' => ['fake_mammals', 'id'],
                'update' => 'noAction',
                'delete' => 'noAction',
            ],
        ],
    ],
    [
        'table' => 'fake_articles',
        'columns' => [
            'id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => null,
                'precision' => null,
                'autoIncrement' => true,
            ],
            'title' => [
                'type' => 'string',
                'length' => 255,
                'null' => false,
                'default' => null,
                'precision' => null,
            ],
            'body' => ['type' => 'text'],
            'fake_animal_id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false,
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
            ],
            'fakearticles_fk_1' => [
                'type' => 'foreign',
                'columns' => ['fake_animal_id'],
                'references' => ['fake_animals', 'id'],
                'update' => 'noAction',
                'delete' => 'noAction',
            ],
        ],
    ],
    [
        'table' => 'fake_tags',
        'columns' => [
            'id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => null,
                'precision' => null,
                'autoIncrement' => true,
            ],
            'name' => [
                'type' => 'string',
                'length' => 255,
                'null' => false,
                'default' => null,
                'precision' => null,
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
            ],
        ],
    ],
    [
        'table' => 'fake_articles_tags',
        'columns' => [
            'id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => null,
                'precision' => null,
                'autoIncrement' => true,
            ],
            'fake_article_id' => [
                'type' => 'integer',
                'null' => true,
            ],
            'fake_tag_id' => [
                'type' => 'integer',
                'null' => true,
            ],
            'fake_params' => [
                'type' => 'string',
                'length' => 255,
                'null' => true,
                'default' => null,
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
            ],
        ],
    ],
    [
        'table' => 'fake_categories',
        'columns' => [
            'id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => null,
                'precision' => null,
                'autoIncrement' => true,
            ],
            'name' => [
                'type' => 'string',
                'length' => 255,
                'null' => false,
                'default' => null,
                'precision' => null,
            ],
            'parent_id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => true,
                'default' => null,
                'precision' => null,
            ],
            'left_idx' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => null,
                'precision' => null,
            ],
            'right_idx' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => null,
                'precision' => null,
            ],
        ],
        'indexes' => [
            'fakecategories_parentid_idx' => [
                'type' => 'index',
                'columns' => [
                    'parent_id',
                ],
            ],
            'fakecategories_leftright_idx' => [
                'type' => 'index',
                'columns' => [
                    'left_idx',
                    'right_idx',
                ],
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
            ],
            'fakecategories_parentid_fk' => [
                'type' => 'foreign',
                'columns' => ['parent_id'],
                'references' => ['fake_categories', 'id'],
                'update' => 'noAction',
                'delete' => 'noAction',
            ],
        ],
    ],
    [
        'table' => 'fake_labels',
        'columns' => [
            'id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => null,
                'comment' => '',
                'precision' => null,
                'autoIncrement' => true,
            ],
            'fake_tag_id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => true,
                'default' => null,
                'precision' => null,
            ],
            'color' => [
                'type' => 'string',
                'length' => 255,
                'null' => false,
                'default' => null,
                'precision' => null,
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
            ],
            'fakelabels_tagid_fk' => [
                'type' => 'foreign',
                'columns' => ['fake_tag_id'],
                'references' => ['fake_tags', 'id'],
                'update' => 'noAction',
                'delete' => 'noAction',
            ],
        ],
    ],
    [
        'table' => 'fake_searches',
        'columns' => [
            'id' => [
                'type' => 'integer',
                'length' => 10,
                'unsigned' => true,
                'null' => false, 'default' => null,
                'precision' => null,
                'autoIncrement' => true,
            ],
            'name' => [
                'type' => 'string',
                'length' => 255,
                'null' => true,
                'default' => null,
                'precision' => null,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
                'default' => null,
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
            ],
        ],
    ],
    [
        'table' => 'json_schema_table',
        'columns' => [
            'id' => [
                'type' => 'integer',
                'length' => 5,
                'unsigned' => true,
                'null' => false,
                'default' => null,
                'autoIncrement' => true,
                'precision' => null,
            ],
            'name' => [
                'type' => 'string',
                'length' => 255,
                'null' => false,
                'default' => null,
                'precision' => null,
                'fixed' => null,
            ],
            'json_field' => [
                'type' => 'text',
                'length' => null,
                'null' => true,
                'default' => null,
                'precision' => null,
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
            ],
        ],
    ],
];

<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\Fixture;

use Cake\Database\Driver\Postgres;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * ObjectsFixture
 */
class ObjectsFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        // 1
        [
            'object_type_id' => 4,
            'status' => 'on',
            'uname' => 'first-user',
            'locked' => 1,
            'deleted' => 0,
            'created' => '2016-05-13 07:09:23',
            'modified' => '2016-05-13 07:09:23',
            'published' => null,
            'title' => 'Mr. First User',
            'description' => null,
            'body' => null,
            'extra' => null,
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
        ],
        // 2
        [
            'object_type_id' => 2,
            'status' => 'on',
            'uname' => 'title-one',
            'locked' => 1,
            'deleted' => 0,
            'created' => '2016-05-13 07:09:23',
            'modified' => '2016-05-13 07:09:23',
            'published' => '2016-05-13 07:09:23',
            'title' => 'title one',
            'description' => 'description here',
            'body' => 'body here',
            'extra' => [
                'abstract' => 'abstract here',
                'list' => ['one', 'two', 'three'],
            ],
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => '2016-05-13 07:09:23',
            'publish_end' => '2016-05-13 07:09:23',
        ],
        // 3
        [
            'object_type_id' => 2,
            'status' => 'draft',
            'uname' => 'title-two',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2016-05-12 07:09:23',
            'modified' => '2016-05-13 08:30:00',
            'published' => null,
            'title' => 'title two',
            'description' => 'description here',
            'body' => 'body here',
            'extra' => null,
            'lang' => null,
            'created_by' => 1,
            'modified_by' => 5,
            'publish_start' => null,
            'publish_end' => null,
        ],
        // 4
        [
            'object_type_id' => 3,
            'status' => 'on',
            'uname' => 'gustavo-supporto',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2016-05-13 07:09:23',
            'modified' => '2016-05-13 07:09:23',
            'published' => null,
            'title' => 'Gustavo Supporto profile',
            'description' => 'Some description about Gustavo',
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
        ],
        // 5
        [
            'object_type_id' => 4,
            'status' => 'on',
            'uname' => 'second-user',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2016-05-13 07:09:23',
            'modified' => '2016-05-13 07:09:23',
            'published' => null,
            'title' => 'Miss Second User',
            'description' => null,
            'body' => null,
            'extra' => null,
            'lang' => 'en',
            'created_by' => 5,
            'modified_by' => 5,
            'custom_props' => [
                'another_username' => 'synapse',
                'another_email' => 'synapse@example.org',
            ],
        ],
        // 6
        [
            'object_type_id' => 2,
            'status' => 'on',
            'uname' => 'title-one-deleted',
            'locked' => 0,
            'deleted' => 1,
            'created' => '2016-10-13 07:09:23',
            'modified' => '2016-10-13 07:09:23',
            'published' => '2016-10-13 07:09:23',
            'title' => 'title one deleted',
            'description' => 'description removed',
            'body' => 'body no more',
            'extra' => ['abstract' => 'what?'],
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => '2016-10-13 07:09:23',
            'publish_end' => '2016-10-13 07:09:23',
        ],
        // 7
        [
            'object_type_id' => 2,
            'status' => 'on',
            'uname' => 'title-two-deleted',
            'locked' => 0,
            'deleted' => 1,
            'created' => '2016-10-13 07:09:23',
            'modified' => '2016-10-13 07:09:23',
            'published' => '2016-10-13 07:09:23',
            'title' => 'title two deleted',
            'description' => 'description removed',
            'body' => 'body no more',
            'extra' => ['abstract' => 'what?'],
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => '2016-10-13 07:09:23',
            'publish_end' => '2016-10-13 07:09:23',
        ],
        // 8
        [
            'object_type_id' => 6,
            'status' => 'on',
            'uname' => 'the-two-towers',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2017-02-20 07:09:23',
            'modified' => '2017-02-20 07:09:23',
            'published' => '2017-02-20 07:09:23',
            'title' => 'The Two Towers',
            'description' => null,
            'body' => null,
            'extra' => null,
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => null,
            'publish_end' => null,
        ],
        // 9
        [
            'object_type_id' => 7,
            'status' => 'on',
            'uname' => 'event-one',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2017-03-08 07:09:23',
            'modified' => '2016-03-08 08:30:00',
            'published' => null,
            'title' => 'first event',
            'description' => 'event description goes here',
            'body' => null,
            'extra' => null,
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => null,
            'publish_end' => null,
        ],
        // 10
        [
            'object_type_id' => 9,
            'status' => 'on',
            'uname' => 'media-one',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2017-03-08 07:09:23',
            'modified' => '2017-03-08 08:30:00',
            'published' => null,
            'title' => 'first media',
            'description' => 'media description goes here',
            'body' => null,
            'extra' => null,
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => null,
            'publish_end' => null,
            'custom_props' => ['media_property' => true],
        ],
        // 11
        [
            'object_type_id' => 10,
            'status' => 'on',
            'uname' => 'root-folder',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2018-01-31 07:09:23',
            'modified' => '2018-01-31 08:30:00',
            'published' => null,
            'title' => 'Root Folder',
            'description' => 'first root folder',
            'body' => null,
            'extra' => null,
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => null,
            'publish_end' => null,
        ],
        // 12
        [
            'object_type_id' => 10,
            'status' => 'on',
            'uname' => 'sub-folder',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2018-01-31 07:09:23',
            'modified' => '2018-01-31 08:30:00',
            'published' => null,
            'title' => 'Sub Folder',
            'description' => 'sub folder of root folder',
            'body' => null,
            'extra' => null,
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => null,
            'publish_end' => null,
        ],
        // 13
        [
            'object_type_id' => 10,
            'status' => 'on',
            'uname' => 'another-root-folder',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2018-03-08 12:20:00',
            'modified' => '2018-03-08 12:20:00',
            'published' => null,
            'title' => 'Another Root Folder',
            'description' => 'second root folder',
            'body' => null,
            'extra' => null,
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => null,
            'publish_end' => null,
        ],
        // 14
        [
            'object_type_id' => 9,
            'status' => 'on',
            'uname' => 'media-two',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2018-03-22 16:42:31',
            'modified' => '2018-03-22 16:42:31',
            'published' => null,
            'title' => 'second media',
            'description' => 'another media description goes here',
            'body' => null,
            'extra' => null,
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => null,
            'publish_end' => null,
            'custom_props' => ['media_property' => false],
        ],
        // 15 (ghost object)
        [
            'object_type_id' => 2,
            'status' => 'draft',
            'uname' => '__deleted-15',
            'locked' => 1,
            'deleted' => 1,
            'created' => '2018-07-13 07:09:23',
            'modified' => '2018-07-15 08:30:00',
            'published' => null,
            'title' => null,
            'description' => null,
            'body' => null,
            'extra' => null,
            'lang' => null,
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => null,
            'publish_end' => null,
        ],
        // 16
        [
            'object_type_id' => 9,
            'status' => 'on',
            'uname' => 'media-svg',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2024-03-25 16:11:18',
            'modified' => '2024-03-25 16:11:18',
            'published' => null,
            'title' => 'svg media',
            'description' => 'an svg image',
            'body' => null,
            'extra' => null,
            'lang' => 'en',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => null,
            'publish_end' => null,
            'custom_props' => ['media_property' => false],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();

        // remove `objects_createdby_fk` and `objects_modifiedby_fk` constraints
        // to avoid PostgreSQL error inserting first user that references itself.
        // CakePHP inserting fixture disables constraints but
        // when the constraints are enabled again PostgreSQL give an SQL error.
        $connection = ConnectionManager::get($this->connection());
        if (!$connection->getDriver() instanceof Postgres) {
            return;
        }

        $constraints = $this->_schema->constraints();
        $removeConstraints = ['objects_createdby_fk', 'objects_modifiedby_fk'];
        if (empty(array_intersect($constraints, $removeConstraints))) {
            return;
        }

        $restoreConstraints = [];
        foreach ($this->_schema->constraints() as $name) {
            if (in_array($name, $removeConstraints)) {
                continue;
            }

            $restoreConstraints[$name] = $this->_schema->getConstraint($name);
            $this->_schema->dropConstraint($name);
        }

        $dropConstraintSql = $this->_schema->dropConstraintSql($connection);
        foreach ($dropConstraintSql as $sql) {
            $connection->execute($sql);
        }

        foreach ($restoreConstraints as $name => $attrs) {
            $this->_schema->addConstraint($name, $attrs);
        }
    }
}

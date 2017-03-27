<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * ObjectsFixture
 *
 */
class ObjectsFixture extends TestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'object_type_id' => 3,
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
            'lang' => 'eng',
            'created_by' => 1,
            'modified_by' => 1,
        ],
        [
            'object_type_id' => 1,
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
            'extra' => '{"abstract": "abstract here", "list": ["one", "two", "three"]}',
            'lang' => 'eng',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => '2016-05-13 07:09:23',
            'publish_end' => '2016-05-13 07:09:23'
        ],
        [
            'object_type_id' => 1,
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
            'lang' => 'eng',
            'created_by' => 1,
            'modified_by' => 2,
            'publish_start' => null,
            'publish_end' => null
        ],
        [
            'object_type_id' => 2,
            'status' => 'on',
            'uname' => 'gustavo-supporto',
            'locked' => 0,
            'deleted' => 0,
            'created' => '2016-05-13 07:09:23',
            'modified' => '2016-05-13 07:09:23',
            'published' => null,
            'title' => 'Gustavo Supporto profile',
            'description' => 'Some description about Gustavo',
            'lang' => 'eng',
            'created_by' => 1,
            'modified_by' => 1
        ],
        [
            'object_type_id' => 3,
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
            'lang' => 'eng',
            'created_by' => 1,
            'modified_by' => 1,
        ],
        [
            'object_type_id' => 1,
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
            'extra' => '{"abstract": "what?"}',
            'lang' => 'eng',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => '2016-10-13 07:09:23',
            'publish_end' => '2016-10-13 07:09:23'
        ],
        [
            'object_type_id' => 1,
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
            'extra' => '{"abstract": "what?"}',
            'lang' => 'eng',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => '2016-10-13 07:09:23',
            'publish_end' => '2016-10-13 07:09:23'
        ],
        [
            'object_type_id' => 5,
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
            'lang' => 'eng',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => null,
            'publish_end' => null
        ],
        [
            'object_type_id' => 6,
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
            'lang' => 'eng',
            'created_by' => 1,
            'modified_by' => 1,
            'publish_start' => null,
            'publish_end' => null
        ],
    ];

    /**
     * Before Build Schema callback
     *
     * Change `status` type to 'string' to avoid errors
     *
     * @return void
     */
    public function beforeBuildSchema()
    {
        $this->fields['status']['type'] = 'string';

        unset($this->fields['_constraints']['objects_modifiedby_fk']);
        unset($this->fields['_constraints']['objects_createdby_fk']);
    }
}

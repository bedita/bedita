<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;
use Cake\Event\Event;

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
            'object_type_id' => 1,
            'status' => 'on',
            'uname' => 'title-one',
            'locked' => 1,
            'created' => '2016-05-13 07:09:23',
            'modified' => '2016-05-13 07:09:23',
            'published' => '2016-05-13 07:09:23',
            'title' => 'title one',
            'description' => 'description here',
            'body' => 'body here',
            'extra' => '{"abstract": "abstract here", "list": "[\"one\", \"two\", \"three\"]"}',
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
    ];

    /**
     * Before Build Schema callback
     *
     * Change `status` type to 'string' to avoid errors
     *
     * @return void
     */
    public function beforeBuildSchema(Event $event)
    {
        $this->fields['status']['type'] = 'string';
    }
}

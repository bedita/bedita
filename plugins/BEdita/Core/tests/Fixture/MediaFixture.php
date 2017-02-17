<?php
namespace BEdita\Core\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MediaFixture
 *
 */
class MediaFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'uri' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'media uri: relative path on local filesystem or remote URL', 'precision' => null],
        'name' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'file name', 'precision' => null],
        'mime_type' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'resource mime type', 'precision' => null, 'fixed' => null],
        'file_size' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => true, 'default' => null, 'comment' => 'file size in bytes (if local)', 'precision' => null, 'autoIncrement' => null],
        'hash_file' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'md5 hash of local file', 'precision' => null, 'fixed' => null],
        'original_name' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'original name for uploaded file', 'precision' => null],
        'width' => ['type' => 'integer', 'length' => 6, 'unsigned' => true, 'null' => true, 'default' => null, 'comment' => '(image) width', 'precision' => null, 'autoIncrement' => null],
        'height' => ['type' => 'integer', 'length' => 6, 'unsigned' => true, 'null' => true, 'default' => null, 'comment' => '(image) height', 'precision' => null, 'autoIncrement' => null],
        'provider' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'external provider/service name', 'precision' => null, 'fixed' => null],
        'media_uid' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'uid, used for remote videos', 'precision' => null, 'fixed' => null],
        'thumbnail' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'remote media thumbnail URL', 'precision' => null, 'fixed' => null],
        '_indexes' => [
            'media_hashfile_idx' => ['type' => 'index', 'columns' => ['hash_file'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'media_id_fk' => ['type' => 'foreign', 'columns' => ['id'], 'references' => ['objects', 'id'], 'update' => 'noAction', 'delete' => 'cascade', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'uri' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'name' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'mime_type' => 'Lorem ipsum dolor sit amet',
            'file_size' => 1,
            'hash_file' => 'Lorem ipsum dolor sit amet',
            'original_name' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'width' => 1,
            'height' => 1,
            'provider' => 'Lorem ipsum dolor sit amet',
            'media_uid' => 'Lorem ipsum dolor sit amet',
            'thumbnail' => 'Lorem ipsum dolor sit amet'
        ],
    ];
}

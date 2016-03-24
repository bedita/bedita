<?php
namespace BEdita\Auth\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AuthProvidersFixture
 */
class AuthProvidersFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 5, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => 'external provider name: facebook, google, github...', 'precision' => null, 'fixed' => null],
        'url' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => 'external provider url', 'precision' => null, 'fixed' => null],
        'params' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'external provider parameters', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'name' => ['type' => 'unique', 'columns' => ['name'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci',
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
            'name' => 'example',
            'url' => 'https://example.com/oauth2',
            'params' => null,
        ],
        [
            'id' => 2,
            'name' => 'example_2',
            'url' => 'https://example.org/oauth2',
            'params' => '{"param":"value"}',
        ],
    ];
}

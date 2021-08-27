<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * LinksFixture
 *
 */
class LinksFixture extends TestFixture
{
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        // Beware: this is a `fake` fixture, object with id 15 is acutally a `document`!
        [
            'id' => 15,
            'http_status' => '200 OK',
            'url' => 'https://www.gustavo.com',
            'last_update' => '2020-04-29 08:05:15',
        ],
    ];
}

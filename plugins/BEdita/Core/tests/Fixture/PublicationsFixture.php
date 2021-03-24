<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * PublicationsFixture
 */
class PublicationsFixture extends TestFixture
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
            'public_name' => 'Gustavo blog',
            'public_url' => 'https://www.gustavo.com',
            'staging_url' => 'https://staging.gustavo.com',
            'stats_code' => 'abcdef',
        ],
    ];
}

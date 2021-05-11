<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * MediaFixture
 *
 */
class MediaFixture extends TestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 10,
            'uname' => 'media-10',
            'name' => 'My media name',
            'width' => null,
            'height' => null,
            'duration' => null,
            'provider' => null,
            'provider_uid' => null,
            'provider_url' => 'https://via.placeholder.com/150',
            'provider_thumbnail' => 'https://via.placeholder.com/150',
        ],
        [
            'id' => 14,
            'uname' => 'media-14',
            'name' => 'My other media name',
            'width' => null,
            'height' => null,
            'duration' => null,
            'provider' => null,
            'provider_uid' => null,
            'provider_url' => null,
            'provider_thumbnail' => null,
        ],
    ];
}

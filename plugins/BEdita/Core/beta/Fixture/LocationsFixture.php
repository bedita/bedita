<?php
namespace BEdita\Core\Test\Fixture;

use BEdita\Core\TestSuite\Fixture\TestFixture;

/**
 * LocationsFixture
 *
 */
class LocationsFixture extends TestFixture
{

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 8,
            'coords' => 'POINT(11.3464055 44.4944183)',
            'address' => 'Piazza di Porta Ravegnana',
            'locality' => 'Bologna',
            'postal_code' => '40126',
            'country_name' => 'Italy',
            'region' => 'Emilia-romagna'
        ],
    ];
}

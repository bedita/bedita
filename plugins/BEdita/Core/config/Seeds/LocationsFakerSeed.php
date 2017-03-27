<?php
use BEdita\Core\Utility\ObjectsHandler;
use Faker\Factory;
use Migrations\AbstractSeed;

class LocationsFakerSeed extends AbstractSeed
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $faker = Factory::create();
        $data = [
            'title' => $faker->name,
            'coords' => 'POINT (' . $faker->latitude . ', ' . $faker->longitude . ')',
            'address' => $faker->streetAddress,
            'locality' => $faker->city,
            'postal_code' => $faker->postcode,
            'country' => $faker->country,
            'region' => $faker->stateAbbr,
        ];
        ObjectsHandler::save('locations', $data);
    }
}

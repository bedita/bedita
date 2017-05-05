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
        $faker = Factory::create(array_rand(['de_DE', 'es_ES', 'fr_FR', 'it_IT', 'en_GB']));
        $long = $faker->longitude;
        $long = (abs($long) > 90.0) ? abs($long) - 90 : $long;
        $data = [
            'title' => $faker->name,
            'coords' => 'POINT (' . $faker->latitude . ' ' . $long . ')',
            'address' => $faker->streetAddress,
            'locality' => $faker->city,
            'postal_code' => $faker->postcode,
            'country' => $faker->country,
            'region' => $faker->stateAbbr,
        ];
        ObjectsHandler::save('locations', $data);
    }
}

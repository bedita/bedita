<?php
use BEdita\Core\Utility\ObjectsHandler;
use Faker\Factory;
use Migrations\AbstractSeed;

class ProfilesFakerSeed extends AbstractSeed
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $faker = Factory::create();
        $gender = $faker->randomElement(['male', 'female']);
        $name = $faker->firstName($gender);
        $surname = $faker->lastName;
        $data = [
            'name' => $name,
            'surname' => $surname,
            'title' => "$name $surname",
            'email' => $faker->email,
            'person_title' => $faker->title($gender),
            'gender' => $gender,
            'birthdate' => $faker->date('1999-12-31'),
            'street_address' => $faker->streetAddress,
            'city' => $faker->city,
            'zipcode' => $faker->postcode,
            'country' => $faker->country,
            'state_name' => $faker->state,
            'phone' => $faker->phoneNumber,
            'website' => 'www.' . $faker->domainName,
        ];
        ObjectsHandler::save('profiles', $data);
    }
}

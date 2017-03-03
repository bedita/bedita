<?php
use BEdita\Core\Utility\ObjectsHandler;
use Faker\Factory;
use Migrations\AbstractSeed;

class UsersFakerSeed extends AbstractSeed
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $faker = Factory::create();
        $data = [
            'username' => $faker->unique()->userName,
            'password' => $faker->password,
            'email' => $faker->email,
        ];
        ObjectsHandler::create('users', $data);
    }
}

<?php
use BEdita\Core\Utility\ObjectsHandler;
use Faker\Factory;
use Migrations\AbstractSeed;

class NewsFakerSeed extends AbstractSeed
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $faker = Factory::create();
        $data = [
            'title' => $faker->unique()->sentence(3),
            'status' => $faker->randomElement(['on', 'draft']),
            'description' => $faker->optional()->paragraph,
            'body' => $faker->optional()->text,
            'description' => $faker->paragraph,
        ];
        ObjectsHandler::create('news', $data);
    }
}

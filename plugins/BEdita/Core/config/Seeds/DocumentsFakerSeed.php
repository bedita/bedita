<?php
use BEdita\Core\Utility\ObjectsHandler;
use Cake\I18n\Time;
use Faker\Factory;
use Migrations\AbstractSeed;

class DocumentsFakerSeed extends AbstractSeed
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $faker = Factory::create();
        $data = [
            'title' => $faker->unique()->sentence(4),
            'status' => $faker->randomElement(['on', 'draft']),
            'description' => $faker->optional()->paragraph,
            'body' => $faker->optional()->text,
            'description' => $faker->paragraph,
            'publish_start' => Time::now(),
        ];
        ObjectsHandler::save('documents', $data);
    }
}

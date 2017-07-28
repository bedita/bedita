<?php
use BEdita\Core\Utility\ObjectsHandler;
use Cake\I18n\Time;
use Faker\Factory;
use Migrations\AbstractSeed;

class EventsFakerSeed extends AbstractSeed
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $faker = Factory::create();
        $data = [
            'title' => 'Event ' . $faker->unique()->sentence(4),
            'status' => $faker->randomElement(['on', 'draft']),
            'description' => $faker->optional()->paragraph,
            'body' => $faker->optional()->text,
            'date_ranges' => [
                [
                    'start_date' => new Time($faker->dateTimeBetween('-30 days', '+30 days')),
                    'end_date' => null
                ],
                [
                    'start_date' => new Time($faker->dateTimeBetween('-7 days', '-2 days')),
                    'end_date' => new Time($faker->dateTimeBetween('+2 days', '+5 days')),
                ],
            ],
        ];
        ObjectsHandler::save('events', $data);
    }
}

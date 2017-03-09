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
            'description' => $faker->paragraph,
        ];
        $entity = ObjectsHandler::create('events', $data);

        $data = [
            [
                'object_id' => $entity->id,
                'start_date' => $faker->dateTimeBetween($startDate = '-30 days', $endDate = '+30 days')
                                    ->format('Y-m-d H:i:s'),
                'end_date' => null
            ],
            [
                'object_id' => $entity->id,
                'start_date' => $faker->dateTimeBetween($startDate = '-7 days', $endDate = '-2 days')
                                    ->format('Y-m-d H:i:s'),
                'end_date' => $faker->dateTimeBetween($startDate = '+2 days', $endDate = '+5 days')
                                    ->format('Y-m-d H:i:s'),
            ]
        ];
        $table = $this->table('date_ranges');
        foreach ($data as $row) {
            $table->reset();
            $table->insert($row)->saveData();
        }
    }
}

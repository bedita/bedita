<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Shell;

use Cake\Console\Shell;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Utility\Text;

/**
 * Shell commands to seed new fake data
 *
 * @since 4.0.0
 */
class DataSeedShell extends Shell
{

    /**
     * Faker instance
     *
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * {@inheritDoc}
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('insert', [
            'help' => 'Insert new fake data.',
            'parser' => [
                'description' => [
                    'Use this command to generate new fake data in BE4',
                    'You have to specify table and number of new items to seed.',
                ],
                'options' => [
                    'table' => [
                        'help' => 'Specify item table',
                        'short' => 't',
                        'required' => true,
                        'default' => 'users',
                    ],
                    'number' => [
                        'help' => 'Specify item number',
                        'short' => 'n',
                        'required' => true,
                        'default' => 1,
                    ],
                    'fields' => [
                        'help' => 'Specifiy values for some fields using this format field1="val1",field2="val2"',
                        'short' => 'f',
                        'required' => false,
                    ],
                ],
            ],
        ]);

        return $parser;
    }

    /**
     * Initialize Faker instance.
     *
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        if (!class_exists('\Faker\Factory')) {
            $this->abort('Faker lib not found');
        }
        $this->faker = \Faker\Factory::create();
    }

    /**
     * Generate data for "users" table
     *
     * @return array
     */
    protected function usersData()
    {
        $tsLast = $this->faker->dateTimeThisYear()->getTimestamp();
        return [
            'username' => $this->faker->userName,
            'password' => $this->faker->password,
            'last_login' => Time::createFromTimestamp($tsLast),
        ];
    }

    /**
     * Generate data for "roles" table
     *
     * @return array
     */
    protected function rolesData()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'backend_auth' => $this->faker->numberBetween(0, 1),
            'created' => Time::now(),
            'modified' => Time::now(),
        ];
    }

    /**
     * Generate data for "objects" table
     *
     * @return array
     */
    protected function objectsData()
    {
        $title = $this->faker->sentence(4);

        return [
            'title' => $title,
            'uname' => Text::slug($title),
            'status' => $this->faker->randomElement(['on', 'off', 'draft', 'deleted']),
            'description' => $this->faker->paragraph,
            'body' => $this->faker->text,
            'lang' => 'eng',
            'created_by' => 1,
            'modified_by' => 1,
            'created' => Time::now(),
            'modified' => Time::now(),
        ];
    }

    /**
     * Parse fields.
     *
     * @param string $fields Field string to be parsed.
     * @return array Parsed fields.
     * @throws \InvalidArgumentException Throws an exception if a field couldn't be parsed.
     */
    protected function parseFields($fields)
    {
        if (empty($fields)) {
            return [];
        }

        $parsed = [];
        $fields = explode(',', $fields);
        foreach ($fields as $field) {
            $field = explode('=', $field);
            if (count($field) != 2) {
                throw new \InvalidArgumentException(sprintf('Could not parse field "%s"', implode('=', $field)));
            }

            list($key, $value) = $field;
            $parsed[$key] = $value;
        }

        return $parsed;
    }

    /**
     * Insert new items using Faker
     *
     * @return void
     */
    public function insert()
    {
        $tableName = Inflector::camelize($this->params['table']);
        $method = Inflector::variable($tableName) . 'Data';
        if (!method_exists($this, $method)) {
            $this->abort('Table "' . $tableName . '" is not yet supported');
        }
        $table = TableRegistry::get('BEdita/Core.' . $tableName);
        $count = max(1, intval($this->params['number']));

        $fields = [];
        if (!empty($this->params['fields'])) {
            try {
                $fields = $this->parseFields($this->params['fields']);
            } catch (\InvalidArgumentException $e) {
                $this->abort(sprintf('Parsing error: ' . $e->getMessage()));
            }
        }

        $this->out('<info>Generating entities...</info> ', 0);
        $entities = [];
        for ($i = 0; $i < $count; $i++) {
            $data = $fields + call_user_func([$this, $method]);
            $entities[] = $table->newEntity($data);
        }
        $this->out('<success>DONE</success>');

        $this->out('<info>Persisting entities...</info> ', 0);
        try {
            $table->connection()->transactional(function () use ($table, $entities) {
                foreach ($entities as $entity) {
                    $table->save($entity, ['atomic' => false]);
                }
            });
        } catch (\Exception $e) {
            $this->out('<error>ERROR</error>');
            $this->abort(sprintf('Error while saving entities: %s', $e->getMessage()));
        }
        $this->out('<success>DONE</success>');
    }
}

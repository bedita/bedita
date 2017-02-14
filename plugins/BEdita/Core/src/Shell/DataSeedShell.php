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

use Cake\Console\Exception\StopException;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Faker\Factory;

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

        Configure::load('core', 'database');

        if (!class_exists('\Faker\Factory')) {
            $this->abort('Faker lib not found');
        }
        $this->faker = Factory::create();
    }

    /**
     * Generate data for "roles" table
     *
     * @param array $fields Fields to be set.
     * @param int $count Amount of entities to be generated.
     * @return array
     */
    protected function rolesData(array $fields, $count)
    {
        $entities = [];
        for ($i = 0; $i < $count; $i++) {
            $data = $fields;
            $data += [
                'name' => $this->faker->unique()->word,
                'description' => $this->faker->optional()->sentence,
                'unchangeable' => $this->faker->boolean(10),
                'created' => Time::now(),
                'modified' => Time::now(),
            ];

            $entities[] = $data;
        }

        return $entities;
    }

    /**
     * Generate data for "objects" table
     *
     * @param array $fields Fields to be set.
     * @param int $count Amount of entities to be generated.
     * @return array
     */
    protected function objectsData(array $fields, $count)
    {
        $entities = [];
        for ($i = 0; $i < $count; $i++) {
            $title = $this->faker->unique()->sentence(4);

            $data = $fields;
            $data += [
                'title' => $title,
                'uname' => Text::slug($title),
                'status' => $this->faker->randomElement(['on', 'off', 'draft', 'deleted']),
                'description' => $this->faker->optional()->paragraph,
                'body' => $this->faker->optional()->text,
                'lang' => 'eng',
                'created_by' => 1,
                'modified_by' => 1,
                'created' => Time::createFromTimestamp(
                    $this->faker->dateTimeThisYear->getTimestamp()
                ),
                'modified' => Time::now(),
            ];

            $entities[] = $data;
        }

        return $entities;
    }

    /**
     * Generate data for "users" table
     *
     * @param array $fields Fields to be set.
     * @param int $count Amount of entities to be generated.
     * @return array
     */
    protected function usersData(array $fields, $count)
    {
        $entities = $this->objectsData($fields, $count);

        foreach ($entities as &$data) {
            $data += [
                'type' => 'users',
                'username' => $this->faker->unique()->userName,
                'password_hash' => $this->faker->password,
                'last_login' => Time::createFromTimestamp(
                    $this->faker->dateTimeThisYear->getTimestamp()
                ),
            ];
        }
        unset($data);

        return $entities;
    }

    /**
     * Generate data for "object_types" table
     *
     * @return array
     */
    protected function objectTypesData()
    {
        return [
            'name' => $this->faker->sentence(1),
            'pluralized' => $this->faker->sentence(1),
            'description' => $this->faker->paragraph,
            'plugin' => $this->faker->sentence(1),
            'model' => $this->faker->sentence(1)
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
        $table = TableRegistry::get($tableName);
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
        $data = call_user_func([$this, $method], $fields, $count);
        foreach ($data as $entity) {
            $entity = $table->newEntity($entity, ['accessibleFields' => ['*' => true]]);
            if ($entity->errors()) {
                $this->out('<error>ERROR</error>');
                $this->abort(sprintf('Entity validation failed: %s', print_r($entity->errors(), true)));
            }
            $entities[] = $entity;
        }
        $this->out('<success>DONE</success>');

        $this->out('<info>Persisting entities...</info> ', 0);
        try {
            $table->getConnection()->transactional(function () use ($table, $entities) {
                foreach ($entities as $entity) {
                    if (!$table->save($entity, ['atomic' => false])) {
                        throw new StopException(sprintf('Application rules failed: %s', print_r($entity->errors(), true)));
                    }
                }
            });
        } catch (\Exception $e) {
            $this->out('<error>ERROR</error>');
            $this->abort(sprintf('Error while saving entities: %s', $e->getMessage()));
        }
        $this->out('<success>DONE</success>');
    }
}

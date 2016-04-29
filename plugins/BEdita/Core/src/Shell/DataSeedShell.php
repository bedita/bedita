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

use BEdita\Core\Utils\DbUtils;
use BEdita\Core\Utils\StringUtils;
use Cake\Console\Shell;

/**
 * Shell commands to seed new fake data
 *
 * @since 4.0.0
 */
class DataSeedShell extends Shell
{

    /**
     * Faker intance
     *
     * @var
     */
    private $faker = null;

    /**
     * User defined field values
     *
     * @array
     */
    private $fields = [];


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
                        'help' => 'Specifiy item table',
                        'short' => 't',
                        'required' => true,
                        'default' => 'users',
                    ],
                    'number' => [
                        'help' => 'Specifiy item number',
                        'short' => 'n',
                        'required' => true,
                        'default' => 1,
                    ],
                    'fields' => [
                        'help' => 'Specifiy values for some fields using thus forma field1="val1",field2="val2"',
                        'short' => 'f',
                        'required' => false,
                    ],
                ],
            ],
        ]);

         return $parser;
    }

    /**
     * Init Faker instance, check class existence
     *
     * @return void
     */
    protected function initFaker()
    {
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
        return [
            'username' => $this->faker->userName,
            'password' => md5($this->faker->password),
            'last_login' => $this->faker->dateTimeThisDecade()->format('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
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
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Generate data for "objects" table
     *
     * @return array
     */
    protected function objectsData()
    {
        if (empty($this->fields['object_type_id'])) {
            $this->abort('Unable to set object_type_id: please use --fieldsd option');
        }
        $userId = 1;
        if (!empty($this->fields['user_id'])) {
            $userId = $this->fields['user_id'];
        }
        $title = $this->faker->sentence(4);
        return [
            'object_type_id' => $this->fields['object_type_id'],
            'title' => $title,
            'uname' => StringUtils::friendlyUrl($title),
            'status' => $this->faker->randomElement(['on', 'off', 'draft', 'deleted']),
            'description' => $this->faker->paragraph,
            'body' => $this->faker->text,
            'lang' => 'eng',
            'created_by' => $userId,
            'modified_by' => $userId,
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Generate SQL insert statement from $data
     *
     * @param array $data Input data from Faker
     * @param string $table Table name in SQL
     * @return string
     */
    protected function sqlInsert($data, $table)
    {
        $sql = 'INSERT INTO ' . $table . ' (';
        $dim = count($data);
        $count = 0;
        foreach ($data as $k => $v) {
            $sql .= $k;
            $count++;
            if ($count < $dim) {
                $sql .= ', ';
            }
        }
        $sql .= ') VALUES (';
        $count = 0;
        foreach ($data as $k => $v) {
            $sql .= is_string($v) ? "'" . addslashes($v) . "'" : $v;
            $count++;
            if ($count < $dim) {
                $sql .= ', ';
            }
        }
        $sql .= ')';
        return $sql;
    }

    /**
     * Insert new items using Faker
     *
     * @return void
     */
    public function insert()
    {
        $this->initFaker();
        $table = $this->params['table'];
        $method = $table . 'Data';
        if (!method_exists($this, $method)) {
            $this->abort('Table "' . $table . '" not supported');
        }
        $num = intval($this->params['number']);

        if (!empty($this->params['fields'])) {
            $f = explode(',', $this->params['fields']);
            foreach ($f as $value) {
                $fData = explode('=', $value);
                if (count($fData) != 2) {
                    $this->abort('Error parsing input field: ' . $value);
                }
                $this->fields[$fData[0]] = $fData[1];
            }
        }

        $sql = '';
        $this->info('Generating SQL queries...');
        for ($i = 0; $i < $num; $i++) {
            $data = $this->{$method}();
            $query = $this->sqlInsert($data, $table);
            $this->out($query);
            $sql .= $query . ";\n";
        }

        $this->info('Executing SQL queries...');
        $result = DbUtils::executeTransaction($sql);
        if (!$result['success']) {
            $this->abort('Error executing SQL: ' . $result['error']);
        }
        $this->info('...done');
    }
}

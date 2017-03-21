<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Validation;

use BEdita\Core\Model\Validation\UsersValidator;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @coversNothing
 */
class UsersValidatorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'empty' => [
                [
                    'username._required',
                ],
                [],
            ],
            'missing fields on update' => [
                [
                    'id._required',
                ],
                [],
                false,
            ],
            'empty fields' => [
                [
                    'status._empty',
                    'uname._empty',
                ],
                [
                    'status' => '',
                    'uname' => null,
                    'username' => 0,
                ],
            ],
            'invalid types' => [
                [
                    'id.naturalNumber',
                    'status.inList',
                    'uname.ascii',
                    'locked.boolean',
                    'deleted.boolean',
                    'published.dateTime',
                    'publish_start.dateTime',
                    'publish_end.dateTime',
                    'email.email',
                    'birthdate.date',
                    'deathdate.date',
                    'company.boolean',
                    'website.url',
                    'blocked.boolean',
                ],
                [
                    'id' => -pi(),
                    'status' => 'neither on, nor off... maybe draft',
                    'uname' => 'àèìòù',
                    'locked' => 'yes',
                    'deleted' => 'maybe',
                    'published' => 'tomorrow',
                    'publish_start' => 'someday',
                    'publish_end' => 'somewhen',
                    'email' => 'http://not.an.email.example.org/',
                    'birthdate' => 'a gloomy day',
                    'deathdate' => 'a glorious day',
                    'company' => 'good company',
                    'website' => 'not.an.url@example.org',
                    'username' => 'fquffio',
                    'blocked' => 'aye!',
                ],
            ],
            'not unique' => [
                [
                    'uname.unique',
                    'email.unique',
                    'username.unique',
                ],
                [
                    'uname' => 'title-one',
                    'email' => 'first.user@example.com',
                    'username' => 'first user',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param array $expected Expected validation errors.
     * @param array $data Data being validated.
     * @param bool $newRecord Is this a new record?
     * @return void
     *
     * @dataProvider validationProvider()
     */
    public function testValidation(array $expected, array $data, $newRecord = true)
    {
        $validator = new UsersValidator();

        $errors = $validator->errors($data, $newRecord);
        $errors = Hash::flatten($errors);

        static::assertEquals($expected, array_keys($errors));
    }
}

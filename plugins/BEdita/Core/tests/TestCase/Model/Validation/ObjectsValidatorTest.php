<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Validation\ObjectsValidator;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\Core\Model\Validation\ObjectsValidator
 */
class ObjectsValidatorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
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
                [],
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
                ],
                [
                    'status' => '',
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
                ],
            ],
            'uname numeric not valid' => [
                [
                    'uname.notNumeric',
                ],
                [
                    'uname' => '123',
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
     * @dataProvider validationProvider()
     * @coversNothing
     */
    public function testValidation(array $expected, array $data, $newRecord = true): void
    {
        $validator = new ObjectsValidator();

        $errors = $validator->validate($data, $newRecord);
        $errors = Hash::flatten($errors);

        static::assertEquals($expected, array_keys($errors));
    }

    /**
     * Test not numeric validation.
     *
     * @return void
     * @covers ::notNumeric()
     */
    public function testNotNumeric(): void
    {
        $validator = new ObjectsValidator();
        $this->assertNotEmpty($validator->validate(['uname' => '22']));
        $this->assertEmpty($validator->validate(['uname' => 'a']));

        $validator->notNumeric('fake_field', 'The provided value must be NOT numeric', 'create');
        $errors = $validator->validate(['fake_field' => '22']);
        $this->assertNotEmpty($errors);
        $this->assertEquals(
            [
                'fake_field' => [
                    'notNumeric' => 'The provided value must be NOT numeric',
                ],
            ],
            $errors
        );
        $errors = $validator->validate(['id' => 1000, 'fake_field' => '22'], false);
        $this->assertEmpty($errors);
    }
}

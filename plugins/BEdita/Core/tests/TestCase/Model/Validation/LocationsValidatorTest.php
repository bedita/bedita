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

use BEdita\Core\Model\Validation\LocationsValidator;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @coversNothing
 */
class LocationsValidatorTest extends TestCase
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
        'plugin.BEdita/Core.locations',
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
            'not unique' => [
                [
                    'uname.unique',
                ],
                [
                    'uname' => 'title-one',
                ],
            ],
            'invalid coordinates' => [
                [
                    'coords.valid',
                ],
                [
                    'coords' => '1212121',
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
        $validator = new LocationsValidator();

        $errors = $validator->errors($data, $newRecord);
        $errors = Hash::flatten($errors);

        static::assertEquals($expected, array_keys($errors));
    }

    /**
     * Data provider for `testCheckWkt` test case.
     *
     * @return array
     */
    public function checkWktProvider()
    {
        return [
            'not a string' => [
                'invalid Well-Known Text',
                new \stdClass(),
            ],
            'random string' => [
                'invalid Well-Known Text',
                'I am not a valid WKT',
            ],
            'invalid longitude' => [
                'invalid longitude',
                'POINT(-180 50.44)',
            ],
            'invalid latitude' => [
                'invalid latitude',
                'POINT(180 ..)',
            ],
            'valid' => [
                true,
                'POINT(180 -90)',
            ],
        ];
    }

    /**
     * Test WKT validator.
     *
     * @param string|true $expected Expected result.
     * @param mixed $value Value being validated.
     * @return void
     *
     * @dataProvider checkWktProvider()
     * @covers \BEdita\Core\Model\Validation\LocationsValidator::checkWkt()
     */
    public function testCheckWkt($expected, $value)
    {
        $result = LocationsValidator::checkWkt($value);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testCheckCoordinates` test case.
     *
     * @return array
     */
    public function checkCoordinatesProvider()
    {
        return [
            'not an array' => [
                'coordinates must be a pair of values',
                new \stdClass(),
            ],
            'wrong length' => [
                'coordinates must be a pair of values',
                [1, 2, 3, 4, 5],
            ],
            'not a zero-based numerically-indexed array' => [
                'coordinates must be a pair of values',
                // This format would be harder to validate, since many variations are possible. K.I.S.S.!
                ['lat' => 1, 'lng' => 2],
            ],
            'invalid longitude' => [
                'invalid longitude',
                ['50.44', '180.0000000001'],
            ],
            'invalid latitude' => [
                'invalid latitude',
                ['-179.999999999', '91'],
            ],
            'valid' => [
                true,
                [0, -90],
            ],
        ];
    }

    /**
     * Test coordinates validator.
     *
     * @param string|true $expected Expected result.
     * @param mixed $value Value being validated.
     * @return void
     *
     * @dataProvider checkCoordinatesProvider()
     * @covers \BEdita\Core\Model\Validation\LocationsValidator::checkCoordinates()
     */
    public function testCheckCoordinates($expected, $value)
    {
        $result = LocationsValidator::checkCoordinates($value);

        static::assertSame($expected, $result);
    }
}

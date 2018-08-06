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

use BEdita\Core\Model\Validation\Validation;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\TestSuite\TestCase;
use DateTime;

/**
 * {@see \BEdita\Core\Model\Validation\Validation} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Validation\Validation
 */
class ValidationTest extends TestCase
{

    /**
     * Data provider for `testReserved` test case.
     *
     * @return array
     */
    public function reservedProvider()
    {
        return [
            'simple' => [
                true,
                'gustavo',
            ],
            'fail' => [
                false,
                'role',
            ],
        ];
    }

    /**
     * Test reserved word validation.
     *
     * @param boolean $expected Expected result.
     * @param string $value Test value.
     * @return void
     *
     * @dataProvider reservedProvider()
     * @covers ::notReserved()
     */
    public function testReserved($expected, $value)
    {
        $result = Validation::notReserved($value);

        static::assertSame($expected, $result);
    }

    /**
     * Test clear and load runtime cache.
     *
     * @return void
     *
     * @covers ::clear()
     * @covers ::reservedWords()
     */
    public function testClear()
    {
        Validation::clear();

        static::assertAttributeSame(null, 'reserved', Validation::class);

        $file = Plugin::configPath('BEdita/Core') . 'reserved.php';
        $expected = include $file;

        $allowed = Validation::notReserved('gustavo');

        static::assertTrue($allowed);
        static::assertAttributeSame($expected, 'reserved', Validation::class);
    }

    /**
     * Data provider for `testUrl` test case.
     *
     * @return array
     */
    public function urlProvider()
    {
        return [
            'https://example.com' => [
                true,
                'https://example.com',
            ],
            'myapp://example.com' => [
                true,
                'myapp://example.com',
            ],
            'https:example.com' => [
                false,
                'https:example.com',
            ],
            'https://examplecom' => [
                false,
                'https://examplecom',
            ],
        ];
    }

    /**
     * Test URL validation.
     *
     * @param bool $expected Expected result.
     * @param string $url URL being validated.
     * @return void
     *
     * @dataProvider urlProvider()
     * @covers ::url()
     */
    public function testUrl($expected, $url)
    {
        $result = Validation::url($url);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testJsonSchema` test case.
     *
     * @return array
     */
    public function jsonSchemaProvider()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                ],
                'age' => [
                    'type' => 'integer',
                    'minimum' => 0,
                ],
            ],
            'additionalProperties' => false,
            'required' => ['name'],
        ];

        return [
            'meta schema' => [
                true,
                $schema,
                'http://json-schema.org/draft-06/schema#',
            ],
            'valid' => [
                true,
                [
                    'name' => 'Gustavo Supporto',
                    'age' => 42,
                ],
                $schema,
            ],
            'missing' => [
                true,
                [
                    'name' => 'Gustavo Supporto',
                    'age' => 42,
                    'whatever' => true,
                ],
                null,
            ],
            'unknown property' => [
                'The object must not contain additional properties',
                [
                    'name' => 'Gustavo Supporto',
                    'age' => 42,
                    'wtf' => 'this should not be present',
                ],
                $schema,
            ],
            'invalid value' => [
                'The number must be at least 0',
                [
                    'name' => 'Gustavo Supporto',
                    'age' => -42,
                ],
                $schema,
            ],
            'missing required property' => [
                'The object must contain the properties',
                [
                    'age' => 42,
                ],
                $schema,
            ],
            'wrong type' => [
                'The data must be a(n) string',
                [
                    'name' => true,
                ],
                $schema,
            ],
        ];
    }

    /**
     * Test JSON Schema validator.
     *
     * @param true|string $expected Expected result.
     * @param mixed $value Value being validated.
     * @param mixed $jsonSchema JSON Schema.
     * @return void
     *
     * @dataProvider jsonSchemaProvider()
     * @covers ::jsonSchema()
     */
    public function testJsonSchema($expected, $value, $jsonSchema)
    {
        $result = Validation::jsonSchema($value, $jsonSchema);

        if ($expected === true) {
            static::assertTrue($result);
        } else {
            static::assertContains($expected, $result);
        }
    }

    /**
     * Data provider for `testLanguageTag` test case.
     *
     * @return array
     */
    public function languageTagProvider()
    {
        return [
            'bad lang' => [
                'Invalid language tag "fi"',
                [
                    'languages' => [
                        'en' => 'English',
                    ],
                ],
                'fi',
            ],
            'ok' => [
                true,
                [
                    'languages' => [
                        'en' => 'English',
                        'sp' => 'Spanish',
                    ],
                ],
                'sp',
            ],
        ];
    }

    /**
     * Test lang tag validation.
     *
     * @param bool $expected Expected result.
     * @param array $config I18n config.
     * @param string $lang Lang tag being validated.
     * @return void
     *
     * @dataProvider languageTagProvider()
     * @covers ::languageTag()
     */
    public function testLanguageTag($expected, $config, $lang)
    {
        Configure::write('I18n', $config);

        $result = Validation::languageTag($lang);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testDateTime` test case.
     *
     * @return array
     */
    public function dateTimeProvider()
    {
        return [
            'bad date' => [
                'Invalid date or datetime "July, 1"',
                'July, 1',
            ],
            'ok date' => [
                true,
                '2018-08-02',
            ],
            'date time object' => [
                true,
                new DateTime('2018-08-02'),
            ],
            'ok date time' => [
                true,
                '2018-08-02 14:23',
            ],
            'ok iso date time' => [
                true,
                '2018-08-02T09:00:00+20:00',
            ],
            'ok timestamp' => [
                true,
                '1533117600',
            ],
        ];
    }

    /**
     * Test `dateTime` validation.
     *
     * @param bool|string $expected Expected result.
     * @param string|int $value Datetime validated.
     * @return void
     *
     * @dataProvider dateTimeProvider()
     * @covers ::dateTime()
     */
    public function testDateTime($expected, $value)
    {
        $result = Validation::dateTime($value);

        static::assertSame($expected, $result);
    }
}

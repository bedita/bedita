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

use BEdita\Core\Model\Validation\MediaValidator;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @coversNothing
 */
class MediaValidatorTest extends TestCase
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
        'plugin.BEdita/Core.media',
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
                    'provider_uid._empty',
                ],
                [
                    'provider_uid' => '',
                    'provider' => 'example',
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
                    'provider.ascii',
                    'provider_url.url',
                    'provider_thumbnail.url',
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
                    'provider' => 'àèìòù',
                    'provider_url' => 'not a valid URL',
                    'provider_thumbnail' => 'gustavo.supporto@example.org',
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
        $validator = new MediaValidator();

        $errors = $validator->errors($data, $newRecord);
        $errors = Hash::flatten($errors);

        static::assertEquals($expected, array_keys($errors));
    }
}

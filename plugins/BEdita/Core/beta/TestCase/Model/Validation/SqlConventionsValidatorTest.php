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

use BEdita\Core\Model\Validation\SqlConventionsValidator;
use Cake\Database\Schema\TableSchema;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @covers \BEdita\Core\Model\Validation\SqlConventionsValidator
 */
class SqlConventionsValidatorTest extends TestCase
{

    /**
     * Data provider for `testValidation` provider.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'primary' => [
                [],
                'primary',
                [
                    'table' => 'my_table',
                    'type' => TableSchema::CONSTRAINT_PRIMARY,
                ],
            ],
            'invalid type' => [
                [
                    'symbol.string' => 'must be a string',
                ],
                ['not', 'a', 'string'],
            ],
            'not ASCII, not underscored' => [
                [
                    'symbol.ascii' => 'contains non-ASCII characters',
                    'symbol.underscored' => 'not underscored',
                ],
                'Questa stringa non è ASCII!',
            ],
            'reserved word, same as table' => [
                [
                    'symbol.reservedWord' => 'reserved word',
                    'symbol.differentFromTable' => 'same name as table',
                ],
                'table',
                [
                    'table' => 'table',
                ],
            ],
            'leading, trailing and double underscores' => [
                [
                    'symbol.noLeadingUnderscore' => 'starts with "_"',
                    'symbol.noTrailingUnderscore' => 'ends with "_"',
                    'symbol.noDoubleUnderscore' => 'contains "__"',
                ],
                '_under__scores_',
            ],
            'leading digit, wrong prefix and suffix' => [
                [
                    'symbol.noLeadingDigit' => 'starts with a digit',
                    'symbol.prefix' => 'should start with "mytable_"',
                    'symbol.suffix' => 'should end with "_fk"',
                ],
                '1mysymbol',
                [
                    'table' => 'my_table',
                    'type' => TableSchema::CONSTRAINT_FOREIGN,
                ],
            ],
            'wrong suffix' => [
                [
                    'symbol.suffix' => 'should end with "_uq"',
                ],
                'mytable_suffix',
                [
                    'table' => 'my_table',
                    'type' => TableSchema::CONSTRAINT_UNIQUE,
                ],
            ],
            'missing unique identifier' => [
                [
                    'symbol.uniqueIdentifier' => 'should have a unique identifier between prefix and suffix',
                ],
                'mytable_idx',
                [
                    'table' => 'my_table',
                    'type' => TableSchema::INDEX_INDEX,
                ],
            ],
            'duplicate column' => [
                [
                    'symbol.globalName' => 'already defined in "my_other_table"',
                ],
                'my_column_name',
                [
                    'table' => 'my_table',
                    'allColumns' => [
                        'my_column_name' => 'my_other_table',
                    ],
                ],
            ],
            'allowed duplicates' => [
                [],
                'created',
                [
                    'table' => 'my_table',
                    'allColumns' => [
                        'created' => 'my_other_table',
                    ],
                ],
            ],
            'unique column' => [
                [],
                'my_unique_column',
                [
                    'table' => 'my_table',
                    'allColumns' => [
                        'my_column_name' => 'my_other_table',
                    ],
                ],
            ],
            'primary (custom name)' => [
                [],
                'mytable_primary_pk',
                [
                    'table' => 'my_table',
                    'type' => TableSchema::CONSTRAINT_PRIMARY,
                ],
            ],
        ];
    }

    /**
     * Test validation rules.
     *
     * @param array $expected Expected errors.
     * @param mixed $symbol Symbol being validated.
     * @param array $context Additional validation context.
     * @return void
     *
     * @dataProvider validationProvider()
     */
    public function testValidation(array $expected, $symbol, array $context = [])
    {
        $validator = new SqlConventionsValidator();
        foreach ($context as $key => $value) {
            $validator->setProvider($key, $value);
        }

        $errors = $validator->errors(compact('symbol'));
        $errors = Hash::flatten($errors);

        static::assertEquals($expected, $errors);
    }
}

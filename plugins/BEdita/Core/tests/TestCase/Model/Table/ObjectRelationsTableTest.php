<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\ObjectRelationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\Core\Model\Table\ObjectRelationsTable
 */
class ObjectRelationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ObjectRelationsTable
     */
    public $ObjectRelations;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->ObjectRelations = TableRegistry::getTableLocator()->get('ObjectRelations');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ObjectRelations);

        parent::tearDown();
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        $schema = (object)[
            'type' => 'object',
            'properties' => (object)[
                'name' => (object)[
                    'type' => 'string',
                ],
                'age' => (object)[
                    'type' => 'integer',
                    'minimum' => 0,
                ],
            ],
            'additionalProperties' => false,
            'required' => ['name'],
        ];

        return [
            'valid' => [
                [],
                [
                    'priority' => 0,
                    'inv_priority' => 17,
                ],
            ],
            'negative integers' => [
                [
                    'priority.nonNegativeInteger',
                    'inv_priority.nonNegativeInteger',
                ],
                [
                    'priority' => -8,
                    'inv_priority' => -1992,
                ],
            ],
            'required params' => [
                [
                    'params._required',
                ],
                [],
                $schema,
            ],
            'empty params' => [
                [
                    'params._empty',
                ],
                [
                    'params' => null,
                ],
                $schema,
            ],
            'invalid params' => [
                [
                    'params.valid',
                ],
                [
                    'params' => [
                        'age' => 42,
                    ],
                ],
                $schema,
            ],
            'required params for existing entities' => [
                [],
                [],
                $schema,
                false,
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     * @param object|null $jsonSchema JSON Schema.
     * @param bool $isNew Should entity be treated as new?
     * @return void
     * @dataProvider validationProvider()
     * @coversNothing
     */
    public function testValidation($expected, array $data, $jsonSchema = null, $isNew = true)
    {
        if ($jsonSchema) {
            $this->ObjectRelations->getValidator()->setProvider('jsonSchema', $jsonSchema);
        }

        $objectRelation = $this->ObjectRelations->newEmptyEntity();
        $objectRelation->setNew($isNew);
        $this->ObjectRelations->patchEntity($objectRelation, $data);
        $objectRelation->left_id = 1;
        $objectRelation->relation_id = 1;
        $objectRelation->right_id = 1;

        $errors = $objectRelation->getErrors();
        $errors = Hash::flatten($errors);

        static::assertEquals($expected, array_keys($errors));

        if (!$errors) {
            $success = $this->ObjectRelations->save($objectRelation);
            static::assertTrue((bool)$success);
        }
    }

    /**
     * Data provider for `testJsonSchema` test case.
     *
     * @return array
     */
    public function jsonSchemaProvider()
    {
        $schema = (object)[
            'type' => 'object',
            'properties' => (object)[
                'name' => (object)[
                    'type' => 'string',
                ],
                'age' => (object)[
                    'type' => 'integer',
                    'minimum' => 0,
                ],
            ],
            'additionalProperties' => false,
            'required' => ['name'],
        ];

        return [
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
                'Additional properties not allowed: wtf',
                [
                    'name' => 'Gustavo Supporto',
                    'age' => 42,
                    'wtf' => 'this should not be present',
                ],
                $schema,
            ],
            'invalid value' => [
                'Value more than 0 expected, -42 received',
                [
                    'name' => 'Gustavo Supporto',
                    'age' => -42,
                ],
                $schema,
            ],
            'missing required property' => [
                'Required property missing: name',
                [
                    'age' => 42,
                ],
                $schema,
            ],
            'wrong type' => [
                'String expected, true received',
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
     * @param object $jsonSchema JSON Schema.
     * @return void
     * @dataProvider jsonSchemaProvider()
     * @covers ::jsonSchema()
     */
    public function testJsonSchema($expected, $value, $jsonSchema)
    {
        if (!is_object($jsonSchema)) {
            $jsonSchema = json_decode(json_encode($jsonSchema));
        }

        $result = ObjectRelationsTable::jsonSchema($value, ['providers' => compact('jsonSchema')]);

        if ($expected === true) {
            static::assertTrue($result);
        } else {
            static::assertStringContainsString($expected, $result);
        }
    }
}

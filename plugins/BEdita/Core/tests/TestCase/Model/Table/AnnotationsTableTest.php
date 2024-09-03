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

use BEdita\Core\Utility\LoggedUser;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Table\AnnotationsTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\AnnotationsTable
 */
class AnnotationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\AnnotationsTable
     */
    public $Annotations;

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
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Annotations',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Annotations = TableRegistry::getTableLocator()->get('Annotations');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Annotations);

        parent::tearDown();
    }

    /**
     * Test initialization.
     *
     * @return void
     * @covers ::initialize()
     */
    public function testInitialize()
    {
        static::assertInstanceOf(BelongsTo::class, $this->Annotations->Objects);
        static::assertInstanceOf(BelongsTo::class, $this->Annotations->Users);
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'ok' => [
                [],
                [
                    'object_id' => 2,
                    'description' => 'some text',
                ],
            ],
            'invalid 1' => [
                [
                    'object_id._required',
                    'object_id.integer',
                ],
                [
                    'object_id' => 'definitely not a number',
                ],
            ],
            'invalid 2' => [
                [
                    'object_id._required',
                ],
                [
                    'description' => 'some description',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param string[] $expected Expected errors.
     * @param array $data Data.
     * @return void
     * @dataProvider validationProvider()
     * @coversNothing
     */
    public function testValidation(array $expected, array $data)
    {
        $entity = $this->Annotations->newEntity([]);
        $entity = $this->Annotations->patchEntity($entity, $data);
        $errors = array_keys(Hash::flatten($entity->getErrors()));

        static::assertEquals($expected, $errors);
    }

    /**
     * Data provider for `testBeforeSave` test case.
     *
     * @return array
     */
    public function beforeSaveProvider()
    {
        return [
            'help' => [
                true,
                [
                    'description' => 'Gustavo Supporto Help!',
                    'object_id' => 3,
                ],
            ],
            'user error' => [
                new ForbiddenException('Could not change annotation "1" of user "1"'),
                [
                    'description' => '',
                ],
                1,
            ],
            'object error' => [
                new ForbiddenException('Could not change object id on annotation "2"'),
                [
                    'object_id' => 9,
                ],
                2,
            ],
        ];
    }

    /**
     * Test `beforeSave` method.
     *
     * @param array|\Exception $expected Expected result.
     * @param array $data Save input data.
     * @param int $id Annotation id.
     * @return void
     * @dataProvider beforeSaveProvider
     * @covers ::beforeSave()
     */
    public function testBeforeSave($expected, array $data, $id = null)
    {
        LoggedUser::setUser(['id' => 5]);
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        if ($id) {
            $entity = $this->Annotations->get($id);
        } else {
            $entity = $this->Annotations->newEmptyEntity();
        }
        $entity = $this->Annotations->patchEntity($entity, $data);

        $success = $this->Annotations->save($entity);
        static::assertTrue((bool)$success);
    }

    /**
     * Test `beforeDelete` method.
     *
     * @covers ::beforeDelete()
     */
    public function testBeforeDelete()
    {
        LoggedUser::setUserAdmin();
        $annotation = $this->Annotations->get(1);
        $success = $this->Annotations->delete($annotation);
        static::assertTrue((bool)$success);
    }

    /**
     * Test `beforeDelete` failure.
     *
     * @covers ::beforeDelete()
     */
    public function testBeforeDeleteFailure()
    {
        $this->expectException(\Cake\Http\Exception\ForbiddenException::class);
        $this->expectExceptionMessage('Could not delete annotation "1" of user "1"');
        LoggedUser::setUser(['id' => 5]);
        $annotation = $this->Annotations->get(1);
        $success = $this->Annotations->delete($annotation);
    }
}

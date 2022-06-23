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

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\ProfilesTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\ProfilesTable
 */
class ProfilesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ProfilesTable
     */
    public $Profiles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.History',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Profiles = TableRegistry::getTableLocator()->get('Profiles');
        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Profiles);
        LoggedUser::resetUser();

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     * @coversNothing
     */
    public function testInitialize()
    {
        $this->Profiles->associations()->removeAll();
        $this->Profiles->initialize([]);
        $this->assertEquals('profiles', $this->Profiles->getTable());
        $this->assertEquals('id', $this->Profiles->getPrimaryKey());
        $this->assertEquals('name', $this->Profiles->getDisplayField());
    }

    /**
     * Data provider for `testSave` test case.
     *
     * @return array
     */
    public function saveProvider()
    {
        return [
            'valid' => [
                false,
                [
                    'name' => 'Fake',
                    'surname' => 'User',
                    'email' => 'fake.user@example.com',
                    'person_title' => 'Miss',
                    'gender' => null,
                    'birthdate' => null,
                    'deathdate' => null,
                ],
            ],
            'notUniqueUname' => [
                true,
                [
                    'name' => 'Real',
                    'surname' => 'User',
                    'email' => 'real.user@example.com',
                    'person_title' => 'Mr',
                    'gender' => null,
                    'birthdate' => null,
                    'deathdate' => null,
                    'uname' => 'gustavo-supporto',
                ],
            ],
        ];
    }

    /**
     * Test entity save.
     *
     * @param bool $changed
     * @param array $data
     * @return void
     * @dataProvider saveProvider
     * @coversNothing
     */
    public function testSave(bool $changed, array $data)
    {
        $entity = $this->Profiles->newEntity($data);
        $success = (bool)$this->Profiles->save($entity);

        $this->assertTrue($success);

        if ($changed) {
            $this->assertNotEquals($data['uname'], $entity->uname);
        } elseif (isset($data['uname'])) {
            $this->assertEquals($data['uname'], $entity->uname);
        }
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'valid' => [
                true,
                [
                    'name' => 'Channelweb Srl',
                    'company' => true,
                ],
            ],
            'invalidBirthdate' => [
                false,
                [
                    'name' => 'Gustavo',
                    'surname' => 'Supporto',
                    'birthdate' => 'do not remember',
                    'lang' => 'en',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     * @return void
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $profile = $this->Profiles->newEntity($data);
        $profile->type = 'profiles';

        $error = (bool)$profile->getErrors();
        $this->assertEquals($expected, !$error, print_r($profile->getErrors(), true));

        if ($expected) {
            $success = $this->Profiles->save($profile);
            $this->assertTrue((bool)$success);
        }
    }

    /**
     * Test find method.
     *
     * @return void
     * @coversNothing
     */
    public function testFind()
    {
        $expectedProperties = [
            'id',
            'name',
            'surname',
            'email',
            'person_title',
            'gender',
            'birthdate',
            'deathdate',
            'company',
            'company_name',
            'company_kind',
            'street_address',
            'city',
            'zipcode',
            'country',
            'state_name',
            'phone',
            'website',
            'national_id_number',
            'vat_number',
            'status',
            'uname',
            'locked',
            'created',
            'modified',
            'published',
            'title',
            'description',
            'body',
            'extra',
            'lang',
            'created_by',
            'modified_by',
            'publish_start',
            'publish_end',
            'type',
            // custom prop
            'another_birthdate',
            'another_surname',
            'number_of_friends',
            'pseudonym',
        ];

        sort($expectedProperties);

        $profile = $this->Profiles->find()
            ->where(['object_type_id' => 3])
            ->first();
        $visibleProperties = $profile->getVisible();
        sort($visibleProperties);

        $this->assertEquals($expectedProperties, $visibleProperties);
    }

    /**
     * Test delete.
     *
     * @return void
     */
    public function testDelete()
    {
        $profile = $this->Profiles->find()->first();
        $id = $profile->id;
        $this->assertEquals(true, $this->Profiles->delete($profile));

        $inheritanceTables = $this->Profiles->inheritedTables();
        $inheritanceTables[] = $this->Profiles;

        foreach ($inheritanceTables as $table) {
            try {
                $table->get($id);
                $this->fail($table->getAlias() . ' record not deleted');
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $ex) {
                continue;
            }
        }
    }

    /**
     * Data provider for `testBeforeSave` test case.
     *
     * @return array
     */
    public function beforeSaveProvider()
    {
        return [
            'missing' => [
                [
                    'email' => null,
                ],
                [
                    'name' => 'Gustavo',
                    'surname' => 'Supporto',
                    'email' => '',
                ],
            ],
            'empty title' => [
                [
                    'title' => 'Gustavo Supporto',
                ],
                [
                    'name' => 'Gustavo',
                    'surname' => 'Supporto',
                ],
            ],
            'null title' => [
                [
                    'title' => null,
                ],
                [
                    'name' => 'Gustavo',
                    'title' => null,
                ],
            ],
            'no title' => [
                [
                    'title' => 'Gustavo',
                ],
                [
                    'name' => 'Gustavo',
                ],
            ],
            'title set' => [
                [
                    'title' => 'Dr. Supporto Matteo',
                ],
                [
                    'title' => 'Dr. Supporto Matteo',
                    'name' => 'Matteo',
                    'surname' => 'Supporto',
                ],
            ],
            'surname only' => [
                [
                    'title' => 'Supporto',
                ],
                [
                    'surname' => 'Supporto',
                ],
            ],
            'company only' => [
                [
                    'title' => 'Supporto Inc.',
                ],
                [
                    'company_name' => 'Supporto Inc.',
                ],
            ],
            'existing profile' => [
                [
                    'title' => 'Luciano Supporto',
                ],
                [
                    'id' => 4,
                    'name' => 'Luciano',
                ],
            ],
        ];
    }

    /**
     * Test `beforeSave` method.
     *
     * @param array $expected Expected result.
     * @param array $data Save input data.
     * @return void
     * @dataProvider beforeSaveProvider
     * @covers ::beforeSave()
     * @covers ::titleValue()
     */
    public function testBeforeSave(array $expected, array $data)
    {
        if (empty($data['id'])) {
            $profile = $this->Profiles->newEntity($data);
        } else {
            $profile = $this->Profiles->get($data['id']);
            $profile = $this->Profiles->patchEntity($profile, $data);
        }
        $profile->type = 'profiles';
        $success = $this->Profiles->save($profile);
        static::assertTrue((bool)$success);

        foreach ($expected as $key => $value) {
            static::assertEquals($value, $success->get($key));
        }
    }
}

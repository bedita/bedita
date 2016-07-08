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

use BEdita\Core\Model\Table\ProfilesTable;
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
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Profiles = TableRegistry::get('BEdita/Core.Profiles');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Profiles);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->Profiles->removeBehavior('ClassTableInheritance');
        $this->Profiles->initialize([]);
        $this->assertEquals('profiles', $this->Profiles->table());
        $this->assertEquals('id', $this->Profiles->primaryKey());
        $this->assertEquals('name', $this->Profiles->displayField());

        $this->assertInstanceOf('\BEdita\Core\ORM\Association\ExtensionOf', $this->Profiles->Objects);
        $this->assertInstanceOf('\Cake\ORM\Association\BelongsTo', $this->Profiles->Users);
        $this->assertInstanceOf(
            '\BEdita\Core\Model\Behavior\ClassTableInheritanceBehavior',
            $this->Profiles->Behaviors()->get('ClassTableInheritance')
        );
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
            'notUniqueEmail' => [
                false,
                [
                    'email' => 'gustavo.supporto@channelweb.it',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     *
     * @return void
     * @dataProvider validationProvider
     * @covers ::validationDefault
     * @covers ::buildRules
     */
    public function testValidation($expected, array $data)
    {
        // @todo: remove and put as $data fields when CTI save is completed
        $data['object'] = [
            'object_type_id' => 2,
            'uname' => 'object-associated-' . md5(implode('|', $data)),
            'status' => 'draft',
            'lang' => 'eng',
            'created_by' => 1,
            'modified_by' => 1,
        ];

        // @todo: remove `associated` when CTI save is complete
        $profile = $this->Profiles->newEntity($data, [
            'associated' => [
                'Objects' => [
                    'accessibleFields' => ['*' => true]
                ]
            ]
        ]);

        $error = (bool)$profile->errors();
        $this->assertEquals($expected, !$error, print_r($profile->errors(), true));

        if ($expected) {
            $success = $this->Profiles->save($profile);
            $this->assertTrue((bool)$success);
        }
    }

    public function testFind()
    {
        $expectedProperites = [
            'id',
            'user_id',
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
            'object_type_id',
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
            'publish_end'
        ];

        sort($expectedProperites);

        $profile = $this->Profiles->find()
            ->where(['object_type_id' => 2])
            ->first();
        $visibleProperties = $profile->visibleProperties();
        sort($visibleProperties);

        $this->assertEquals($expectedProperites, $visibleProperties);
    }
}

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

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\LocationsTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\LocationsTable
 */
class LocationsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\LocationsTable
     */
    public $Locations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.History',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Locations = TableRegistry::getTableLocator()->get('Locations');
        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Locations);
        LoggedUser::resetUser();

        parent::tearDown();
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
                    'coords' => 'POINT(11.3441359 44.4959174)',
                    'address' => 'Piazza del Nettuno',
                    'locality' => 'Bologna',
                    'postal_code' => '40126',
                    'country_name' => 'Italy',
                    'region' => 'Emilia-romagna',
                ],
            ],
            'notUniqueUname' => [
                true,
                [
                    'coords' => 'POINT(11.3464055 44.4944183)',
                    'address' => 'Piazza di Porta Ravegnana',
                    'locality' => 'Bologna',
                    'postal_code' => '40126',
                    'country_name' => 'Italy',
                    'region' => 'Emilia-romagna',
                    'uname' => 'the-two-towers',
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
        $entity = $this->Locations->newEntity($data);
        $success = (bool)$this->Locations->save($entity);

        $this->assertTrue($success);

        if ($changed) {
            $this->assertNotEquals($data['uname'], $entity->uname);
        } elseif (isset($data['uname'])) {
            $this->assertEquals($data['uname'], $entity->uname);
        }
    }
}

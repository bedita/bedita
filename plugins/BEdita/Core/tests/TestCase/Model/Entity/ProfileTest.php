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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\Profile;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Profile} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Profile
 */
class ProfileTest extends TestCase
{

    /**
     * Test subject's table
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
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Profiles = TableRegistry::get('BEdita/Core.Profiles');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Profiles);

        TableRegistry::clear();

        parent::tearDown();
    }

    /**
     * Test accessible properties.
     *
     * @return void
     * @coversNothing
     */
    public function testAccessible()
    {
        $profile = $this->Profiles->get(4);

        $data = [
            'id' => 42,
            'name' => 'Gust'
        ];
        $profile = $this->Profiles->patchEntity($profile, $data);
        if (!($profile instanceof Profile)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(4, $profile->id);
    }
}

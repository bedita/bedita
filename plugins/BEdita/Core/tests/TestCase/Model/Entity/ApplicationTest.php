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

use BEdita\Core\Model\Entity\Application;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Application} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Application
 */
class ApplicationTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\ApplicationsTable
     */
    public $Applications;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.applications',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Applications = TableRegistry::get('Applications');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Applications);

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
        $application = $this->Applications->get(1);

        $created = $application->created;
        $modified = $application->modified;
        $apiKey = $application->api_key;

        $data = [
            'id' => 42,
            'api_key' => '123abc',
            'created' => '2016-01-01 12:00:00',
            'modified' => '2016-01-01 12:00:00',
        ];
        $application = $this->Applications->patchEntity($application, $data);
        if (!($application instanceof Application)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(1, $application->id);
        $this->assertEquals($apiKey, $application->api_key);
        $this->assertEquals($created, $application->created);
        $this->assertEquals($modified, $application->modified);
    }
}

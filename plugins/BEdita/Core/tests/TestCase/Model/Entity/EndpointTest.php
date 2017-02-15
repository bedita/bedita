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

use BEdita\Core\Model\Entity\Endpoint;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Endpoint} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Endpoint
 */
class EndpointTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\EndpointsTable
     */
    public $Endpoints;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.endpoints',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Endpoints = TableRegistry::get('Endpoints');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Endpoints);

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
        $endpoint = $this->Endpoints->get(1);

        $created = $endpoint->created;
        $modified = $endpoint->modified;

        $data = [
            'id' => 42,
            'created' => '2016-01-01 12:00:00',
            'modified' => '2016-01-01 12:00:00',
        ];
        $endpoint = $this->Endpoints->patchEntity($endpoint, $data);
        if (!($endpoint instanceof Endpoint)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(1, $endpoint->id);
        $this->assertEquals($created, $endpoint->created);
        $this->assertEquals($modified, $endpoint->modified);
    }
}

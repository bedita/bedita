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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Utility\JsonApiSerializable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 *  {@see \BEdita\Core\Model\Entity\JsonApiAdminTrait} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\JsonApiAdminTrait
 */
class JsonApiAdminTraitTest extends TestCase
{

    /**
     * Helper table.
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
     * Test getter for meta.
     *
     * @return void
     *
     * @covers ::getLinks()
     */
    public function testGetLinks()
    {
        $expected = [
            'self' => '/admin/applications/1',
        ];

        $application = $this->Applications->get(1)->jsonApiSerialize();

        $links = $application['links'];

        static::assertEquals($expected, $links);
    }
}

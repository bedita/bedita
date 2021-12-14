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

use BEdita\Core\Model\Entity\ExternalAuth;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\ExternalAuth} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\ExternalAuth
 */
class ExternalAuthTest extends TestCase
{
    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\AuthProvidersTable
     */
    public $ExternalAuth;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->ExternalAuth = TableRegistry::getTableLocator()->get('ExternalAuth');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->ExternalAuth);

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
        $externalAuth = $this->ExternalAuth->get(1);

        $data = [
            'id' => 42,
            'provider_username' => 'patched_username',
        ];
        $externalAuth = $this->ExternalAuth->patchEntity($externalAuth, $data);
        if (!($externalAuth instanceof ExternalAuth)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(1, $externalAuth->id);
        $this->assertEquals('patched_username', $externalAuth->provider_username);
    }
}

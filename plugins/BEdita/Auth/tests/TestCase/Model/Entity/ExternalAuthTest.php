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

namespace BEdita\Auth\Test\TestCase\Model\Entity;

use BEdita\Auth\Model\Entity\ExternalAuth;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Auth\Model\Entity\ExternalAuth} Test Case
 *
 * @coversDefaultClass \BEdita\Auth\Model\Entity\ExternalAuth
 */
class ExternalAuthTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Auth\Model\Table\AuthProvidersTable
     */
    public $ExternalAuth;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Auth.users',
        'plugin.BEdita/Auth.auth_providers',
        'plugin.BEdita/Auth.external_auth',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->ExternalAuth = TableRegistry::get(
            'ExternalAuth',
            TableRegistry::exists('ExternalAuth') ? [] : ['className' => 'BEdita\Auth\Model\Table\ExternalAuthTable']
        );
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->ExternalAuth);

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
        $externalAuth = $this->ExternalAuth->get(1);

        $data = [
            'id' => 42,
            'username' => 'patched_username',
        ];
        $externalAuth = $this->ExternalAuth->patchEntity($externalAuth, $data);
        if (!($externalAuth instanceof ExternalAuth)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(1, $externalAuth->id);
        $this->assertEquals('patched_username', $externalAuth->username);
    }
}

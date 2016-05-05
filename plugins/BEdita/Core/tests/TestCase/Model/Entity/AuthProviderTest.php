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

use BEdita\Core\Model\Entity\AuthProvider;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\AuthProvider} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\AuthProvider
 */
class AuthProviderTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\AuthProvidersTable
     */
    public $AuthProviders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.auth_providers',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->AuthProviders = TableRegistry::get('BEdita/Core.AuthProviders');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->AuthProviders);

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
        $authProvider = $this->AuthProviders->get(1);

        $data = [
            'id' => 42,
            'name' => 'patched_name',
        ];
        $authProvider = $this->AuthProviders->patchEntity($authProvider, $data);
        if (!($authProvider instanceof AuthProvider)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(1, $authProvider->id);
        $this->assertEquals('patched_name', $authProvider->name);
    }
}

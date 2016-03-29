<?php

namespace BEdita\Auth\Test\TestCase\Model\Entity;

use BEdita\Auth\Model\Entity\AuthProvider;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Auth\Model\Entity\AuthProvider} Test Case
 *
 * @coversDefaultClass \BEdita\Auth\Model\Entity\AuthProvider
 */
class AuthProviderTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Auth\Model\Table\AuthProvidersTable
     */
    public $AuthProviders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Auth.auth_providers',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->AuthProviders = TableRegistry::get(
            'AuthProviders',
            TableRegistry::exists('AuthProviders') ? [] : ['className' => 'BEdita\Auth\Model\Table\AuthProvidersTable']
        );
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

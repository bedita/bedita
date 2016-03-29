<?php
namespace BEdita\Auth\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Auth\Model\Table\AuthProvidersTable} Test Case
 *
 * @coversDefaultClass \BEdita\Auth\Model\Table\AuthProvidersTable
 */
class AuthProvidersTableTest extends TestCase
{

    /**
     * Test subject
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
     * Test initialization.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::_initializeSchema()
     */
    public function testInitialization()
    {
        $this->AuthProviders->initialize([]);
        $schema = $this->AuthProviders->schema();

        $this->assertEquals('auth_providers', $this->AuthProviders->table());
        $this->assertEquals('id', $this->AuthProviders->primaryKey());
        $this->assertEquals('name', $this->AuthProviders->displayField());

        $this->assertInstanceOf('\Cake\ORM\Association\HasMany', $this->AuthProviders->ExternalAuth);

        $this->assertEquals('json', $schema->columnType('params'));
    }

    /**
     * Data provider for `testValidationDefault` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'valid' => [
                true,
                [
                    'name' => 'some_unique_value',
                    'url' => 'https://example.com/oauth2',
                    'params' => [],
                ],
            ],
            'notUnique' => [
                false,
                [
                    'name' => 'example',
                    'url' => 'https://example.com/oauth2',
                    'params' => [
                        'someParam' => 'someValue',
                    ],
                ],
            ],
            'invalidUrl' => [
                false,
                [
                    'name' => 'some_unique_value',
                    'url' => 'this is not a URL',
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
        $authProvider = $this->AuthProviders->newEntity();
        $this->AuthProviders->patchEntity($authProvider, $data);

        $error = (bool)$authProvider->errors();
        $this->assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->AuthProviders->save($authProvider);
            $this->assertEquals($expected, (bool)$success);
        }
    }
}

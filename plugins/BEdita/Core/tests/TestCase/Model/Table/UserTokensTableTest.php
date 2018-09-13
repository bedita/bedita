<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\UserTokensTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Table\UserTokensTable} Test Case
 * @covers BEdita\Core\Model\Table\UserTokensTable
 */
class UserTokensTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\UserTokensTable
     */
    public $UserTokens;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.user_tokens',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.applications'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->UserTokens = TableRegistry::get('UserTokens');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->UserTokens);

        parent::tearDown();
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'ok' => [
                [],
                [
                    'user_id' => 1,
                    'client_token' => 'token token token',
                    'token_type' => 'refresh',
                ],
            ],
            'invalid 1' => [
                [
                    'client_token._required',
                    'token_type._required',
                ],
                [
                    'application_id' => 2,
                    'secret_token' => 'super secret token',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param string[] $expected Expected errors.
     * @param array $data Data.
     * @return void
     *
     * @dataProvider validationProvider()
     */
    public function testValidation(array $expected, array $data)
    {
        $entity = $this->UserTokens->newEntity();
        $entity = $this->UserTokens->patchEntity($entity, $data);
        $errors = array_keys(Hash::flatten($entity->getErrors()));

        static::assertEquals($expected, $errors);
    }

    /**
     * Test 'valid' finder.
     *
     * @return void
     */
    public function testValidFinder()
    {
        $entity = $this->UserTokens->find('valid')->first();

        static::assertNotEmpty($entity);
        static::assertEquals(1, $entity->get('id'));
    }
}

<?php

namespace BEdita\Auth\Test\TestCase\Auth;

use BEdita\Auth\Auth\LegacyMd5PasswordHasher;
use Cake\TestSuite\TestCase;

/**
 * \BEdita\Auth\Auth\LegacyMd5PasswordHasher Test Case
 */
class LegacyMd5PasswordHasherTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Auth\Auth\LegacyMd5PasswordHasher
     */
    public $Md5PasswordHasher;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Md5PasswordHasher = new LegacyMd5PasswordHasher();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Md5PasswordHasher);

        parent::tearDown();
    }

    /**
     * Test hash method
     *
     * @return void
     */
    public function testHash()
    {
        $hashed = $this->Md5PasswordHasher->hash('BEdita');
        $this->assertEquals('41f7badfa34c04a74c39f94c3bad8f3a', $hashed);
    }

    /**
     * Data provider for `testCheck` test case.
     *
     * @return array
     */
    public function checkProvider()
    {
        return [
            'success' => [true, 'BEdita', '41f7badfa34c04a74c39f94c3bad8f3a'],
            'failure' => [false, 'WrongPassword', '41f7badfa34c04a74c39f94c3bad8f3a'],
        ];
    }

    /**
     * Test check method
     *
     * @param bool $expected Expected result.
     * @param string $password Password to be checked.
     * @param string $hashedPassword Hashed password.
     *
     * @return void
     *
     * @dataProvider checkProvider
     */
    public function testCheck($expected, $password, $hashedPassword)
    {
        $check = $this->Md5PasswordHasher->check($password, $hashedPassword);
        $this->assertEquals($expected, $check);
    }
}

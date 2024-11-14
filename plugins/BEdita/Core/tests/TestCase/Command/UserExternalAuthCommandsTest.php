<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Channelweb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Command\UserExternalAuthAddCommand;
use BEdita\Core\Command\UserExternalAuthListCommand;
use BEdita\Core\Command\UserExternalAuthRemoveCommand;
use BEdita\Core\Model\Entity\ExternalAuth;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\TestSuite\TestCase;

/**
 * Test cases for {@see \BEdita\Core\Command\UserExternalAuthListCommand}, {@see \BEdita\Core\Command\UserExternalAuthAddCommand} and {@see \BEdita\Core\Command\UserExternalAuthRemoveCommand}.
 */
class UserExternalAuthCommandsTest extends TestCase
{
    use ConsoleIntegrationTestTrait;
    use LocatorAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
    ];

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->cleanupConsoleTrait();
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthListCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthListCommand
     */
    public function testList(): void
    {
        /** @var \BEdita\Core\Model\Entity\ExternalAuth[] $externalAuth */
        $externalAuth = $this->fetchTable('ExternalAuth')
            ->find()
            ->contain(['AuthProviders', 'Users'])
            ->all();

        $this->exec(UserExternalAuthListCommand::defaultName());
        $this->assertExitSuccess();
        foreach ($externalAuth as $auth) {
            $this->assertOutputContains($auth->auth_provider->name);
            $this->assertOutputContains($auth->user->username);
            foreach (['id', 'provider_username', 'params'] as $field) {
                $this->assertOutputContains(substr((string)($auth->{$field} ?? '<empty>'), 0, 24));
            }
        }
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthListCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthListCommand
     */
    public function testListById(): void
    {
        /** @var \BEdita\Core\Model\Entity\ExternalAuth[] $externalAuth */
        $externalAuth = $this->fetchTable('ExternalAuth')
            ->find()
            ->where(['user_id' => 5])
            ->contain(['AuthProviders', 'Users'])
            ->all();

        $this->exec(sprintf('%s --bedita-id 5', UserExternalAuthListCommand::defaultName()));
        $this->assertExitSuccess();
        foreach ($externalAuth as $auth) {
            $this->assertOutputContains($auth->auth_provider->name);
            $this->assertOutputContains($auth->user->username);
            foreach (['id', 'provider_username', 'params'] as $field) {
                $this->assertOutputContains(substr((string)($auth->{$field} ?? '<empty>'), 0, 24));
            }
        }
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthListCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthListCommand
     */
    public function testListByUsername(): void
    {
        /** @var \BEdita\Core\Model\Entity\ExternalAuth[] $externalAuth */
        $externalAuth = $this->fetchTable('ExternalAuth')
            ->find()
            ->where(['user_id' => 5])
            ->contain(['AuthProviders', 'Users'])
            ->all();

        $this->exec(sprintf('%s --bedita-username "second user"', UserExternalAuthListCommand::defaultName()));
        $this->assertExitSuccess();
        foreach ($externalAuth as $auth) {
            $this->assertOutputContains($auth->auth_provider->name);
            $this->assertOutputContains($auth->user->username);
            foreach (['id', 'provider_username', 'params'] as $field) {
                $this->assertOutputContains(substr((string)($auth->{$field} ?? '<empty>'), 0, 24));
            }
        }
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthListCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthListCommand
     */
    public function testListByProvider(): void
    {
        /** @var \BEdita\Core\Model\Entity\ExternalAuth[] $externalAuth */
        $externalAuth = $this->fetchTable('ExternalAuth')
            ->find()
            ->where(['user_id' => 5])
            ->contain(['AuthProviders', 'Users'])
            ->all();

        $this->exec(sprintf('%s --bedita-id 5 --provider linkedout', UserExternalAuthListCommand::defaultName()));
        $this->assertExitSuccess();
        foreach ($externalAuth as $auth) {
            if ($auth->auth_provider->name !== 'linkedout') {
                $this->assertOutputNotContains($auth->auth_provider->name);
                foreach (['id', 'provider_username', 'params'] as $field) {
                    if (!empty($auth->{$field})) {
                        $this->assertOutputNotContains(substr((string)$auth->{$field}, 0, 24));
                    }
                }

                continue;
            }

            $this->assertOutputContains($auth->auth_provider->name);
            $this->assertOutputContains($auth->user->username);
            foreach (['id', 'provider_username', 'params'] as $field) {
                $this->assertOutputContains(substr((string)($auth->{$field} ?? '<empty>'), 0, 24));
            }
        }
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthListCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthListCommand
     */
    public function testListUserNotFound(): void
    {
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Record not found');
        $this->exec(sprintf('%s --bedita-id 5555 --provider linkedout', UserExternalAuthListCommand::defaultName()));
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthAddCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthAddCommand
     */
    public function testAdd()
    {
        $this->exec(sprintf('%s --bedita-id 5 --provider otp --provider-username erik', UserExternalAuthAddCommand::defaultName()));

        $this->assertExitSuccess();
        $this->assertOutputContains('Added user "second user" external auth for provider "otp"');

        /** @var \BEdita\Core\Model\Entity\ExternalAuth|null $auth */
        $auth = $this->fetchTable('ExternalAuth')
            ->find('authProvider', ['auth_provider' => 'otp'])
            ->where(['user_id' => 5])
            ->contain(['AuthProviders'])
            ->first();

        static::assertNotNull($auth);
        static::assertEquals('otp', $auth->auth_provider->name);
        static::assertEquals('erik', $auth->provider_username);
        static::assertNull($auth->params);
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthAddCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthAddCommand
     */
    public function testAddParams()
    {
        $this->exec(sprintf("%s --bedita-id 5 --provider otp --provider-username erik --provider-params '{\"test\":\"params are set\"}'", UserExternalAuthAddCommand::defaultName()));

        $this->assertExitSuccess();
        $this->assertOutputContains('Added user "second user" external auth for provider "otp"');

        /** @var \BEdita\Core\Model\Entity\ExternalAuth|null $auth */
        $auth = $this->fetchTable('ExternalAuth')
            ->find('authProvider', ['auth_provider' => 'otp'])
            ->where(['user_id' => 5])
            ->contain(['AuthProviders'])
            ->first();

        static::assertNotNull($auth);
        static::assertEquals('otp', $auth->auth_provider->name);
        static::assertEquals('erik', $auth->provider_username);
        static::assertEquals(['test' => 'params are set'], $auth->params);
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthAddCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthAddCommand
     */
    public function testAddNoUser(): void
    {
        $this->exec(sprintf('%s --provider otp --provider-username erik', UserExternalAuthAddCommand::defaultName()));

        $this->assertExitError();
        $this->assertErrorContains('One option between "--bedita-id" or "--bedita-username" must be provided');
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthAddCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthAddCommand
     */
    public function testAddDuplicateProvider()
    {
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('user_id._isUnique: "This value is already in use"');
        $this->exec(sprintf('%s --bedita-id 5 --provider uuid --provider-username erik', UserExternalAuthAddCommand::defaultName()));
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthAddCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthAddCommand
     */
    public function testAddProviderNotFound()
    {
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Record not found');
        $this->exec(sprintf('%s --bedita-id 5 --provider qwerty --provider-username erik', UserExternalAuthAddCommand::defaultName()));
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthAddCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthAddCommand
     */
    public function testAddNoProvider()
    {
        $this->exec(sprintf('%s --bedita-id 5 --provider-username erik', UserExternalAuthAddCommand::defaultName()));

        $this->assertExitError();
        $this->assertErrorContains('Option "--provider" is required.');
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthAddCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthAddCommand
     */
    public function testAddNoProviderUsername()
    {
        $this->exec(sprintf('%s --bedita-id 5 --provider otp', UserExternalAuthAddCommand::defaultName()));

        $this->assertExitError();
        $this->assertErrorContains('Option "--provider-username" is required.');
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthRemoveCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthRemoveCommand
     */
    public function testRemoveById()
    {
        /** @var \BEdita\Core\Model\Entity\ExternalAuth[] $before */
        $before = $this->fetchTable('ExternalAuth')
            ->find()
            ->where(['user_id' => 5])
            ->contain(['AuthProviders'])
            ->all()
            ->toArray();
        $this->exec(sprintf('%s --bedita-id 5 --external-id 2', UserExternalAuthRemoveCommand::defaultName()));

        $this->assertExitSuccess();
        $this->assertOutputContains('Removed user "second user" external auth for provider "uuid"');

        /** @var \BEdita\Core\Model\Entity\ExternalAuth[] $after */
        $after = $this->fetchTable('ExternalAuth')
            ->find()
            ->where(['user_id' => 5])
            ->contain(['AuthProviders'])
            ->all()
            ->toArray();

        static::assertLessThan(count($before), count($after));
        static::assertNotContains('uuid', array_map(fn (ExternalAuth $auth): string => $auth->auth_provider->name, $after));
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthRemoveCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthRemoveCommand
     */
    public function testRemoveByProvider()
    {
        /** @var \BEdita\Core\Model\Entity\ExternalAuth[] $before */
        $before = $this->fetchTable('ExternalAuth')
            ->find()
            ->where(['user_id' => 5])
            ->contain(['AuthProviders'])
            ->all()
            ->toArray();
        $this->exec(sprintf('%s --bedita-id 5 --provider uuid', UserExternalAuthRemoveCommand::defaultName()));

        $this->assertExitSuccess();
        $this->assertOutputContains('Removed user "second user" external auth for provider "uuid"');

        /** @var \BEdita\Core\Model\Entity\ExternalAuth[] $after */
        $after = $this->fetchTable('ExternalAuth')
            ->find()
            ->where(['user_id' => 5])
            ->contain(['AuthProviders'])
            ->all()
            ->toArray();

        static::assertLessThan(count($before), count($after));
        static::assertNotContains('uuid', array_map(fn (ExternalAuth $auth): string => $auth->auth_provider->name, $after));
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthRemoveCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthRemoveCommand
     */
    public function testRemoveNoUser(): void
    {
        $this->exec(UserExternalAuthRemoveCommand::defaultName());

        $this->assertExitError();
        $this->assertErrorContains('One option between "--bedita-id" or "--bedita-username" must be provided');
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthRemoveCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthRemoveCommand
     */
    public function testRemoveNoOptions()
    {
        $this->exec(sprintf('%s --bedita-id 5', UserExternalAuthRemoveCommand::defaultName()));

        $this->assertExitError();
        $this->assertErrorContains('One option between "--external-id" or "--provider" must be provided.');
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthRemoveCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthRemoveCommand
     */
    public function testRemoveNotFoundByProvider()
    {
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Record not found');
        $this->exec(sprintf('%s --bedita-id 5 --provider qwerty', UserExternalAuthRemoveCommand::defaultName()));
    }

    /**
     * Test case for {@see \BEdita\Core\Command\UserExternalAuthRemoveCommand} command.
     *
     * @return void
     * @covers \BEdita\Core\Command\UserExternalAuthRemoveCommand
     */
    public function testRemoveNotFoundById()
    {
        $this->exec(sprintf('%s --bedita-id 5 --external-id 5555', UserExternalAuthRemoveCommand::defaultName()));

        $this->assertExitError();
        $this->assertErrorContains('External auth record not found.');
    }
}
